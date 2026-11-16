<?php
// includes/i18n.php (Mimi) — the middleman: gives every page t() and tn()
const LANG_DIR = __DIR__ . '/../lang';

// Which language did the user choose? Only ever one of these 4.
function currentLang(): string {
    $allowed = ['en', 'tw', 'gaa', 'ee'];
    if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed, true)) {
        $_SESSION['lang'] = $_GET['lang'];
        setcookie('lang', $_GET['lang'], time() + 365 * 24 * 3600, '/');   // remember for a year
    }
    $lang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'en';
    return in_array($lang, $allowed, true) ? $lang : 'en';
}

// t('help.button') → "Get help now" (or the Twi / Ga / Ewe words)
function t(string $key, array $vars = []): string {
    static $cur = null, $en = null;
    $file = LANG_DIR . '/' . currentLang() . '.php';
    $cur ??= file_exists($file) ? require $file : [];
    $en  ??= require LANG_DIR . '/en.php';
    $text = $cur[$key] ?? $en[$key] ?? null;        // their language → English → the key itself
    if ($text === null) { error_log("Missing text: $key"); $text = $key; }
    foreach ($vars as $k => $v) {
        $text = str_replace('{' . $k . '}', htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'), $text);
    }
    return $text;
}

// tn('pub.card.free', 3) → "3 free beds"   ·   tn('pub.card.free', 1) → "1 free bed"
function tn(string $key, int $n, array $vars = []): string {
    return t($key . ($n === 1 ? '.one' : '.other'), $vars + ['n' => $n]);
}