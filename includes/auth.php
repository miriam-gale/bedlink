<?php
// includes/auth.php — FIRST line of every page and API file
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/functions.php';

// Strict sessions: PHP refuses a session id it did not create, ids never travel in the URL,
// and our own cookie name keeps BedLink apart from other projects on localhost.
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
session_name('BEDLINKSESSID');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => IS_HTTPS,
                           'httponly' => true, 'samesite' => 'Lax']);
session_start();

define('IDLE_LIMIT', (int)setting('session_idle_minutes', 15) * 60);   // from config/settings.json
if (isset($_SESSION['user_id'])) {
    $onTrip = !empty($_SESSION['on_trip']);                        // crews mid-trip are never logged out
    if (!$onTrip && time() - ($_SESSION['last_seen'] ?? time()) > IDLE_LIMIT) {
        logoutUser();
        isApi() ? fail(401, 'Session expired') : header('Location: /bedlink/login.php?expired=1');
        exit;
    }
    $_SESSION['last_seen'] = time();
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));                 // one secret token per session
}

// Call once, right after the password is checked (login.php).
function loginUser(array $u): void {
    session_regenerate_id(true);                                   // NEW session id: stops session fixation
    $_SESSION['user_id']     = (int)$u['user_id'];
    $_SESSION['role']        = $u['role'];
    $_SESSION['hospital_id'] = $u['hospital_id'];                  // golden rule 2 depends on this line
    $_SESSION['region_id']   = $u['region_id'];
    $_SESSION['name']        = $u['full_name'];
    $_SESSION['last_seen']   = time();
    $_SESSION['csrf']        = bin2hex(random_bytes(32));          // fresh token for the logged-in session
}

// Empties the session and deletes the browser cookie (logout.php and idle timeout).
function logoutUser(): void {
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/', 'secure' => IS_HTTPS,
                                       'httponly' => true, 'samesite' => 'Lax']);
        session_destroy();
    }
}

function requireRole(array $roles): void {
    if (empty($_SESSION['user_id'])) {
        isApi() ? fail(401, 'Please log in') : header('Location: /bedlink/login.php');
        exit;
    }
    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
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
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'], $sent)) {
        fail(403, 'Security check failed. Please reload the page.');
    }
}
