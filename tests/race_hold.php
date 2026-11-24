<?php
// tests/race_hold.php (Yaw) — 20 ambulances tap Hold on the LAST free bed at the same instant.
// Run from the bedlink folder: php tests/race_hold.php
// Setup: seed has crew1..crew20@test.com (Test1234!). Leave Korle Bu emergency with exactly one free bed
// (see the SQL in Cookbook, "Yaw's race test") and clear transfers first.
$base  = getenv('BEDLINK_URL') ?: 'http://localhost/bedlink';
$crews = 20;

function csrfFrom(string $html): string {
    preg_match('/name="csrf" content="([^"]+)"/', $html, $m);
    return $m[1] ?? '';
}
function request(string $url, string $jar, ?array $form = null): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar,
                            CURLOPT_FOLLOWLOCATION => true]);
    if ($form) curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($form)]);
    $out = curl_exec($ch);
    curl_close($ch);
    return (string)$out;
}

// 1. Log in 20 different crews, one cookie jar each (= 20 phones)
$mh = curl_multi_init();
$handles = [];
for ($i = 1; $i <= $crews; $i++) {
    $jar   = tempnam(sys_get_temp_dir(), 'crew');
    $token = csrfFrom(request("$base/login.php", $jar));
    request("$base/login.php", $jar, ['email' => "crew$i@test.com", 'password' => 'Test1234!', 'csrf' => $token]);
    $token = csrfFrom(request("$base/ambulance/index.php", $jar));   // any logged-in page prints the token

    // 2. Prepare the hold request, but don't send it yet
    $ch = curl_init("$base/api/hold.php");
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => $jar, CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', "X-CSRF-Token: $token"],
        CURLOPT_POSTFIELDS => json_encode(['hospital_id' => 1, 'ward_type' => 'emergency', 'lat' => 5.5600, 'lng' => -0.2050])]);
    curl_multi_add_handle($mh, $ch);
    $handles[] = $ch;
}

// 3. Fire all 20 at the same moment
do { curl_multi_exec($mh, $running); curl_multi_select($mh); } while ($running);

$codes = array_count_values(array_map(fn($ch) => curl_getinfo($ch, CURLINFO_HTTP_CODE), $handles));
ksort($codes);
foreach ($codes as $code => $n) echo "$code: $n ";
echo PHP_EOL;
echo (($codes[201] ?? 0) === 1 && ($codes[409] ?? 0) === $crews - 1)
    ? "PASS: exactly one ambulance got the bed\n"
    : "FAIL: look at hold.php (and the PHP error log)\n";
