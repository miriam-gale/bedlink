<?php
// api/trip.php (Yaw) — what Jeff's trip page polls every 5 seconds
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/expiry.php';
require_once __DIR__ . '/../classes/Matcher.php';

requireRole(['ambulance_crew', 'doctor', 'midwife', 'coordinator']);
runExpiry($pdo);

$st = $pdo->prepare("
    SELECT t.transfer_id, t.stage, t.reject_reason, t.bed_id,
           GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(), t.hold_until)) AS seconds_left,
           h.hospital_id, h.name, h.phone, h.latitude, h.longitude, h.is_diverting,
           b.label AS bed_label, wt.code AS ward_type,
           ho.sent_at, ho.seen_at, ho.ready_at
    FROM transfers t
    JOIN hospitals h   ON h.hospital_id = t.to_hospital_id
    JOIN beds b        ON b.bed_id = t.bed_id
    JOIN wards w       ON w.ward_id = b.ward_id
    JOIN ward_types wt ON wt.ward_type_id = w.ward_type_id
    LEFT JOIN handovers ho ON ho.transfer_id = t.transfer_id
    WHERE t.transfer_id = ? AND t.from_user_id = ?");     // own trip only: anyone else's gives 404
$st->execute([(int)($_GET['transfer_id'] ?? 0), $_SESSION['user_id']]);
$t = $st->fetch();
if (!$t) fail(404, 'Trip not found');

// Alert banner: rejected, or the hospital switched Divert on while we're driving
$alert  = null;
$active = in_array($t['stage'], ['pending', 'accepted', 'in_transit'], true);
if ($t['stage'] === 'rejected' || ($active && $t['is_diverting'])) {
    $pos = $pdo->prepare("SELECT latitude, longitude FROM ambulance_locations WHERE user_id = ? ORDER BY location_id DESC LIMIT 1");
    $pos->execute([$_SESSION['user_id']]);
    $p = $pos->fetch() ?: ['latitude' => $t['latitude'], 'longitude' => $t['longitude']];   // no GPS yet: search near the hospital
    $alert = [
        'type'    => $t['stage'] === 'rejected' ? 'rejected' : 'diverting',
        'message' => $t['reject_reason'],
        'next_best' => Matcher::best($pdo, ['lat' => (float)$p['latitude'], 'lng' => (float)$p['longitude'],
                         'ward' => $t['ward_type'], 'radius' => 25, 'exclude' => (int)$t['hospital_id']])[0] ?? null,
    ];
}

respond(200, [
    'transfer_id'  => (int)$t['transfer_id'],
    'stage'        => $t['stage'],
    'hospital'     => ['hospital_id' => (int)$t['hospital_id'], 'name' => $t['name'], 'phone' => $t['phone'],
                       'lat' => (float)$t['latitude'], 'lng' => (float)$t['longitude']],
    'bed_label'    => $t['bed_label'],
    'ward_type'    => $t['ward_type'],
    'seconds_left' => (int)$t['seconds_left'],          // seconds, not a date: phone clocks can be wrong
    'ticks'        => ['sent' => (bool)$t['sent_at'], 'seen' => (bool)$t['seen_at'], 'team_ready' => (bool)$t['ready_at']],
    'alert'        => $alert,
]);
