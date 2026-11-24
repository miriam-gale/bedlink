<?php
// api/transfer.php (Yaw) — moves a trip through its stages
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/state.php';
require_once __DIR__ . '/../classes/Transfer.php';

checkCsrf();
$in     = jsonInput();
$id     = (int)($in['transfer_id'] ?? 0);
$action = $in['action'] ?? '';
$reason = trim($in['reason'] ?? '');

$to = ['accept' => 'accepted', 'reject' => 'rejected', 'in_transit' => 'in_transit',
       'arrived' => 'arrived', 'cancel' => 'cancelled'][$action] ?? null;
if (!$to) fail(422, 'Unknown action');

$isHospital = in_array($action, ['accept', 'reject'], true);
requireRole($isHospital ? ['ward_nurse', 'doctor'] : ['ambulance_crew', 'doctor', 'midwife', 'coordinator']);
if (in_array($action, ['reject', 'cancel'], true) && $reason === '') fail(422, 'Please give a reason');

$result = withRetry($pdo, function () use ($pdo, $id, $action, $to, $reason, $isHospital) {
    $pdo->beginTransaction();

    // Scope (golden rule 2): staff only see trips TO their hospital; crews only their OWN trips.
    $scope = $isHospital ? 'to_hospital_id = ?' : 'from_user_id = ?';
    $who   = $isHospital ? $_SESSION['hospital_id'] : $_SESSION['user_id'];
    $st = $pdo->prepare("SELECT * FROM transfers WHERE transfer_id = ? AND $scope FOR UPDATE");
    $st->execute([$id, $who]);
    $t = $st->fetch();
    if (!$t) { $pdo->rollBack(); fail(404, 'Transfer not found'); }

    if (!canMoveTransfer($t['stage'], $to)) {
        $pdo->rollBack();
        fail(409, "Cannot go from {$t['stage']} to $to", ['code' => 'ILLEGAL_STAGE']);
    }
    if ($action === 'reject' && time() - strtotime($t['created_at']) > 120) {
        $pdo->rollBack();
        fail(409, 'The 2-minute window to reject has passed', ['code' => 'REJECT_WINDOW_OVER']);
    }

    $bedStatus = 'held';
    if (in_array($to, ['rejected', 'cancelled'], true)) {
        Transfer::release($pdo, $t, $to, $reason);               // bed free + blood back + logged
        notify($pdo, $isHospital ? 'user' : 'hospital',
               (int)($isHospital ? $t['from_user_id'] : $t['to_hospital_id']), 'transfer_' . $to, $id, ['reason' => $reason]);
        $bedStatus = 'free';
    } else {
        $stamp = ['accepted' => 'accepted_at', 'in_transit' => 'in_transit_at', 'arrived' => 'arrived_at'][$to];
        // "Change only if still true": the WHERE stage = ? stops two people moving it at once
        $upd = $pdo->prepare("UPDATE transfers SET stage = ?, $stamp = NOW() WHERE transfer_id = ? AND stage = ?");
        $upd->execute([$to, $id, $t['stage']]);
        if ($upd->rowCount() !== 1) {
            $pdo->rollBack();
            fail(409, 'This trip just changed. Refresh.', ['code' => 'ILLEGAL_STAGE']);
        }
        if ($to === 'arrived') {
            $pdo->prepare("UPDATE beds SET status = 'occupied' WHERE bed_id = ? AND status = 'held'")->execute([$t['bed_id']]);
            $pdo->prepare("INSERT INTO bed_updates (bed_id, old_status, new_status, source, updated_by)
                           VALUES (?, 'held', 'occupied', 'hold', ?)")->execute([$t['bed_id'], $_SESSION['user_id']]);
            $bedStatus = 'occupied';
        }
        notify($pdo, $isHospital ? 'user' : 'hospital',
               (int)($isHospital ? $t['from_user_id'] : $t['to_hospital_id']), 'transfer_' . $to, $id);
        logAction($pdo, 'transfer_' . $to, 'transfers', $id, $t['stage'], $to);
    }
    $pdo->commit();
    return ['transfer_id' => $id, 'stage' => $to, 'bed_status' => $bedStatus];
});

if (!$isHospital && in_array($to, ['arrived', 'cancelled'], true)) unset($_SESSION['on_trip']);
respond(200, $result);
