<?php
// logout.php — ends the session, deletes the cookie, back to the login page.
require_once __DIR__ . '/includes/auth.php';

logoutUser();
header('Location: /bedlink/login.php');
exit;
