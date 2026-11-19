<?php
// index.php — BedLink homepage

require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Find a bed';
$bodyClass = '';

require __DIR__ . '/includes/header.php';
?>

<main class="container">

  <section class="card">
    <h1><?= e(t('pub.search.title')) ?></h1>

    <p>
      <?= e(tn('pub.card.free', 1)) ?>
    </p>

    <p>
      <?= e(t('help.button')) ?>
    </p>

    <p>
      Language now: <?= e(currentLang()) ?>
    </p>
  </section>

</main>
<?php require __DIR__ . '/includes/footer.php'; ?>