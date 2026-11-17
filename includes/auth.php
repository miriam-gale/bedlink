<?php
// includes/auth.php — FIRST line of every page and API file
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/functions.php';

session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => IS_HTTPS,
                           'httponly' => true, 'samesite' => 'Lax']);
session_start();

const IDLE_LIMIT = 15 * 60;                                        // 15 minutes
if (isset($_SESSION['user_id'])) {
    $onTrip = !empty($_SESSION['on_trip']);                        // crews mid-trip are never logged out
    if (!$onTrip && time() - ($_SESSION['last_seen'] ?? time()) > IDLE_LIMIT) {
        session_unset();
        session_destroy();
        isApi() ? fail(401, 'Session expired') : header('Location: /bedlink/login.php?expired=1');
        exit;
    }
    $_SESSION['last_seen'] = time();
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));                 // one secret token per session
}

function requireRole(array $roles): void {
    if (empty($_SESSION['user_id'])) {
        isApi() ? fail(401, 'Please log in') : header('Location: /bedlink/login.php');
        exit;
    }
    if (!in_array($_SESSION['role'], $roles, true)) {
        isApi() ? fail(403, 'Not allowed') : http_response_code(403);
        require __DIR__ . '/../403.php';
        exit;
    }
}
function csrfField(): string {                                     // put inside every HTML <form>
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
function checkCsrf(): void {                                       // call at the top of every POST
    $sent = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf'], $sent)) {
        fail(403, 'Security check failed. Please reload the page.');
    }
}
