<?php
// index.php — BedLink homepage

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/i18n.php';

$pageTitle = t('pub.search.title');
$bodyClass = 'page-search';

require __DIR__ . '/includes/header.php';
?>

<main class="find-bed-page">

    <!-- Emergency alert -->
    <div class="emergency-alert">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <path d="M6.6 3.5a2 2 0 0 0-2.8 0L2.5 4.8
                     c-.8.8-.7 2.2.1 3.3 3.8 5.3 8.1 9.6
                     13.4 13.4 1.1.8 2.5.9 3.3.1l1.3-1.3
                     a2 2 0 0 0 0-2.8l-3-3a2 2 0 0 0-2.8 0
                     l-1.2 1.2a14 14 0 0 1-5.6-5.6l1.2-1.2
                     a2 2 0 0 0 0-2.8z"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.7"
                  stroke-linecap="round"
                  stroke-linejoin="round"/>
        </svg>
        <span><?= e(t('footer.emergency')) ?> · <?= e(t('footer.call')) ?> 112</span>
    </div>

    <!-- Main heading area -->
    <section class="find-bed-hero">

        <div class="find-bed-content">

            <h1><?= e(t('pub.search.title')) ?></h1>

            <!-- Location status -->
            <p id="location-status">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="1.8"/>
                    <circle cx="12" cy="10" r="2.5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"/>
                </svg>
                <?= e(t('pub.search.gps_wait')) ?>
            </p>

            <!-- Town fallback -->
            <div class="town-field" hidden>
                <label for="town"><?= e(t('pub.search.town')) ?></label>
                <input
                    type="text"
                    id="town"
                    name="town"
                    placeholder="<?= e(t('pub.search.town')) ?>"
                >
            </div>

        </div>

        <!-- Hospital illustration -->
        <div class="find-bed-illustration" aria-hidden="true">

            <svg class="hospital-illustration"
                 viewBox="0 0 240 200"
                 xmlns="http://www.w3.org/2000/svg">

                <!-- Hospital building -->
                <rect x="48" y="35" width="144" height="145"
                      rx="12" fill="#EDE7FF"/>

                <rect x="65" y="55" width="110" height="125"
                      rx="6" fill="#FFFFFF"/>

                <!-- Hospital sign -->
                <rect x="91" y="22" width="58" height="43"
                      rx="10" fill="#6D45D8"/>

                <path d="M120 31v25M107.5 43.5h25"
                      stroke="#FFFFFF"
                      stroke-width="6"
                      stroke-linecap="round"/>

                <!-- Windows -->
                <rect x="79" y="77" width="22" height="22"
                      rx="5" fill="#D8CCFF"/>
                <rect x="139" y="77" width="22" height="22"
                      rx="5" fill="#D8CCFF"/>

                <rect x="79" y="111" width="22" height="22"
                      rx="5" fill="#D8CCFF"/>
                <rect x="139" y="111" width="22" height="22"
                      rx="5" fill="#D8CCFF"/>

                <!-- Door -->
                <rect x="106" y="135" width="28" height="45"
                      rx="6" fill="#6D45D8"/>

                <circle cx="128" cy="157" r="2"
                        fill="#FFFFFF"/>

                <!-- Ground -->
                <path d="M25 180h190"
                      stroke="#B9A8EF"
                      stroke-width="4"
                      stroke-linecap="round"/>

            </svg>

        </div>

    </section>

    <!-- Bed search form -->
    <section class="bed-search-section">

        <form id="bed-search-form">

            <!-- Care type -->
            <fieldset class="care-type-field">

                <legend><?= e(t('pub.search.ward')) ?></legend>

                <div class="care-type-options">

                    <button type="button"
                            class="care-chip active"
                            data-ward="emergency">

                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 3v18M3 12h18"
                                  stroke="currentColor"
                                  stroke-width="2.5"
                                  stroke-linecap="round"/>
                        </svg>

                       <?= e(t('ward.emergency')) ?>
                    </button>

                    <button type="button"
                            class="care-chip"
                            data-ward="maternity">

                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <circle cx="12" cy="6" r="3"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"/>
                            <path d="M8 21v-5l-3-4
                                     a2 2 0 0 1 3-2l4 3
                                     4-3a2 2 0 0 1 3 2l-3 4v5"
                                  fill="none"
                                  stroke="currentColor"
                                  stroke-width="1.8"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <?= e(t('ward.maternity')) ?>
                    </button>

                    <button type="button"
                            class="care-chip"
                            data-ward="children">

                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <circle cx="12" cy="8" r="4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"/>
                            <path d="M5 21v-2a7 7 0 0 1 14 0v2"
                                  fill="none"
                                  stroke="currentColor"
                                  stroke-width="1.8"
                                  stroke-linecap="round"/>
                        </svg>

                        <?= e(t('ward.children')) ?>
                    </button>

                    <button type="button"
                            class="care-chip"
                            data-ward="general">

                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M3 18V8a2 2 0 0 1 2-2h14
                                     a2 2 0 0 1 2 2v10
                                     M3 13h18
                                     M7 10h3
                                     M8.5 8.5v3"
                                  fill="none"
                                  stroke="currentColor"
                                  stroke-width="1.8"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>

                        <?= e(t('ward.general')) ?>
                    </button>

                    <button type="button"
                            class="care-chip"
                            data-ward="icu">

                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 21s-8-5-8-11
                                     a4.5 4.5 0 0 1 8-3
                                     a4.5 4.5 0 0 1 8 3
                                     c0 6-8 11-8 11Z"
                                  fill="none"
                                  stroke="currentColor"
                                  stroke-width="1.8"
                                  stroke-linejoin="round"/>
                        </svg>

                        <?= e(t('ward.icu')) ?>
                    </button>

                </div>

            </fieldset>

            <!-- Search filters -->
            <div class="search-filters">

                <!-- Required service -->
                <div class="service-filter">

                    <label for="required-service">
                        <?= e(t('pub.search.service')) ?>
                    </label>

                    <select id="required-service"
                            name="service">

                        <option value=""><?= e(t('pub.search.any')) ?></option>
<option value="ct_scan"><?= e(t('svc.ct_scan')) ?></option>
<option value="theatre"><?= e(t('svc.theatre')) ?></option>
<option value="trauma_surgery"><?= e(t('svc.trauma_surgery')) ?></option>
<option value="blood_bank"><?= e(t('svc.blood_bank')) ?></option>
<option value="icu"><?= e(t('svc.icu')) ?></option>
<option value="obstetrics"><?= e(t('svc.obstetrics')) ?></option>

                    </select>

                </div>

                <!-- NHIS filter -->
                <div class="nhis-filter">

                    <label for="nhis-only">

                        <input
                            type="checkbox"
                            id="nhis-only"
                            name="nhis_only"
                        >

                        <span>
                           <?= e(t('pub.search.nhis')) ?>
                        </span>

                    </label>

                </div>

                <!-- Search button -->
                <button type="submit" id="search-button">

                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <circle cx="10.5" cy="10.5" r="6.5"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"/>
                        <path d="m16 16 5 5"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"/>
                    </svg>

                    <?= e(t('nav.search')) ?>
                </button>

            </div>

        </form>

    </section>

    <!-- Empty results area -->
    <section id="hospital-results"
             class="hospital-results"
             aria-live="polite">

        <div class="empty-results-icon" aria-hidden="true">

            <svg viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg"
                 focusable="false">

                <path d="M3 21V5a2 2 0 0 1 2-2h14
                         a2 2 0 0 1 2 2v16"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                      stroke-linecap="round"/>

                <path d="M9 3v6h6V3
                         M12 4v4
                         M10 6h4"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                      stroke-linecap="round"/>

                <path d="M7 13h10
                         M7 17h10"
                      stroke="currentColor"
                      stroke-width="1.8"
                      stroke-linecap="round"/>

            </svg>

        </div>

    </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>