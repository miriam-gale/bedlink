<?php
// ==========================================================
// BEDLINK NAVIGATION
// Displays the navigation links and the Login/Logout button.==========================================================


// ----------------------------------------------------------
// 1. CHECK LOGIN STATUS
// ----------------------------------------------------------

// Check whether a user is currently logged in.
$loggedIn = !empty($_SESSION['user_id']);

// Retrieve the logged-in user's role and name.
$userRole = $_SESSION['role'] ?? '';
$userName = $_SESSION['name'] ?? '';


// ----------------------------------------------------------
// 2. DEFINE ROLE-SPECIFIC NAVIGATION
// ----------------------------------------------------------

// Different users see different navigation links.
// The translation key is the array key; the URL is the value.

$menus = [
    'ward_nurse' => [
        'nav.bedboard' => '/bedlink/staff/bedboard.php',
        'nav.incoming' => '/bedlink/staff/incoming.php',
    ],

    'doctor' => [
        'nav.bedboard' => '/bedlink/staff/bedboard.php',
        'nav.incoming' => '/bedlink/staff/incoming.php',
    ],

    'hospital_admin' => [
        'nav.bedboard' => '/bedlink/staff/bedboard.php',
        'nav.incoming' => '/bedlink/staff/incoming.php',
    ],

    'ambulance_crew' => [
        'nav.ambulance' => '/bedlink/ambulance/',
    ],

    'community_driver' => [
        'nav.ambulance' => '/bedlink/ambulance/',
    ],

    // More role-specific links will be added as their pages are built.
];


// ----------------------------------------------------------
// 3. DETERMINE THE CURRENT PAGE
// ----------------------------------------------------------

// Get the URL path without query parameters.
$currentPath = parse_url(
    $_SERVER['REQUEST_URI'] ?? '/',
    PHP_URL_PATH
);

// Remove trailing slashes.
$currentPath = rtrim($currentPath, '/') ?: '/';

// Treat index.php as the homepage.
if (basename($currentPath) === 'index.php') {
    $currentPath = rtrim(dirname($currentPath), '/') ?: '/';
}


// ----------------------------------------------------------
// 4. CHECK WHETHER A LINK IS ACTIVE
// ----------------------------------------------------------

// This function compares a link's URL with the current page.
// It helps highlight the page the user is currently viewing.

$isActive = function (string $url) use ($currentPath): bool {

    // Extract the path from the link.
    $linkPath = parse_url($url, PHP_URL_PATH);

    // Remove trailing slashes from the link.
    $linkPath = rtrim($linkPath, '/') ?: '/';

    // Treat index.php as the homepage in links too.
    if (basename($linkPath) === 'index.php') {
        $linkPath = rtrim(dirname($linkPath), '/') ?: '/';
    }

    // Return true when both paths match.
    return $currentPath === $linkPath;
};
?>

<!-- ==========================================================
     5. MAIN NAVIGATION
     ========================================================== -->

<nav class="main-nav" aria-label="<?= e(t('nav.menu')) ?>">

    <!-- Find a Bed is the primary navigation destination -->
    <!-- It is intentionally the only public navigation link -->

    <a href="/bedlink/" class="nav-link nav-find-link <?= $isActive('/bedlink/') ? 'active' : '' ?>"
        <?= $isActive('/bedlink/') ? 'aria-current="page"' : '' ?>>

        <?= e(t('nav.search')) ?>

    </a>


    <!-- ------------------------------------------------------
         6. ROLE-SPECIFIC LINKS
         ------------------------------------------------------ -->

    <?php if ($loggedIn): ?>

        <!-- Display navigation links assigned to the user's role -->

        <?php foreach ($menus[$userRole] ?? [] as $key => $url): ?>

            <a href="<?= e($url) ?>" class="nav-link nav-role-link <?= $isActive($url) ? 'active' : '' ?>" <?= $isActive($url) ? 'aria-current="page"' : '' ?>>

                <?= e(t($key)) ?>

            </a>

        <?php endforeach; ?>

    <?php endif; ?>

</nav>


<!-- ==========================================================
     7. NAVIGATION ACTIONS
     Login or logout buttons.
     ========================================================== -->

<div class="nav-actions">


    <!-- ------------------------------------------------------
         8. LOGIN / LOGOUT
         ------------------------------------------------------ -->

    <?php if ($loggedIn): ?>

        <!-- Display the user's name -->
        <span class="nav-user">
            <?= e($userName) ?>
        </span>


        <!-- Logout form uses POST for security -->
        <form action="/bedlink/logout.php" method="POST" class="logout-form">

            <!-- CSRF protection -->
            <?= csrfField() ?>

            <!-- Logout button -->
            <button type="submit" class="nav-action-login">
                <?= e(t('nav.logout')) ?>
            </button>

        </form>

    <?php else: ?>

        <!-- Login link for visitors -->

        <a href="/bedlink/login.php" class="nav-action-login">

            <!-- User icon -->
            <span aria-hidden="true">

                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                    <circle cx="12" cy="8" r="4" />
                    <path d="M5 21v-2a7 7 0 0 1 14 0v2" />

                </svg>

            </span>

            <?= e(t('nav.login')) ?>

        </a>

    <?php endif; ?>

</div>