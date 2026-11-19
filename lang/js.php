<?php
// ==========================================================
// BEDLINK JAVASCRIPT LANGUAGE FILE
// Makes PHP translation words available to JavaScript.
// ==========================================================


// ----------------------------------------------------------
// 1. LOAD REQUIRED FILES
// ----------------------------------------------------------

// Load authentication, session and helper functions.
require_once __DIR__ . '/../includes/auth.php';

// Load language selection and translation functions.
require_once __DIR__ . '/../includes/i18n.php';


// ----------------------------------------------------------
// 2. TELL THE BROWSER THIS IS JAVASCRIPT
// ----------------------------------------------------------

header('Content-Type: application/javascript; charset=UTF-8');


// ----------------------------------------------------------
// 3. DETERMINE THE CURRENT LANGUAGE
// ----------------------------------------------------------

$lang = currentLang();


// ----------------------------------------------------------
// 4. LOAD ENGLISH AND SELECTED LANGUAGE
// ----------------------------------------------------------

// English is our master translation file and fallback.
$english = require __DIR__ . '/en.php';

// Build the path to the selected language file.
$selectedFile = __DIR__ . '/' . $lang . '.php';

// Only load another language file if the language
// is not English and the file actually exists.
$selected = ($lang !== 'en' && file_exists($selectedFile))
    ? require $selectedFile
    : [];


// English fills missing translations.
// The selected language overrides English where available.
$words = array_merge($english, $selected);


// ----------------------------------------------------------
// 5. SEND TRANSLATIONS TO JAVASCRIPT
// ----------------------------------------------------------

// JavaScript can access translations through window.L.
echo 'window.L = ' . json_encode(
    $words,
    JSON_UNESCAPED_UNICODE
) . ';';