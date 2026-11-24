<?php
// includes/geo.php (Yaw) — used by search, match, location, incidents and USSD

function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float {
    $R = 6371;                                   // Earth's radius in km
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
    return $R * 2 * atan2(sqrt($a), sqrt(1 - $a)); // Haversine: straight-line distance on a sphere
}

function etaMinutes(float $km): int {
    return max(1, (int) ceil($km / 30 * 60));    // average city speed 30 km/h
}

function confidenceLabel(?string $lastUpdate): string {
    if (!$lastUpdate) return 'uncertain';
    $age = time() - strtotime($lastUpdate);
    if ($age <= 10 * 60) return 'live';          // 10 minutes
    if ($age <= 2 * 3600) return 'recent';       // 2 hours
    return 'uncertain';
}

function boundingBox(float $lat, float $lng, float $km): array { // a quick square, so SQL can use the index
    $dLat = $km / 111;                           // 1 degree of latitude is about 111 km
    $dLng = $km / (111 * cos(deg2rad($lat)));
    return [$lat - $dLat, $lat + $dLat, $lng - $dLng, $lng + $dLng];
}

function inGhana($lat, $lng): bool {
    return is_float($lat) && is_float($lng) && $lat >= 4.5 && $lat <= 11.5
        && $lng >= -3.5 && $lng <= 1.5;
}
