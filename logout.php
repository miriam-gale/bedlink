<?php
// logout.php: only the Logout button (POST + CSRF token) can log you out.
require_once _DIR_ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /bedlink/');
    exit;
}
checkCsrf();

logoutUser();
header('Location: /bedlink/login.php');
exit;