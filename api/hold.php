<?php
// api/hold.php (Yaw) — THE HEART OF THE SYSTEM. The last bed can only ever go to one ambulance.
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/expiry.php';
require_once __DIR__ . '/../classes/Matcher.php';
require_once __DIR__ . '/../classes/Transfer.php';

requireRole(['ambulance_crew', 'doctor', 'midwife', 'coordinator']);
checkCsrf();
runExpiry($pdo);                                 // a hold that just ran out frees its bed first

// ---- 1. Read and check the input ----
$in         = jsonInput();
$hospitalId = (int)($in['hospital_id'] ?? 0);
$wardType   = $in['ward_type'] ?? '';
$bloodType  = $in['blood_type'] ?? null;
$bloodUnits = (int)($in['blood_units'] ?? 0);
$replaces   = (int)($in['replaces_transfer_id'] ?? 0);          // set when rerouting after a Divert (Should)
$lat = (float)($in['lat'] ?? 0); $lng = (float)($in['lng'] ?? 0); // where the crew is, for "next best"
$userId     = (int)$_SESSION['user_id'];

if (!in_array($wardType, WARD_TYPES, true)) fail(422, 'Choose a ward');
if ($bloodUnits < 0 || $bloodUnits > 10) fail(422, 'Blood units must be 0 to 10');
if ($bloodUnits > 0 && !in_array($bloodType, BLOOD_TYPES, true)) fail(422, 'Choose a blood type');
if ($bloodUnits === 0) $bloodType = null;

// ---- 2. One active hold per crew, so nobody can hoard beds ----
$busy = $pdo->prepare("SELECT COUNT(*) FROM transfers
                       WHERE from_user_id = ? AND stage IN ('pending','accepted','in_transit') AND transfer_id <> ?");
$busy->execute([$userId, $replaces]);
if ($busy->fetchColumn() > 0) fail(409, 'You already have an active hold', ['code' => 'ALREADY_HOLDING']);

$nextBest = function () use ($pdo, $lat, $lng, $wardType, $hospitalId): ?array {
    if (!inGhana($lat, $lng)) return null;
    return Matcher::best($pdo, ['lat' => $lat, 'lng' => $lng, 'ward' => $wardType,
                                'radius' => 25, 'exclude' => $hospitalId])[0] ?? null;
};

// ---- 3. The transaction ----
$result = withRetry($pdo, function () use ($pdo, $hospitalId, $wardType, $bloodType, $bloodUnits, $userId, $replaces, $nextBest) {
    $pdo->beginTransaction();

    // 3a. Lock ONE free bed. A second request for the same bed WAITS here until we commit,
    //     then sees status = 'held' and finds no row.
    $bed = $pdo->prepare("
        SELECT b.bed_id, b.label FROM beds b
        JOIN wards w ON w.ward_id = b.ward_id AND w.is_active = 1
        JOIN ward_types wt ON wt.ward_type_id = w.ward_type_id
        JOIN hospitals h ON h.hospital_id = w.hospital_id
        WHERE h.hospital_id = ? AND wt.code = ? AND b.status = 'free'
          AND b.is_surge = 0 AND b.is_active = 1 AND h.is_diverting = 0 AND h.is_approved = 1
        ORDER BY b.bed_id LIMIT 1 FOR UPDATE");
    $bed->execute([$hospitalId, $wardType]);
    $row = $bed->fetch();
    if (!$row) {
        $pdo->rollBack();
        fail(409, 'Bed was just taken', ['code' => 'BED_TAKEN', 'next_best' => $nextBest()]);
    }

    // 3b. Blood, if asked for: lock that stock row (ALWAYS after the bed, so two requests can't deadlock).
    if ($bloodUnits > 0) {
        $st = $pdo->prepare("SELECT units, reserve_units FROM blood_stock
                             WHERE hospital_id = ? AND blood_type = ? FOR UPDATE");
        $st->execute([$hospitalId, $bloodType]);
        $stock = $st->fetch();
        if (!$stock || $stock['units'] < $stock['reserve_units'] + $bloodUnits) {
            $pdo->rollBack();
            fail(409, "Not enough $bloodType blood", ['code' => 'NO_BLOOD', 'next_best' => $nextBest()]);
        }
        $pdo->prepare("UPDATE blood_stock SET units = units - ?, updated_by = ? WHERE hospital_id = ? AND blood_type = ?")
            ->execute([$bloodUnits, $userId, $hospitalId, $bloodType]);
    }

    // 3c. Mark the bed held and start the 30-minute clock.
    $pdo->prepare("UPDATE beds SET status = 'held', updated_by = ? WHERE bed_id = ?")->execute([$userId, $row['bed_id']]);
    $pdo->prepare("INSERT INTO bed_updates (bed_id, old_status, new_status, source, updated_by) VALUES (?, 'free', 'held', 'hold', ?)")
        ->execute([$row['bed_id'], $userId]);
    $pdo->prepare("INSERT INTO transfers (type, from_user_id, to_hospital_id, bed_id, blood_type, blood_units, stage, hold_until)
                   VALUES ('ambulance', ?, ?, ?, ?, ?, 'pending', NOW() + INTERVAL 30 MINUTE)")
        ->execute([$userId, $hospitalId, $row['bed_id'], $bloodType, $bloodUnits]);
    $transferId = (int)$pdo->lastInsertId();

    // 3d. Reroute (Should): give back the old hold in the SAME transaction.
    if ($replaces) {
        $old = $pdo->prepare("SELECT * FROM transfers WHERE transfer_id = ? AND from_user_id = ? FOR UPDATE");
        $old->execute([$replaces, $userId]);
        if ($t = $old->fetch()) Transfer::release($pdo, $t, 'cancelled', 'diverted');
    }

    notify($pdo, 'hospital', $hospitalId, 'incoming', $transferId);
    logAction($pdo, 'hold', 'transfers', $transferId, null, 'pending');

    $h = $pdo->prepare("SELECT hold_until FROM transfers WHERE transfer_id = ?");
    $h->execute([$transferId]);
    $holdUntil = $h->fetchColumn();

    $pdo->commit();                              // all of it is saved together, or none of it
    return ['transfer_id' => $transferId, 'bed_label' => $row['label'], 'hold_until' => $holdUntil,
            'blood_units_held' => $bloodUnits];
});

$_SESSION['on_trip'] = true;                     // Hariel's 15-minute idle logout skips crews on a trip
respond(201, $result);
