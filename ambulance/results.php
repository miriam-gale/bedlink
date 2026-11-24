<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/i18n.php';
// requireRole(['ambulance_crew', 'community_driver']);

$matchesJsonPath = __DIR__ . '/../assets/mock/matches.json';
$matchesPayload = [
    'ok' => false,
    'data' => [
        'radius_km' => 20,
        'coordinator_told' => false,
        'results' => [],
    ],
];

if (is_readable($matchesJsonPath)) {
    $rawJson = file_get_contents($matchesJsonPath);
    $decoded = json_decode($rawJson ?: '', true);

    if (is_array($decoded)) {
        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $matchesPayload = [
                'ok' => isset($decoded['ok']) ? (bool) $decoded['ok'] : true,
                'data' => array_merge($matchesPayload['data'], $decoded['data']),
            ];
        } else {
            $matchesPayload = [
                'ok' => true,
                'data' => array_merge($matchesPayload['data'], $decoded),
            ];
        }
    }
}

$pageTitle = t('amb.results_title');
$bodyClass = 'page-amb-results';
require __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="/bedlink/assets/css/style.css">

<main
    class="container amb-results"
    data-amb-matches="<?= e(json_encode($matchesPayload, JSON_UNESCAPED_SLASHES)) ?>"
>

    <header class="amb-results__header">
        <div class="amb-results__title-wrap">
            <img
                class="amb-results__hero-img amb-results__hero-img--dark"
                src="/bedlink/assets/img/ambulance-hero-dark.png"
                alt=""
                aria-hidden="true"
            >
            <h1><?= e(t('amb.results_title')) ?></h1>
        </div>
        <span class="amb-results__radius">
            <?= e((string) ($matchesPayload['data']['radius_km'] ?? 20)) ?> km
        </span>
    </header>

    <!-- Hospital cards populated by amb-results.js -->
    <div id="amb-list" class="amb-results__list"></div>

    <!-- Shown only when NO hospital is found -->
    <div id="amb-empty" class="amb-results__empty" hidden>
        <p id="amb-empty-text"></p>
        <p id="amb-coordinator" hidden><?= e(t('amb.coordinator_told')) ?></p>
        <button type="button" id="amb-widen" class="btn"><?= e(t('amb.widen')) ?></button>
    </div>

    <!-- Card template cloned by amb-results.js -->
    <template id="amb-card">
        <article class="amb-card">
            <div class="amb-card__photo-wrap">
                <img
                    class="amb-card__image"
                    src=""
                    alt=""
                    loading="lazy"
                >
            </div>
            <div class="amb-card__body">
                <h2 class="amb-card__name"></h2>
                <p class="amb-card__town"></p>
                <dl class="amb-card__metrics">
                    <div>
                        <dt>ETA</dt>
                        <dd class="amb-card__eta"></dd>
                    </div>
                    <div>
                        <dt>Beds</dt>
                        <dd class="amb-card__beds"></dd>
                    </div>
                    <div>
                        <dt>Updated</dt>
                        <dd class="amb-card__updated"></dd>
                    </div>
                </dl>
            </div>
            <div class="amb-card__actions">
                <button type="button" class="btn btn--primary amb-card__hold"><?= e(t('amb.hold')) ?></button>
                <a class="btn amb-card__call" href="#"><?= e(t('ui.call')) ?></a>
                <a class="btn amb-card__dir" href="#" target="_blank" rel="noopener"><?= e(t('ui.directions')) ?></a>
            </div>
        </article>
    </template>

     

</main>
<script src="/bedlink/assets/js/amb-results.js" defer></script>
<?php require __DIR__ . '/../includes/footer.php'; ?>