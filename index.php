<?php
// index.php — TEST of i18n.php (we replace this with the real search page later)
session_start();
require_once __DIR__ . '/includes/i18n.php';

echo '<h1>' . t('pub.search.title') . '</h1>';
echo '<p>' . tn('pub.card.free', 1) . ' · ' . tn('pub.card.free', 3) . '</p>';
echo '<p>' . t('help.button') . '</p>';
echo '<p>' . t('this.key.does.not.exist') . '</p>';
echo '<p>Language now: ' . currentLang() . '</p>';
echo '<p><a href="?lang=en">English</a> · <a href="?lang=tw">Twi</a></p>';