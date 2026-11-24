<?php
// classes/Transfer.php (Yaw)
class Transfer {
    // Call INSIDE a transaction, after locking the transfer row with SELECT ... FOR UPDATE.
    // The one place that gives a bed and blood back, so reject, cancel, reroute and turn-away all behave the same.
    public static function release(PDO $pdo, array $t, string $stage, ?string $reason = null): void {
        $col = $stage === 'rejected' ? 'reject_reason' : 'cancel_reason';   // fixed list, never from the request
        $upd = $pdo->prepare("UPDATE transfers SET stage = ?, $col = ? WHERE transfer_id = ? AND stage = ?");
        $upd->execute([$stage, $reason, $t['transfer_id'], $t['stage']]);
        if ($upd->rowCount() !== 1) fail(409, 'This trip just changed. Refresh.', ['code' => 'ILLEGAL_STAGE']);

        $bed = $pdo->prepare("UPDATE beds SET status = 'free' WHERE bed_id = ? AND status = 'held'");
        $bed->execute([$t['bed_id']]);
        if ($bed->rowCount() === 1) {
            $pdo->prepare("INSERT INTO bed_updates (bed_id, old_status, new_status, source, updated_by)
                           VALUES (?, 'held', 'free', 'hold', ?)")
                ->execute([$t['bed_id'], $_SESSION['user_id'] ?? null]);
        }
        if ($t['blood_units'] > 0) {
            $pdo->prepare("UPDATE blood_stock SET units = units + ? WHERE hospital_id = ? AND blood_type = ?")
                ->execute([$t['blood_units'], $t['to_hospital_id'], $t['blood_type']]);
        }
        logAction($pdo, 'transfer_' . $stage, 'transfers', (int)$t['transfer_id'], $t['stage'], $stage);
    }
}
