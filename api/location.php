<?php
// api/location.php (Yaw) — live GPS.
// POST (crew phone, every 15 s while driving) stores the point and keeps the hold alive when moving closer (FR-31).
// GET (receiving hospital) returns the last position and ETA.
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/geo.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    requireRole(['ward_nurse', 'doctor']);
    $st = $pdo->prepare("SELECT l.latitude, l.longitude, l.created_at, h.latitude AS hlat, h.longitude AS hlng
                         FROM transfers t
                         JOIN hospitals h ON h.hospital_id = t.to_hospital_id
                         JOIN ambulance_locations l ON l.transfer_id = t.transfer_id
                         WHERE t.transfer_id = ? AND t.to_hospital_id = ?
                         ORDER BY l.location_id DESC LIMIT 1");
    $st->execute([(int)($_GET['transfer_id'] ?? 0), $_SESSION['hospital_id']]);
    $p = $st->fetch();
    if (!$p) fail(404, 'No position yet');
    $km = distanceKm((float)$p['latitude'], (float)$p['longitude'], (float)$p['hlat'], (float)$p['hlng']);
    respond(200, ['lat' => (float)$p['latitude'], 'lng' => (float)$p['longitude'],
                  'eta_min' => etaMinutes($km), 'recorded_at' => $p['created_at']]);
}

requireRole(['ambulance_crew', 'community_driver']);
checkCsrf();
$in  = jsonInput();
$lat = (float)($in['lat'] ?? 0); $lng = (float)($in['lng'] ?? 0);
if (!inGhana($lat, $lng)) fail(422, 'Location must be inside Ghana');
$acc    = isset($in['accuracy']) ? min(65535, max(0, (int)$in['accuracy'])) : null;
$userId = (int)$_SESSION['user_id'];

// Find this crew's active trip (if any) and the last point we stored
$st = $pdo->prepare("SELECT t.transfer_id, t.stage, h.latitude AS hlat, h.longitude AS hlng
                     FROM transfers t JOIN hospitals h ON h.hospital_id = t.to_hospital_id
                     WHERE t.from_user_id = ? AND t.stage IN ('pending','accepted','in_transit')
                     ORDER BY t.transfer_id DESC LIMIT 1");
$st->execute([$userId]);
$trip = $st->fetch();

$prev = $pdo->prepare("SELECT latitude, longitude, created_at FROM ambulance_locations WHERE user_id = ? ORDER BY location_id DESC LIMIT 1");
$prev->execute([$userId]);
$last = $prev->fetch();
if ($last && time() - strtotime($last['created_at']) < 10) respond(200, ['ignored' => true]);   // max one point per 10 s

$pdo->prepare("INSERT INTO ambulance_locations (user_id, transfer_id, latitude, longitude, accuracy_m) VALUES (?, ?, ?, ?, ?)")
    ->execute([$userId, $trip['transfer_id'] ?? null, $lat, $lng, $acc]);

$holdUntil = null;
if ($trip && $last) {
    $before = distanceKm((float)$last['latitude'], (float)$last['longitude'], (float)$trip['hlat'], (float)$trip['hlng']);
    $now    = distanceKm($lat, $lng, (float)$trip['hlat'], (float)$trip['hlng']);
    if ($now < $before - 0.05) {                 // moved at least 50 m closer to the hospital
        // FR-31: a hold on the move never runs out: keep at least 10 minutes on the clock
        $pdo->prepare("UPDATE transfers SET hold_until = GREATEST(hold_until, NOW() + INTERVAL 10 MINUTE) WHERE transfer_id = ?")
            ->execute([$trip['transfer_id']]);
        // First ping moving closer counts as "Start driving"
        $pdo->prepare("UPDATE transfers SET stage = 'in_transit', in_transit_at = NOW()
                       WHERE transfer_id = ? AND stage IN ('pending','accepted')")->execute([$trip['transfer_id']]);
    }
}
if ($trip) {
    $h = $pdo->prepare("SELECT hold_until FROM transfers WHERE transfer_id = ?");
    $h->execute([$trip['transfer_id']]);
    $holdUntil = $h->fetchColumn();
}
respond(200, ['hold_until' => $holdUntil]);
