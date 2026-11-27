<?php
// api/search.php (Yaw) — public, read-only: no login, no CSRF
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/geo.php';
require_once __DIR__ . '/../includes/expiry.php';

$lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
$lng = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);
if (!inGhana($lat, $lng)) fail(422, 'Location must be inside Ghana', ['fields' => ['lat' => 'Must be inside Ghana']]);

$ward    = in_array($_GET['ward'] ?? '', WARD_TYPES, true) ? $_GET['ward'] : 'emergency';
$service = preg_replace('/[^a-z_]/', '', $_GET['service'] ?? '');
$nhis    = ($_GET['nhis'] ?? '1') === '1' ? 1 : 0;      // NHIS-friendly is the default
$onlyId  = (int)($_GET['hospital_id'] ?? 0);            // hospital.php asks for one hospital

runExpiry($pdo);                                         // expired holds count as free again

[$minLat, $maxLat, $minLng, $maxLng] = boundingBox($lat, $lng, 50);

$st = $pdo->prepare("
    SELECT h.hospital_id, h.name, h.ownership, h.accepts_nhis, h.phone, h.latitude, h.longitude,
           SUM(b.status = 'free' AND b.is_surge = 0) AS free_beds,
           SUM(b.status = 'occupied' AND b.expected_discharge_at <= NOW() + INTERVAL 2 HOUR) AS expected_soon,
           MAX(b.updated_at) AS last_update
    FROM hospitals h
    JOIN wards w       ON w.hospital_id = h.hospital_id AND w.is_active = 1
    JOIN ward_types wt ON wt.ward_type_id = w.ward_type_id AND wt.code = ?
    JOIN beds b        ON b.ward_id = w.ward_id AND b.is_active = 1
    WHERE h.is_approved = 1 AND h.is_active = 1 AND h.is_diverting = 0
      AND h.latitude BETWEEN ? AND ? AND h.longitude BETWEEN ? AND ?
      AND (? = 0 OR (h.accepts_nhis = 1 AND h.ownership IN ('government','mission')))
      AND (? = 0 OR h.hospital_id = ?)
      AND (? = '' OR EXISTS (SELECT 1 FROM hospital_services hs
                             WHERE hs.hospital_id = h.hospital_id AND hs.service = ? AND hs.available = 1))
    GROUP BY h.hospital_id
    HAVING free_beds > 0 OR expected_soon > 0 OR ? > 0");
$st->execute([$ward, $minLat, $maxLat, $minLng, $maxLng, $nhis, $onlyId, $onlyId, $service, $service, $onlyId]);

$results = [];
foreach ($st->fetchAll() as $h) {
    $km = distanceKm($lat, $lng, (float)$h['latitude'], (float)$h['longitude']);
    $results[] = [
        'hospital_id' => (int)$h['hospital_id'], 'name' => $h['name'], 'phone' => $h['phone'],
        'ownership' => $h['ownership'], 'nhis' => (bool)$h['accepts_nhis'],
        'distance_km' => round($km, 1), 'eta_min' => etaMinutes($km),
        'free_beds' => (int)$h['free_beds'], 'expected_soon' => (int)$h['expected_soon'],
        'confidence' => confidenceLabel($h['last_update']),
        'updated_ago' => (int) floor((time() - strtotime($h['last_update'])) / 60),   // minutes
        'lat' => (float)$h['latitude'], 'lng' => (float)$h['longitude'],
    ];
}
usort($results, fn($a, $b) => $a['distance_km'] <=> $b['distance_km']);
respond(200, ['count' => count($results), 'results' => array_slice($results, 0, 20)]);
