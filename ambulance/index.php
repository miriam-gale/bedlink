
<?php
require_once __DIR__ . '/../includes/auth.php';
// requireRole(['ambulance_crew', 'community_driver']);

$pageTitle = 'Ambulance';
$bodyClass = 'dark';

require __DIR__ . '/../includes/header.php';
?>

<main class="container amb">

    <!-- HERO SECTION -->
    <section class="amb-hero">

        <div class="amb-hero__content">
            <h1>Find the best hospital</h1>
            <p>Choose what your patient needs.</p>
        </div>

        <img
            src="/bedlink/assets/img/ambulance-hero.png"
            alt="BedLink emergency ambulance"
            class="amb-hero-image"
        >

    </section>


    <!-- GPS LOCATION -->
    <p id="gps-line" class="gps" aria-live="polite">
        <?= t('amb.gps_wait') ?>
    </p>

    <label id="town-box" hidden>
        <?= t('amb.town') ?>

        <input
            id="town"
            type="text"
            list="towns"
            autocomplete="off"
        >

        <datalist id="towns"></datalist>
    </label>


    <!-- HOSPITAL SEARCH FORM -->
    <form id="amb-form">

        <input type="hidden" id="lat">
        <input type="hidden" id="lng">


        <!-- WARD SELECTION -->
        <fieldset class="chips amb-ward-chips">

            <legend><?= t('amb.ward') ?></legend>

            <label>
                <input
                    type="radio"
                    name="ward"
                    value="emergency"
                    checked
                >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 20V8h3v8h15v4H3Z"/>
                    <path d="M7 12h14M7 8h14M12 4h4M14 2v4"/>
                </svg>

                <span><?= t('ward.emergency') ?></span>
            </label>


            <label>
                <input
                    type="radio"
                    name="ward"
                    value="maternity"
                >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="5" r="3"/>
                    <path d="M8 22v-5a4 4 0 0 1 8 0v5"/>
                    <path d="M8 13l4 3 4-3"/>
                    <path d="M17 8c2 0 3 1 3 3"/>
                </svg>

                <span><?= t('ward.maternity') ?></span>
            </label>


            <label>
                <input
                    type="radio"
                    name="ward"
                    value="icu"
                >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="2" y="3" width="20" height="18" rx="2"/>
                    <path d="M4 12h4l2-4 3 8 2-4h5"/>
                </svg>

                <span><?= t('ward.icu') ?></span>
            </label>


            <label>
                <input
                    type="radio"
                    name="ward"
                    value="children"
                >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="8" cy="7" r="3"/>
                    <circle cx="17" cy="7" r="3"/>
                    <path d="M2 21v-2a6 6 0 0 1 12 0v2"/>
                    <path d="M14 14a6 6 0 0 1 8 5v2"/>
                </svg>

                <span><?= t('ward.children') ?></span>
            </label>

        </fieldset>


        <!-- SERVICES NEEDED -->
        <fieldset class="chips amb-service-chips">

            <legend><?= t('amb.needs') ?></legend>

            <label>
                <input
                    type="checkbox"
                    name="svc"
                    value="ct_scan"
                >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 3v2M12 19v2M3 12h2M19 12h2"/>
                </svg>

                <span><?= t('svc.ct_scan') ?></span>
            </label>


            <label>
                <input
                    type="checkbox"
                    name="svc"
                    value="trauma_surgery"
                >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/>
                    <path d="M12 7v8M8 11h8"/>
                </svg>

                <span><?= t('svc.trauma_surgery') ?></span>
            </label>


            <label>
                <input
                    type="checkbox"
                    name="svc"
                    value="theatre"
                >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 21h18M5 17h14M7 17v4M17 17v4"/>
                    <path d="M8 3h8M12 3v5M5 8h14M7 8v5M17 8v5"/>
                    <circle cx="12" cy="11" r="2"/>
                </svg>

                <span><?= t('svc.theatre') ?></span>
            </label>


            <label>
                <input
                    type="checkbox"
                    name="svc"
                    value="icu"
                >

                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="2" y="3" width="20" height="16" rx="2"/>
                    <path d="M4 11h4l2-4 3 8 2-4h5M8 22h8M12 19v3"/>
                </svg>

                <span><?= t('svc.icu') ?></span>
            </label>

        </fieldset>


        <!-- BLOOD REQUIREMENTS -->
        <fieldset>

            <legend><?= t('amb.blood') ?></legend>

            <div class="grid-2">

                <!-- BLOOD TYPE -->
                <label>
                    <?= t('amb.bloodty') ?>

                    <select name="blood">

                        <option value="">
                            <?= t('amb.no_blood') ?>
                        </option>

                        <?php foreach (BLOOD_TYPES as $bt): ?>

                            <option value="<?= e($bt) ?>">
                                <?= e($bt) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </label>


                <!-- BLOOD QUANTITY -->
                <label class="amb-units-label">

                    <?= t('amb.units') ?>

                    <div class="amb-quantity">

                        <button
                            type="button"
                            class="amb-quantity-btn"
                            id="units-minus"
                            aria-label="Decrease blood units"
                        >−</button>


                        <input
                            type="number"
                            name="units"
                            id="units-input"
                            min="0"
                            max="10"
                            value="0"
                            inputmode="numeric"
                            aria-label="<?= t('amb.units') ?>"
                        >


                        <button
                            type="button"
                            class="amb-quantity-btn"
                            id="units-plus"
                            aria-label="Increase blood units"
                        >+</button>

                    </div>

                </label>

            </div>

        </fieldset>


        <!-- FIND HOSPITAL BUTTON -->
        <button
            class="btn btn-big btn--primary"
            type="submit"
        >
            <?= t('amb.find') ?>
        </button>

    </form>

</main>


<!-- EXISTING AMBULANCE JAVASCRIPT -->
<script src="/bedlink/assets/js/ambulance.js" defer></script>


<!-- EXISTING FOOTER -->
<?php require __DIR__ . '/../includes/footer.php'; ?>
