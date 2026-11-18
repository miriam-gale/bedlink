<?php
// config/db.php — one PDO connection for the whole request
require_once __DIR__ . '/env.php';

try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // errors become exceptions we can catch
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // rows come back as ['col' => value]
            PDO::ATTR_EMULATE_PREPARES   => false,                  // REAL prepared statements (SQL injection safe)
        ]
    );
    $pdo->exec("SET SESSION innodb_lock_wait_timeout = 5");         // don't wait forever on a locked row
} catch (PDOException $e) {
    error_log($e->getMessage());                                    // details for us, in the log
    http_response_code(500);
    exit('BedLink is having trouble right now. Please try again.'); // never show the raw error
}
