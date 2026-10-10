<?php

$pageTitle = "Page Not Found";
$pageDescription = "The page you're looking for doesn't exist. Explore our Grenada taxi services and island tours instead.";

http_response_code(404);

require_once __DIR__ . "/includes/header.php";

?>

<section class="page-hero">

    <div class="container page-hero-content">

        <p class="section-label">404 — PAGE NOT FOUND</p>

        <h1>This page has taken a wrong turn.</h1>

        <p>
            The link you followed may be broken, or the page may
            have been moved. Let's get you back on track.
        </p>

    </div>

</section>


<section class="content-section">

    <div class="container">

        <div class="error-layout">

            <div class="error-card">

                <div class="error-icon">🧭</div>

                <h2>Where to next?</h2>

                <p>
                    Head back to the homepage, explore our island tours,
                    or send us a message — we'll get you sorted.
                </p>

                <div class="error-actions">

                    <a href="/fetecation/" class="primary-button">
                        Back to Home
                    </a>

                    <a href="/fetecation/tours.php" class="secondary-button">
                        Explore Tours
                    </a>

                </div>

                <div class="error-links">

                    <a href="/fetecation/taxis.php">Taxi Services</a>
                    <a href="/fetecation/booking.php">Book a Ride</a>
                    <a href="/fetecation/contact.php">Contact Us</a>

                </div>

            </div>

        </div>

    </div>

</section>


<?php require_once __DIR__ . "/includes/footer.php"; ?>