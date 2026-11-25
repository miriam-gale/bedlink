<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/i18n.php';

$pageTitle = t('pub.search.title');
$bodyClass = 'page-search home-page';

include __DIR__ . '/includes/header.php';
?>

<main class="homepage">

    <!-- Emergency alert -->
    <section class="home-emergency-alert">
        <a href="tel:112" class="emergency-link">
            <span class="emergency-icon">
                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                    19.79 19.79 0 0 1-8.63-3.07
                    19.5 19.5 0 0 1-6-6
                    A19.79 19.79 0 0 1 2.12 4.18
                    2 2 0 0 1 4.11 2h3
                    a2 2 0 0 1 2 1.72
                    c.12.92.33 1.82.61 2.68
                    a2 2 0 0 1-.45 2.11L7.91 9.87
                    a16 16 0 0 0 6 6l1.36-1.36
                    a2 2 0 0 1 2.11-.45
                    c.86.28 1.76.49 2.68.61
                    a2 2 0 0 1 1.94 2.25z"/>
                </svg>
            </span>

                       <span>
                <strong><?= e(t('footer.emergency')) ?></strong>
                <span class="emergency-separator">·</span>
                <?= e(t('footer.call')) ?> 112
            </span>

            <span class="emergency-pulse"></span>
        </a>
    </section>


    <!-- Hero section -->
    <section class="homepage-hero">

        <div class="homepage-hero-content">

                     <div class="hero-eyebrow"><?= e(t('pub.hero.eyebrow')) ?></div>

            <h1>
                <?= e(t('pub.hero.title')) ?>
                <span class="hero-highlight"><?= e(t('pub.hero.near')) ?></span>
            </h1>

            <p class="hero-description"><?= e(t('pub.hero.sub')) ?></p>

            <!-- Location status -->
            <div class="location-status" id="location-status">

                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 5-8 12-8 12s-8-7-8-12a8 8 0 1 1 16 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>

                                <span id="location-text"><?= e(t('pub.search.gps_wait')) ?></span>

                <span class="location-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>

            </div>

        </div>


        <!-- Actual hero image -->
        <div class="homepage-hero-art">

            <div class="hero-art-background"></div>

           <img
    src="/bedlink/assets/img/homepage-healthcare-hero.png"
    alt="<?= e(t('pub.hero.img_alt')) ?>"
    class="hero-healthcare-image"
    loading="eager"
>

            <div class="hero-floating-decoration hero-decoration-one">
                +
            </div>

            <div class="hero-floating-decoration hero-decoration-two">
                +
            </div>

        </div>

    </section>


    <!-- Hospital search card -->
    <section class="bed-search-card" id="bed-search-section">

        <form id="bed-search-form">

            <!-- Care type -->
            <div class="care-type-field">

                <h2><?= e(t('pub.search.ward')) ?></h2>

                <div class="care-type-options">

                    <!-- Emergency -->
                    <button
                        type="button"
                        class="care-chip active"
                        data-ward="emergency"
                        aria-pressed="true">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14"/>
                            <path d="M5 12h14"/>
                        </svg>

                        	<span><?= e(t('ward.emergency')) ?></span>
                    </button>


                    <!-- Maternity -->
                    <button
                        type="button"
                        class="care-chip"
                        data-ward="maternity"
                        aria-pressed="false">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="7" r="3"/>
                            <path d="M5 21l3-6 4-2 4 2 3 6"/>
                            <path d="M8 15l-3-3"/>
                            <path d="M16 15l3-3"/>
                        </svg>

                        <span><?= e(t('ward.maternity')) ?></span>
                    </button>


                    <!-- Children -->
                    <button
                        type="button"
                        class="care-chip"
                        data-ward="children"
                        aria-pressed="false">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
                        </svg>

                       <span><?= e(t('ward.children')) ?></span>
                    </button>


                    <!-- General -->
                    <button
                        type="button"
                        class="care-chip"
                        data-ward="general"
                        aria-pressed="false">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 18V7"/>
                            <path d="M21 18V7"/>
                            <path d="M3 11h18"/>
                            <path d="M7 11V8"/>
                            <path d="M3 18h18"/>
                        </svg>

                        <span><?= e(t('ward.general')) ?></span>
                    </button>


                    <!-- ICU -->
                    <button
                        type="button"
                        class="care-chip"
                        data-ward="icu"
                        aria-pressed="false">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0
                            L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8
                            7.8L12 21l8.9-8.6a5.5 5.5 0 0 0-.1-7.8z"/>
                        </svg>

                        <span><?= e(t('ward.icu')) ?></span>
                    </button>

                </div>

            </div>


            <!-- Search filters -->
            <div class="search-controls">

                <!-- Town -->
                <div class="search-control town-control">

                    <label for="town-input">
                       <?= e(t('pub.search.town')) ?>
                    </label>

                    <div class="control-input-wrapper">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 10c0 5-8 12-8 12s-8-7-8-12a8 8 0 1 1 16 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>

                        <input
                            type="text"
                            id="town-input"
                            name="town"
                            placeholder="<?= e(t('pub.search.town_ph')) ?>"
                            autocomplete="address-level2"
                        >

                    </div>

                </div>


                <!-- Must have -->
                <div class="search-control service-control">

                    <label for="service-filter">
                        <?= e(t('pub.search.service')) ?>
                    </label>

                    <div class="control-input-wrapper">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3 3 8l9 5 9-5-9-5z"/>
                            <path d="m3 12 9 5 9-5"/>
                            <path d="m3 16 9 5 9-5"/>
                        </svg>

                        <select
                            id="service-filter"
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

                </div>


                <!-- NHIS filter -->
                <div class="search-control nhis-control">

                    <label class="nhis-checkbox-label">

                        <input
                            type="checkbox"
                            id="nhis-filter"
                            name="nhis_only"
                            value="1"
                        >

                        <span class="custom-checkbox"></span>

                        <span class="nhis-label-text">
                           <?= e(t('pub.search.nhis')) ?>
                        </span>

                    </label>

                </div>


                <!-- Search button -->
                <div class="search-control button-control">

                    <button
                        type="submit"
                        id="search-button"
                        class="find-bed-button">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m20 20-4-4"/>
                        </svg>

                    <span><?= e(t('nav.search')) ?></span>

                    </button>

                </div>

            </div>

        </form>

    </section>


    <!-- Features -->
    <section class="homepage-features">

        <!-- Feature 1 -->
        <article class="homepage-feature">

            <div class="feature-icon feature-purple">

                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="m13 2-3 8h7l-6 12 2-9H6l7-11z"/>
                </svg>

            </div>

            <div class="feature-text">

                <h3><?= e(t('pub.feat.live.title')) ?></h3>

                <p>
                   <?= e(t('pub.feat.live.desc')) ?> 
                </p>

            </div>

        </article>


        <!-- Feature 2 -->
        <article class="homepage-feature">

            <div class="feature-icon feature-teal">

                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>

            </div>

            <div class="feature-text">

                <h3><?= e(t('pub.feat.trust.title')) ?></h3>

                <p>
                   <?= e(t('pub.feat.trust.desc')) ?>
                </p>

            </div>

        </article>


        <!-- Feature 3 -->
        <article class="homepage-feature">

            <div class="feature-icon feature-purple">

                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0
                    L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8
                    7.8L12 21l8.9-8.6a5.5 5.5 0 0 0-.1-7.8z"/>
                </svg>

            </div>

            <div class="feature-text">

                <h3><?= e(t('pub.feat.fast.title')) ?></h3>

                <p>
                  <?= e(t('pub.feat.fast.desc')) ?>
                </p>

            </div>

        </article>

    </section>


    <!-- Hospital results -->
    <section class="hospital-results-section" id="hospital-results">

        <div class="results-heading" id="results-heading" hidden>

            <h2><?= e(t('pub.results.title')) ?></h2>

            <p>
                <?= e(t('pub.results.sub')) ?>
            </p>

        </div>


        <!-- Results list -->
        <div id="hospital-list" class="hospital-list">

        </div>

                <p id="results-empty" class="results-none" hidden><?= e(t('pub.search.none')) ?></p>


        <!-- Empty state -->
        <div class="empty-results" id="empty-results">

            <div class="empty-results-icon">

                <svg viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="3" width="14" height="18" rx="2"/>
                    <path d="M9 7h6"/>
                    <path d="M9 11h6"/>
                    <path d="M9 15h3"/>
                </svg>

            </div>

            <p>
               <?= e(t('pub.results.start')) ?>
            </p>

        </div>


        <!-- Hospital card template -->
        <template id="hospital-card">

            <article class="hospital-card">

                <div class="hospital-card-top">

                    <div class="hospital-card-info">

                        <h3 class="hc-name">
                            Hospital name
                        </h3>

                        <p class="hc-town">
                            Town
                        </p>

                    </div>

                    <span class="hc-nhis nhis-badge" hidden>
                        	<?= e(t('pub.card.nhis')) ?>
                    </span>

                </div>


                <div class="hospital-card-bed-info">

                    <div class="hospital-bed-count">

                        <span class="bed-count-number hc-beds">
                            0
                        </span>

                       

                    </div>

                </div>


                <div class="hospital-card-meta">

                    <span class="hc-dist">
                        Distance
                    </span>

                    <span class="hc-updated">
                        Updated
                    </span>

                </div>


                <div class="hospital-card-actions">

                    <a href="#" class="hc-call hospital-call-button">

                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                            19.79 19.79 0 0 1-8.63-3.07
                            19.5 19.5 0 0 1-6-6
                            A19.79 19.79 0 0 1 2.12 4.18
                            2 2 0 0 1 4.11 2h3
                            a2 2 0 0 1 2 1.72
                            c.12.92.33 1.82.61 2.68
                            a2 2 0 0 1-.45 2.11L7.91 9.87
                            a16 16 0 0 0 6 6l1.36-1.36
                            a2 2 0 0 1 2.11-.45
                            c.86.28 1.76.49 2.68.61
                            a2 2 0 0 1 1.94 2.25z"/>
                        </svg>

                       <?= e(t('pub.card.call')) ?>

                    </a>

                </div>

            </article>

        </template>

    </section>

</main>


<!-- Page script -->
<script src="/bedlink/assets/js/search.js" defer></script>

<?php
include __DIR__ . '/includes/footer.php';
?>
