
<?php
// includes/header.php

// Load translations
require_once __DIR__ . '/i18n.php';

// Determine the selected language before HTML output begins.
$lang = currentLang();

// Restore the saved theme before rendering the page.

$bodyClasses = preg_split('/\s+/', trim($bodyClass ?? ''));
$bodyClasses = array_filter($bodyClasses);

$defaultDark = in_array('dark', $bodyClasses, true);
$savedTheme  = $_COOKIE['theme'] ?? '';

$isDark = $savedTheme === 'dark'
    || ($savedTheme === '' && $defaultDark);

$bodyClasses = array_values(array_filter(
    $bodyClasses,
    fn($class) => $class !== 'dark'
));

if ($isDark) {
    $bodyClasses[] = 'dark';
}

$bodyClass = implode(' ', $bodyClasses);

// Security headers
header(
    "Content-Security-Policy: default-src 'self'; "
    . "img-src 'self' data: https://*.tile.openstreetmap.org; "
    . "style-src 'self' https://fonts.googleapis.com; "
    . "font-src https://fonts.gstatic.com; "
    . "connect-src 'self'; "
    . "frame-ancestors 'none'"
);

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
?>

<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf" content="<?= e($_SESSION['csrf'] ?? '') ?>">

    <title><?= e($pageTitle ?? 'BedLink') ?> · BedLink</title>

    

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@700&display=swap">

    <link rel="stylesheet" href="/bedlink/assets/css/style.css">

    <script src="/bedlink/lang/js.php?lang=<?= e($lang) ?>" defer></script>
    <script src="/bedlink/assets/js/app.js" defer></script>
</head>

<body class="<?= e($bodyClass ?? '') ?>">

<header class="site-header">

    <div class="header-inner">

        <!-- BedLink logo -->
        <a href="/bedlink/" class="brand" aria-label="BedLink Home">
            <img
                src="/bedlink/assets/img/logo.png"
                alt="BedLink logo"
                class="brand-logo"
            >
        </a>

        <!-- Role-based navigation -->
        <?php require __DIR__ . '/nav.php'; ?>

        <!-- Header controls -->
        <div class="header-controls">

            <!-- Theme toggle -->
            <?php if (empty($isAmbulancePage)): ?>
                <button
                    type="button"
                    id="theme-toggle"
                    class="theme-toggle"
                    aria-label="Toggle dark mode"
                    title="Toggle dark mode"
                >
                    <!-- Moon icon -->
                    <svg
                        class="theme-icon theme-icon--moon"
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6.5 6.5 0 0 0 8.268 8.268c.344-.215.825-.003.803.401"/>
                    </svg>

                    <!-- Sun icon -->
                    <svg
                        class="theme-icon theme-icon--sun"
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2"/>
                        <path d="M12 20v2"/>
                        <path d="m4.93 4.93 1.42 1.42"/>
                        <path d="m17.65 17.65 1.42 1.42"/>
                        <path d="M2 12h2"/>
                        <path d="M20 12h2"/>
                        <path d="m6.35 17.65-1.42 1.42"/>
                        <path d="m19.07 4.93-1.42 1.42"/>
                    </svg>
                </button>
            <?php endif; ?>

            <!-- Language selector -->
            <div class="language-selector">
                <label for="language-select" class="sr-only">
                    <?= e(t('nav.language')) ?>
                </label>

                <select
                    id="language-select"
                    class="language-select"
                    aria-label="<?= e(t('nav.language')) ?>"
                >
                    <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>
                        English
                    </option>

                    <option value="tw" <?= $lang === 'tw' ? 'selected' : '' ?>>
                        Twi
                    </option>

                    <option value="gaa" <?= $lang === 'gaa' ? 'selected' : '' ?>>
                        Ga
                    </option>

                    <option value="ee" <?= $lang === 'ee' ? 'selected' : '' ?>>
                        Ewe
                    </option>
                </select>
            </div>

        </div>
    </div>

</header>

<!-- Offline notification -->
<div id="offline-banner" class="banner" hidden></div>

