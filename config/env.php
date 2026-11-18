<?php
// config/env.php — reads .env into $_ENV (no Composer needed)
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die('Missing config/.env — copy .env.example and fill it in.');
}
foreach (parse_ini_file($envFile) as $key => $value) {
    $_ENV[$key] = $value;
}
define('IS_HTTPS', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
define('STORAGE', __DIR__ . '/../storage');
