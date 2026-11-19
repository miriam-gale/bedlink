<?php
// ==========================================================
// 403.php — BedLink Access Denied Page
// ==========================================================

// Load authentication, session and shared helper functions.
require_once __DIR__ . '/includes/auth.php';

// Tell the browser that this page is a Forbidden (403) response.
http_response_code(403);

// Set the shared page title and styling class.
$pageTitle = 'Access Denied';
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
        <h1>Access Denied</h1>

        <!-- Friendly explanation -->
        <p>
            You do not have permission to access this page.
        </p>

        <p>
            If you believe this is a mistake, please contact
            the appropriate administrator.
        </p>

        <!-- Return to the homepage -->
        <a href="/bedlink/" class="btn btn-primary">
            Return to BedLink
        </a>

        <!-- Login option -->
        <p>
            <a href="/bedlink/login.php">
                Log in with another account
            </a>
        </p>

    </section>

</main>

<?php
// Load the shared footer.
require __DIR__ . '/includes/footer.php';
?>