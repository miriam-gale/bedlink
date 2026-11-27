<?php
// api/match.php (Yaw) — Ambulance mode: top 3 hospitals that can REALLY take this patient now.
// Contract = Cookbook 7.4 (radius 10|25|50, can_widen, lat/lng, confidence, updated_min, score)
// PLUS the extra fields from matches.json: town, diverting, services[], blood_stock[], coordinator_told.
// Extras are additions only: nothing Jeff's results.js already reads has been renamed.
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/expiry.php';
require_once __DIR__ . '/../classes/Matcher.php';

requireRole(['ambulance_crew', 'doctor', 'midwife', 'coordinator']);

$lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
$lng = filter_input(INPUT_GET, 'lng', FILTER_VALIDATE_FLOAT);
if (!inGhana($lat, $lng)) fail(422, 'Location must be inside Ghana');

$ward = $_GET['ward'] ?? '';
if (!in_array($ward, WARD_TYPES, true)) fail(422, 'Choose a ward');

$allowed  = ['ct_scan', 'trauma_surgery', 'blood_bank', 'icu', 'nicu', 'obstetrics', 'theatre'];
$services = array_values(array_intersect(explode(',', $_GET['services'] ?? ''), $allowed));
$blood    = in_array($_GET['blood'] ?? '', BLOOD_TYPES, true) ? $_GET['blood'] : null;
$units    = $blood ? max(0, min(10, (int)($_GET['units'] ?? 0))) : 0;
$radius   = in_array((int)($_GET['radius'] ?? 10), [10, 25, 50], true) ? (int)$_GET['radius'] : 10;

runExpiry($pdo);

$rows = Matcher::best($pdo, ['lat' => $lat, 'lng' => $lng, 'ward' => $ward, 'services' => $services,
                             'blood' => $blood, 'units' => $units, 'radius' => $radius]);

$coordinatorTold = false;
if (!$rows && $radius === 50) {                 // nothing even at 50 km: tell the coordinator (COO-01)
    $c = $pdo->prepare("SELECT user_id FROM users WHERE role = 'coordinator' AND region_id = 1 AND is_active = 1");
    $c->execute();                              // demo: Greater Accra; later use the region of the crew's position
    foreach ($c->fetchAll(PDO::FETCH_COLUMN) as $uid) {
        notify($pdo, 'user', (int)$uid, 'unmatched', null, ['lat' => $lat, 'lng' => $lng, 'ward' => $ward, 'services' => $services]);
        $coordinatorTold = true;
    }
}

// Enrich only the top 3 (cheap): what each hospital can do, and blood ABOVE the safety reserve.
$top = array_slice($rows, 0, 3);
$svc = $pdo->prepare("SELECT service FROM hospital_services WHERE hospital_id = ? AND available = 1 ORDER BY service");
// GREATEST(...) - reserve avoids UNSIGNED subtraction errors when units < reserve
$bld = $pdo->prepare("SELECT blood_type, GREATEST(units, reserve_units) - reserve_units AS available
                      FROM blood_stock WHERE hospital_id = ? AND units > reserve_units
                      ORDER BY FIELD(blood_type, 'A+','A-','B+','B-','AB+','AB-','O+','O-')");
foreach ($top as &$h) {
    $svc->execute([$h['hospital_id']]);
    $h['services'] = $svc->fetchAll(PDO::FETCH_COLUMN);
    $bld->execute([$h['hospital_id']]);
    $h['blood_stock'] = array_map(fn($r) => ['type' => $r['blood_type'], 'available_units' => (int)$r['available']],
                                  $bld->fetchAll());
    $h['diverting'] = false;                    // always false: Matcher already filters diverting hospitals out
}
unset($h);

respond(200, ['radius_km' => $radius, 'results' => $top, 'can_widen' => $radius < 50,
              'coordinator_told' => $coordinatorTold]);
