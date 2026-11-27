<?php
// api/help.php (Yaw) — "Get help now" (Me). No login, so rate-limited: 5 per hour per person or IP.
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/geo.php';

checkCsrf();                                     // every page (even public) has the token in its <meta>
$in  = jsonInput();
$lat = (float)($in['lat'] ?? 0); $lng = (float)($in['lng'] ?? 0);
if (!inGhana($lat, $lng)) fail(422, 'Location must be inside Ghana');

$userId = $_SESSION['user_id'] ?? null;
$ip     = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

$c = $pdo->prepare("SELECT COUNT(*) FROM incidents
                    WHERE (reporter_ip = INET6_ATON(?) OR (? IS NOT NULL AND reporter_user_id = ?))
                      AND created_at > NOW() - INTERVAL 1 HOUR");
$c->execute([$ip, $userId, $userId]);
if ($c->fetchColumn() >= 5) fail(429, 'Too many requests. Please call 112.');

$pdo->beginTransaction();
$pdo->prepare("INSERT INTO incidents (type, reporter_user_id, reporter_ip, latitude, longitude)
               VALUES ('get_help', ?, INET6_ATON(?), ?, ?)")->execute([$userId, $ip, $lat, $lng]);
$id = (int)$pdo->lastInsertId();

// Text up to 3 saved emergency contacts (simulated SMS: they appear in James's SMS phone, SIM-02)
$texted = 0;
if ($userId) {
    $u = $pdo->prepare("SELECT full_name, emergency_contacts FROM users WHERE user_id = ?");
    $u->execute([$userId]);
    $me  = $u->fetch();
    $sms = $pdo->prepare("INSERT INTO notifications (phone, channel, type, payload) VALUES (?, 'sms', 'help_alert', ?)");
    foreach (array_slice(json_decode($me['emergency_contacts'] ?? '[]', true) ?: [], 0, 3) as $contact) {
        $sms->execute([$contact['phone'], json_encode(['text' => "{$me['full_name']} needs help: https://maps.google.com/?q=$lat,$lng"])]);
        $texted++;
    }
}
logAction($pdo, 'get_help', 'incidents', $id, null, 'open');
$pdo->commit();
respond(201, ['reference' => 'H' . str_pad((string)$id, 5, '0', STR_PAD_LEFT), 'contacts_texted' => $texted]);
