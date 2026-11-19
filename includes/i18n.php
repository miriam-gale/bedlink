<?php
// includes/i18n.php (Mimi)
// Handles language selection and provides t() and tn().

// Directory containing language files.
const LANG_DIR = __DIR__ . '/../lang';


// ==========================================================
// 1. DETERMINE THE CURRENT LANGUAGE
// ==========================================================

function currentLang(): string {

    // Supported languages.
    $allowed = ['en', 'tw', 'gaa', 'ee'];

    // Process language selection only once per request.
    static $initialized = false;

    if (!$initialized) {
        $initialized = true;

        // Check whether the user selected a supported language.
        if (
            isset($_GET['lang']) &&
            in_array($_GET['lang'], $allowed, true)
        ) {

            // Save the selected language in the session.
            $_SESSION['lang'] = $_GET['lang'];

            // Save the language in a cookie only if headers
            // have not already been sent to the browser.
            if (!headers_sent()) {
                setcookie(
                    'lang',
                    $_GET['lang'],
                    time() + (365 * 24 * 3600),
                    '/'
                );
            }
        }
    }

    // Get the saved language from the session or cookie.
    $lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';

    // Return the language if supported; otherwise English.
    return in_array($lang, $allowed, true) ? $lang : 'en';
}


// ==========================================================
// 2. TRANSLATE A TEXT KEY
// ==========================================================

// Example: t('help.button') → "Get help now"
function t(string $key, array $vars = []): string {

    // Cache the selected language file and English fallback.
    static $cur = null, $en = null, $loadedLang = null;

    // Determine the current language.
    $lang = currentLang();

    // Load the selected language file when necessary.
    if ($loadedLang !== $lang) {
        $file = LANG_DIR . '/' . $lang . '.php';

        $cur = file_exists($file) ? require $file : [];

        $loadedLang = $lang;
    }

    // Load English as the fallback language.
    $en ??= require LANG_DIR . '/en.php';

    // Look up the translation.
    $text = $cur[$key] ?? $en[$key] ?? null;

    // If the key is missing, return the key itself.
    if ($text === null) {
        error_log("Missing text: $key");
        $text = $key;
    }

    // Replace variables inside translated text.
    foreach ($vars as $k => $v) {
        $text = str_replace(
            '{' . $k . '}',
            htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'),
            $text
        );
    }

    return $text;
}


// ==========================================================
// 3. HANDLE SINGULAR AND PLURAL TEXT
// ==========================================================

// Example:
// tn('pub.card.free', 1) → "1 free bed"
// tn('pub.card.free', 3) → "3 free beds"

function tn(string $key, int $n, array $vars = []): string {

    // Select the singular or plural translation.
    $suffix = ($n === 1) ? '.one' : '.other';

    // Include the number as a replacement variable.
    return t($key . $suffix, $vars + ['n' => $n]);
}