<?php
// ==========================================================
// BEDLINK FOOTER
// Shared footer for public-facing pages.
// ==========================================================
?>

<!--  1. MAIN FOOTER -->

<footer class="site-footer">

    <div class="footer-inner">

        <!-- 2. BRAND AND EMERGENCY SECTION
            -->

        <div class="footer-brand-section">

            <!-- BedLink logo -->
            <a href="/bedlink/" class="footer-brand" aria-label="BedLink Home">

                <img
                    src="/bedlink/assets/img/logo.png"
                    alt="BedLink logo"
                    class="footer-logo"
                >

            </a>


            <!-- BedLink tagline -->
            <h2 class="footer-tagline">
                Connecting people to available care, faster.
            </h2>


            <!-- Short description -->
            <p class="footer-description">
                Supporting care coordination across Ghana.
            </p>


            <!-- Emergency call button -->
            <a href="tel:112" class="footer-emergency">

                <!-- Phone icon -->
                <span class="footer-emergency-icon" aria-hidden="true">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="32"
                        height="32"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2
                                 19.79 19.79 0 0 1-8.63-3.07
                                 19.5 19.5 0 0 1-6-6
                                 19.79 19.79 0 0 1-3.07-8.63
                                 A2 2 0 0 1 2.12 2h3
                                 a2 2 0 0 1 2 1.72
                                 c.12.89.33 1.76.62 2.6
                                 a2 2 0 0 1-.45 2.11L6.09 9.63
                                 a16 16 0 0 0 6 6l1.2-1.2
                                 a2 2 0 0 1 2.11-.45
                                 c.84.29 1.71.5 2.6.62
                                 A2 2 0 0 1 22 16.92z"/>
                    </svg>

                </span>


                <!-- Emergency text -->
                <span class="footer-emergency-text">

                    <strong>Call 112</strong>

                    <span>Emergency assistance</span>

                </span>

            </a>

        </div>


        <!--3. EXPLORE BEDLINK SECTION-->

        <div class="footer-explore-section">

            <h2 class="footer-explore-title">
                Explore BedLink
            </h2>


            <!-- Decorative teal accent -->
            <div class="footer-accent"></div>


            <!-- Find a Bed link -->
            <a href="/bedlink/" class="footer-find-bed">

                <!-- Bed icon -->
                <span class="footer-bed-icon" aria-hidden="true">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="40"
                        height="40"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M2 4v16"/>
                        <path d="M2 8h20"/>
                        <path d="M2 17h20"/>
                        <path d="M4 8v9"/>
                        <path d="M22 8v9"/>
                        <path d="M4 12h18"/>
                    </svg>

                </span>


                <!-- Find a Bed text -->
                <span class="footer-find-bed-text">

                    <strong>Find a Bed</strong>

                    <span>Search for available care</span>

                </span>

            </a>

        </div>

    </div>


    <!-- 4. COPYRIGHT BAR -->

    <div class="footer-bottom">

        <p>
            &copy; <?= date('Y') ?> BedLink Ghana
        </p>

    </div>

</footer>


<!--  5. CLOSE THE HTML DOCUMENT -->

</body>
</html>