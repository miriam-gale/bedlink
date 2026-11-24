<?php
// api/handover.php (Yaw) — A: crew sends triage/vitals/treatment. B: staff open the card (seen) or tap "I've got it" (claim).
// The tick is never stored: it is worked out from seen_at and ready_at, so it can only move forward.
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

checkCsrf();
$in = jsonInput();

// ---------- A. Crew creates the handover ----------
if (isset($in['transfer_id'])) {
    requireRole(['ambulance_crew', 'doctor', 'midwife']);
    $t = $pdo->prepare("SELECT transfer_id, to_hospital_id FROM transfers
                        WHERE transfer_id = ? AND from_user_id = ? AND stage IN ('pending','accepted','in_transit')");
    $t->execute([(int)$in['transfer_id'], $_SESSION['user_id']]);
    $trip = $t->fetch();
    if (!$trip) fail(404, 'Trip not found');

    if (!in_array($in['triage'] ?? '', ['red', 'yellow', 'green'], true)) fail(422, 'Choose a triage colour');

    // Vitals: each one is a number in a sensible range, or null = "not measured"
    $limits = ['pulse' => [20, 250], 'bp_sys' => [40, 300], 'bp_dia' => [20, 200],
               'spo2' => [40, 100], 'resp' => [4, 80], 'gcs' => [3, 15]];
    $vitals = [];
    foreach ($limits as $k => [$min, $max]) {
        $v = $in['vitals'][$k] ?? null;
        if ($v !== null && (!is_numeric($v) || $v < $min || $v > $max)) fail(422, "Check $k", ['fields' => [$k => "$min–$max"]]);
        $vitals[$k] = $v === null ? null : (int)$v;
    }
    $treatment = mb_substr(trim($in['treatment_given'] ?? ''), 0, 255);
    if ($treatment === '') fail(422, 'Choose at least one treatment (or "None")');
    $details = mb_substr(trim($in['details'] ?? ''), 0, 2000) ?: null;   // clinical only: no names (golden rule 4)

    try {
        $pdo->prepare("INSERT INTO handovers (transfer_id, triage, vitals, treatment_given, details) VALUES (?, ?, ?, ?, ?)")
            ->execute([$trip['transfer_id'], $in['triage'], json_encode($vitals), $treatment, $details]);
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? 0) == 1062) fail(409, 'Handover already sent', ['code' => 'ALREADY_SENT']);   // UNIQUE transfer_id
        throw $e;
    }
    $hid = (int)$pdo->lastInsertId();
    notify($pdo, 'hospital', (int)$trip['to_hospital_id'], 'handover', (int)$trip['transfer_id']);
    respond(201, ['handover_id' => $hid, 'tick' => 'sent']);
}

// ---------- B. Staff open the card (seen) or tap "I've got it" (claim) ----------
requireRole(['ward_nurse', 'doctor']);
$hid    = (int)($in['handover_id'] ?? 0);
$action = $in['action'] ?? '';

// Scope: the handover must be for a trip coming to MY hospital
$own = $pdo->prepare("SELECT ho.handover_id FROM handovers ho JOIN transfers t ON t.transfer_id = ho.transfer_id
                      WHERE ho.handover_id = ? AND t.to_hospital_id = ?");
$own->execute([$hid, $_SESSION['hospital_id']]);
if (!$own->fetch()) fail(404, 'Handover not found');

if ($action === 'seen') {
    $pdo->prepare("UPDATE handovers SET seen_at = NOW(), seen_by = ? WHERE handover_id = ? AND seen_at IS NULL")
        ->execute([$_SESSION['user_id'], $hid]);
} elseif ($action === 'claim') {
    // Change only if still true: if two people tap at once, only one UPDATE matches
    $c = $pdo->prepare("UPDATE handovers SET claimed_by = ?, ready_at = NOW(), seen_at = COALESCE(seen_at, NOW()),
                        seen_by = COALESCE(seen_by, ?)
                        WHERE handover_id = ? AND claimed_by IS NULL");
    $c->execute([$_SESSION['user_id'], $_SESSION['user_id'], $hid]);
    if ($c->rowCount() === 0) {
        $by = $pdo->prepare("SELECT u.full_name FROM handovers ho JOIN users u ON u.user_id = ho.claimed_by WHERE ho.handover_id = ?");
        $by->execute([$hid]);
        fail(409, 'Already claimed by ' . $by->fetchColumn(), ['code' => 'ALREADY_CLAIMED']);
    }
    logAction($pdo, 'handover_claim', 'handovers', $hid, null, (string)$_SESSION['user_id']);
} else {
    fail(422, 'Unknown action');
}

$r = $pdo->prepare("SELECT seen_at, ready_at FROM handovers WHERE handover_id = ?");
$r->execute([$hid]);
$h = $r->fetch();
respond(200, ['handover_id' => $hid, 'tick' => $h['ready_at'] ? 'team_ready' : ($h['seen_at'] ? 'seen' : 'sent')]);
