<?php
// ==========================================================
// 403.php — BedLink Access Denied Page
// ==========================================================

// Load authentication, session and shared helper functions.
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/i18n.php';

// Tell the browser that this page is a Forbidden (403) response.
http_response_code(403);

// Set the shared page title and styling class.
$pageTitle = t('err.403.title');
$bodyClass = 'error-403-page';

// Load the shared header.
require __DIR__ . '/includes/header.php';
?>

<!-- ==========================================================
     ACCESS DENIED CONTENT
     ========================================================== -->

<main class="container">

    <section class="card error-page">

        <!-- Error number -->
        <p class="error-code">403</p>

        <!-- Main heading -->
        <h1><?= e(t('err.403.title')) ?></h1>

        <!-- Friendly explanation -->
        <p><?= e(t('err.403.text')) ?></p>

        <!-- Return to the homepage -->
        <a href="/bedlink/" class="btn btn-primary"><?= e(t('err.403.home')) ?></a>

        <!-- Login option -->
        <p><a href="/bedlink/login.php"><?= e(t('err.403.login')) ?></a></p>

    </section>

</main>

<?php
// Load the shared footer.
require __DIR__ . '/includes/footer.php';
?>