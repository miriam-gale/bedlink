<?php
// includes/functions.php — everyone uses these; only Hariel edits them

// ---------- Escaping and request helpers ----------
function e(?string $s): string {                                   // escape anything printed into HTML
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
function isApi(): bool {
    return str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/api/');
}
function jsonInput(): array {                                      // read a JSON request body
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

// ---------- One JSON shape everywhere (golden rule 3) ----------
function respond(int $status, $data = null): never {               // success
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'data' => $data]);
    exit;
}
function fail(int $status, string $error, array $extra = []): never {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => $error] + $extra);
    exit;
}

// ---------- Audit log (call inside the same transaction as the change) ----------
function logAction(PDO $pdo, string $action, string $table, int $recordId,
                   $old, $new, ?array $payload = null, string $status = 'applied'): int {
    $pdo->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, old_value,
                                          new_value, payload, status, ip)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, INET6_ATON(?))")
        ->execute([$_SESSION['user_id'] ?? null, $action, $table, $recordId, $old, $new,
                   $payload ? json_encode($payload) : null, $status,
                   $_SERVER['REMOTE_ADDR'] ?? null]);
    return (int)$pdo->lastInsertId();
}

// ---------- Shared helpers from Recipe 4.0 (Yaw / James) ----------
const BLOOD_TYPES = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
const WARD_TYPES  = ['emergency', 'maternity', 'icu', 'general', 'children', 'nicu', 'labour'];

// One alert row. $to = 'user' or 'hospital' (= whoever is on shift there). Never sends anything itself.
function notify(PDO $pdo, string $to, int $id, string $type, ?int $transferId = null, ?array $payload = null): void {
    $pdo->prepare("INSERT INTO notifications (recipient_user_id, recipient_hospital_id, channel, type, payload, transfer_id)
                   VALUES (?, ?, 'in_app', ?, ?, ?)")
        ->execute([$to === 'user' ? $id : null, $to === 'hospital' ? $id : null, $type,
                   $payload ? json_encode($payload) : null, $transferId]);
}

// Runs a transaction; if MySQL says "deadlock" (1213) or "lock wait timeout" (1205) it tries once more.
function withRetry(PDO $pdo, callable $work) {
    for ($try = 1; ; $try++) {
        try {
            return $work();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $code = $e instanceof PDOException ? (int)($e->errorInfo[1] ?? 0) : 0;
            $busy = in_array($code, [1205, 1213], true);
            if ($busy && $try < 2) { usleep(100000); continue; }   // wait 0.1 s, then once more
            error_log((string)$e);
            $busy ? fail(503, 'Busy, please try again') : fail(500, 'Something went wrong. Nothing was saved.');
        }
    }
}

// ---------- Where each role lands after login (used by login.php) ----------
// NOTE: the cookbook points to "Spec Part 6.2" for this table, which isn't in the PDF.
// These paths follow the folder tree in Section 1.2 — adjust them to match the Spec.
function homeFor(string $role): string {
    return match ($role) {
        'ward_nurse', 'doctor', 'hospital_admin' => '/bedlink/staff/',
        'ambulance_crew', 'community_driver'     => '/bedlink/ambulance/',
        'midwife'                                => '/bedlink/midwife/',
        'blood_bank'                             => '/bedlink/bloodbank/',
        'coordinator'                            => '/bedlink/coordinator/',
        'nbs_officer'                            => '/bedlink/nbs/',
        'analyst'                                => '/bedlink/analytics/',
        'system_admin'                           => '/bedlink/admin/',
        default                                  => '/bedlink/',
    };
}
