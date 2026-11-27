<?php
// api/incidents.php (Yaw) — POST: a bystander reports an accident.
// Reports within 100 m and 10 minutes are merged: 6 people, 1 incident with 6 reports.
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/geo.php';

checkCsrf();
$in  = jsonInput();
$lat = (float)($in['lat'] ?? 0); $lng = (float)($in['lng'] ?? 0);
if (!inGhana($lat, $lng)) fail(422, 'Location must be inside Ghana');
$hurt    = max(0, min(50, (int)($in['people_hurt'] ?? 1)));
$answers = ['awake' => ($in['awake'] ?? '') === 'yes' ? 'yes' : 'no',
            'bleeding' => ($in['bleeding'] ?? '') === 'yes' ? 'yes' : 'no'];
$ip     = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$userId = $_SESSION['user_id'] ?? null;

// Same 5-per-hour limit as help.php
$c = $pdo->prepare("SELECT COUNT(*) FROM incidents
                    WHERE (reporter_ip = INET6_ATON(?) OR (? IS NOT NULL AND reporter_user_id = ?))
                      AND created_at > NOW() - INTERVAL 1 HOUR");
$c->execute([$ip, $userId, $userId]);
if ($c->fetchColumn() >= 5) fail(429, 'Too many requests. Please call 112.');

$result = withRetry($pdo, function () use ($pdo, $lat, $lng, $hurt, $answers, $ip) {
    $pdo->beginTransaction();

    // Lock every open report in a small square around us, oldest first
    [$a, $b, $c, $d] = boundingBox($lat, $lng, 0.2);
    $st = $pdo->prepare("SELECT incident_id, latitude, longitude FROM incidents
                         WHERE type = 'bystander' AND status <> 'closed' AND merged_into IS NULL
                           AND created_at > NOW() - INTERVAL 10 MINUTE
                           AND latitude BETWEEN ? AND ? AND longitude BETWEEN ? AND ?
                         ORDER BY incident_id FOR UPDATE");
    $st->execute([$a, $b, $c, $d]);
    $main = null;
    foreach ($st->fetchAll() as $row) {
        if (distanceKm($lat, $lng, (float)$row['latitude'], (float)$row['longitude']) <= 0.1) { $main = $row; break; }   // 100 m
    }

    $pdo->prepare("INSERT INTO incidents (type, reporter_user_id, reporter_ip, latitude, longitude, people_hurt, answers, merged_into)
                   VALUES ('bystander', ?, INET6_ATON(?), ?, ?, ?, ?, ?)")
        ->execute([$_SESSION['user_id'] ?? null, $ip, $lat, $lng, $hurt, json_encode($answers), $main['incident_id'] ?? null]);
    $id = (int)$pdo->lastInsertId();

    if ($main) {
        $pdo->prepare("UPDATE incidents SET report_count = report_count + 1,
                       people_hurt = GREATEST(COALESCE(people_hurt, 0), ?) WHERE incident_id = ?")
            ->execute([$hurt, $main['incident_id']]);
    }
    $mainId = (int)($main['incident_id'] ?? $id);
    $n = $pdo->prepare("SELECT report_count FROM incidents WHERE incident_id = ?");
    $n->execute([$mainId]);
    $count = (int)$n->fetchColumn();

    logAction($pdo, $main ? 'incident_merge' : 'incident_new', 'incidents', $id, null, (string)$mainId);
    $pdo->commit();
    return ['reference' => 'B' . str_pad((string)$mainId, 5, '0', STR_PAD_LEFT), 'merged' => (bool)$main,
            'report_count' => $count, 'tips' => ['Keep the area safe', 'Do not move the injured unless in danger', 'Call 112']];
});
respond(201, $result);
