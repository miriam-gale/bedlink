<?php
// classes/Matcher.php (Yaw) — "which hospitals can really take this patient now, best first?"
// Used by match.php, hold.php (next best), trip.php (reroute) and James's divert.php.
require_once __DIR__ . '/../includes/geo.php';

class Matcher {
    // $p = ['lat'=>5.57, 'lng'=>-0.22, 'ward'=>'emergency', 'services'=>['ct_scan'], 'blood'=>'O+', 'units'=>2,
    //       'radius'=>10, 'exclude'=>0]   (exclude = a hospital to leave out, e.g. the one that just said no)
    public static function best(PDO $pdo, array $p): array {
        $services = $p['services'] ?? [];
        $units = (int)($p['units'] ?? 0);
        $in = $services ? implode(',', array_fill(0, count($services), '?')) : "''";
        [$minLat, $maxLat, $minLng, $maxLng] = boundingBox($p['lat'], $p['lng'], $p['radius']);

        $st = $pdo->prepare("
            SELECT h.hospital_id, h.name, h.town, h.phone, h.latitude, h.longitude,
                   SUM(b.status = 'free' AND b.is_surge = 0) AS free_beds,
                   MAX(b.updated_at) AS last_update
            FROM hospitals h
            JOIN wards w       ON w.hospital_id = h.hospital_id AND w.is_active = 1
            JOIN ward_types wt ON wt.ward_type_id = w.ward_type_id AND wt.code = ?
            JOIN beds b        ON b.ward_id = w.ward_id AND b.is_active = 1
            WHERE h.is_approved = 1 AND h.is_active = 1 AND h.is_diverting = 0
              AND h.latitude BETWEEN ? AND ?
              AND h.longitude BETWEEN ? AND ?
              AND h.hospital_id <> ?
              -- every service asked for is available
              AND (SELECT COUNT(DISTINCT hs.service) FROM hospital_services hs
                   WHERE hs.hospital_id = h.hospital_id AND hs.available = 1
                     AND hs.service IN ($in)) = ?
              -- and none of the matching equipment is broken
              AND NOT EXISTS (SELECT 1 FROM equipment_status e
                              WHERE e.hospital_id = h.hospital_id AND e.kind = 'equipment'
                                AND e.working = 0 AND e.item IN ($in))
              -- enough blood ABOVE the safety reserve (written as >= so UNSIGNED never goes negative)
              AND (? = 0 OR EXISTS (SELECT 1 FROM blood_stock bs
                                    WHERE bs.hospital_id = h.hospital_id AND bs.blood_type = ?
                                      AND bs.units >= bs.reserve_units + ?))
            GROUP BY h.hospital_id
            HAVING free_beds > 0");
        $st->execute(array_merge(
            [$p['ward'], $minLat, $maxLat, $minLng, $maxLng, (int)($p['exclude'] ?? 0)],
            $services, [count($services)], $services,
            [$units, $p['blood'] ?? '', $units]));

        $penalty = ['live' => 0, 'recent' => 5, 'uncertain' => 15];   // minutes added for old data
        $out = [];
        foreach ($st->fetchAll() as $h) {
            $km = distanceKm($p['lat'], $p['lng'], (float)$h['latitude'], (float)$h['longitude']);
            if ($km > $p['radius']) continue;                           // the box is a square: trim its corners
            $conf = confidenceLabel($h['last_update']);
            $eta  = etaMinutes($km);
            $out[] = [
                'hospital_id' => (int)$h['hospital_id'], 'name' => $h['name'], 'town' => $h['town'],
                'phone' => $h['phone'], 'ward_type' => $p['ward'],
                'eta_min' => $eta, 'distance_km' => round($km, 1),
                'free_beds' => (int)$h['free_beds'],
                'services_ok' => true, 'blood_ok' => true,              // true by construction: the SQL filtered the rest
                'confidence' => $conf,
                'updated_min' => (int) floor((time() - strtotime($h['last_update'])) / 60),
                'score' => $eta + $penalty[$conf],                      // facts only: ownership is ignored
                'lat' => (float)$h['latitude'], 'lng' => (float)$h['longitude'],
            ];
        }
        usort($out, fn($a, $b) => $a['score'] <=> $b['score']);        // lowest score = best
        return $out;
    }
}
