<?php
// includes/expiry.php (Yaw) — holds that run out, auto-accept, alarms, escalations, privacy clean-up.
// XAMPP has no cron, so this is "lazy": it runs at the top of search, match, hold, trip and incoming.
require_once __DIR__ . '/state.php';

function runExpiry(PDO $pdo): void {
    if ($pdo->query("SELECT GET_LOCK('bedlink_expiry', 0)")->fetchColumn() != 1) return;
    try {
        $last = (int) @file_get_contents(STORAGE . '/last_expiry');
        if (time() - $last < 30) return;                       // at most once every 30 seconds
        file_put_contents(STORAGE . '/last_expiry', time());

        // 1. Expired holds: lock them, free their beds and blood, mark them expired.
        $pdo->beginTransaction();
        $rows = $pdo->query("SELECT transfer_id, bed_id, to_hospital_id, blood_type, blood_units, from_user_id, stage
                             FROM transfers
                             WHERE stage IN ('pending','accepted') AND hold_until < NOW()
                             ORDER BY bed_id FOR UPDATE")->fetchAll();
        foreach ($rows as $t) {
            $pdo->prepare("UPDATE beds SET status = 'free' WHERE bed_id = ? AND status = 'held'")->execute([$t['bed_id']]);
            if ($t['blood_units'] > 0) {
                $pdo->prepare("UPDATE blood_stock SET units = units + ? WHERE hospital_id = ? AND blood_type = ?")
                    ->execute([$t['blood_units'], $t['to_hospital_id'], $t['blood_type']]);
            }
            $pdo->prepare("UPDATE transfers SET stage = 'expired' WHERE transfer_id = ?")->execute([$t['transfer_id']]);
            notify($pdo, 'user', (int)$t['from_user_id'], 'hold_expired', (int)$t['transfer_id']);
            logAction($pdo, 'hold_expired', 'transfers', (int)$t['transfer_id'], $t['stage'], 'expired');
        }
        $pdo->commit();

        // 1b. A hospital that hasn't answered in 2 minutes counts as accepted (FR-30).
        $pdo->exec("UPDATE transfers SET stage = 'accepted', accepted_at = NOW()
                    WHERE stage = 'pending' AND created_at < NOW() - INTERVAL 2 MINUTE");

        // 2. Divert that has run out.
        $pdo->exec("UPDATE hospitals SET is_diverting = 0, divert_reason = NULL, divert_until = NULL
                    WHERE is_diverting = 1 AND divert_until IS NOT NULL AND divert_until < NOW()");

        // 3. Handover alarm at 3 minutes (the NUR-03 card turns red) ...
        $pdo->exec("UPDATE handovers SET alarm_at = NOW()
                    WHERE claimed_by IS NULL AND alarm_at IS NULL AND sent_at < NOW() - INTERVAL 3 MINUTE");
        // ... and the regional coordinator is told at 5 minutes.
        escalateUnclaimedHandovers($pdo);

        // 4. Privacy clean-up.
        $pdo->exec("DELETE FROM unidentified_patients
                    WHERE delete_after < NOW() OR (identified = 1 AND identified_at < NOW() - INTERVAL 24 HOUR)");
        $pdo->exec("DELETE FROM ambulance_locations WHERE created_at < NOW() - INTERVAL 7 DAY");
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log((string)$e);                                  // never break the user's request because clean-up failed
    } finally {
        $pdo->query("SELECT RELEASE_LOCK('bedlink_expiry')");
    }
}

function escalateUnclaimedHandovers(PDO $pdo): void {
    $rows = $pdo->query("SELECT ho.handover_id, ho.transfer_id, h.region_id
                         FROM handovers ho
                         JOIN transfers t ON t.transfer_id = ho.transfer_id
                         JOIN hospitals h ON h.hospital_id = t.to_hospital_id
                         WHERE ho.claimed_by IS NULL AND ho.escalated_at IS NULL
                           AND ho.sent_at < NOW() - INTERVAL 5 MINUTE")->fetchAll();
    $mark  = $pdo->prepare("UPDATE handovers SET escalated_at = NOW() WHERE handover_id = ? AND escalated_at IS NULL");
    $coord = $pdo->prepare("SELECT user_id FROM users WHERE role = 'coordinator' AND region_id = ? AND is_active = 1");
    foreach ($rows as $r) {
        $mark->execute([$r['handover_id']]);
        if ($mark->rowCount() === 0) continue;                  // already done by someone else
        $coord->execute([$r['region_id']]);
        foreach ($coord->fetchAll(PDO::FETCH_COLUMN) as $userId) {
            notify($pdo, 'user', (int)$userId, 'handover_unclaimed', (int)$r['transfer_id']);
        }
    }
}
