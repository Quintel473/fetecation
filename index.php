<?php

$pageTitle = "Home";

require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/tours-data.php";
require_once __DIR__ . "/testimonials-data.php";

$tours = fete_all_tours();

/* Grab the first 3 tours for the featured section */
$featuredTours = array_slice($tours, 0, 3, true);

?>

<!-- ============ HOME HERO ============ -->
<section class="home-hero">

    <div class="home-hero-image"></div>
    <div class="home-hero-overlay"></div>

    <div class="container home-hero-content">

        <p class="hero-eyebrow">FETECATION TAXI &amp; TOURS</p>

        <h1>
            Explore. Ride.<br>
            <span>Experience.</span>
        </h1>

        <p class="hero-description">
            Discover unforgettable Caribbean experiences with
            reliable taxi services and exciting island tours —
            built around you, your group and your schedule.
        </p>

        <div class="hero-actions">

            <a href="/fetecation/booking.php" class="hero-primary-button">
                Book a Ride
            </a>

            <a href="/fetecation/tours.php" class="hero-secondary-button">
                Explore Tours
            </a>

        </div>

    </div>

    <div class="hero-scroll">
        <span></span> SCROLL
    </div>

</section>


<!-- ============ INTRO ============ -->
<section class="intro-section">

    <div class="container intro-grid">

        <div class="intro-image">

            <img
                src="/fetecation/images/tours/island-highlights-1.jpg"
                alt="Exploring the island with FeteCation"
            >

            <div class="intro-badge">
                <strong>100%</strong>
                <span>Private experiences</span>
            </div>

        </div>

        <div class="intro-content">

            <p class="section-label">WELCOME TO FETECATION</p>

            <h2>
                Your Journey <span>Starts Here</span>
            </h2>

            <p>
                Whether you need a comfortable ride from the airport
                or a full day exploring hidden beaches and local
                spots, we handle the details so you can focus on
                the experience.
            </p>

            <p>
                Private vehicles, local guides who know every corner
                of the island, and flexible schedules — all designed
                around you.
            </p>

            <a href="/fetecation/about.php" class="outline-button">
                Learn More About Us
            </a>

        </div>

    </div>

</section>


<!-- ============ HOME SERVICES ============ -->
<section class="home-services">

    <div class="container">

        <div class="section-heading left-heading">

            <div>
                <p class="section-label">WHAT WE OFFER</p>
                <h2>
                    Travel With <span>FeteCation</span>
                </h2>
            </div>

            <p>
                From getting around to discovering the island — two
                services, one standard of care.
            </p>

        </div>


        <div class="home-service-grid">

            <!-- Taxi -->
            <div class="home-service-card taxi-service">

                <div class="service-card-image"></div>

                <div class="home-service-content">

                    <span class="service-number">01 — TAXI</span>

                    <h3>Reliable Rides</h3>

                    <p>
                        Airport transfers, hotel pickups, events and
                        everyday travel — always on time, always
                        comfortable.
                    </p>

                    <a href="/fetecation/taxis.php" class="service-link">
                        View Taxi Services →
                    </a>

                </div>

            </div>


            <!-- Tours -->
            <div class="home-service-card tour-service">

                <div class="service-card-image"></div>

                <div class="home-service-content">

                    <span class="service-number">02 — TOURS</span>

                    <h3>Island Tours</h3>

                    <p>
                        Curated experiences, hidden gems and full days
                        designed around what you want to see and do.
                    </p>

                    <a href="/fetecation/tours.php" class="service-link">
                        Explore Tours →
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ============ FEATURED TOURS ============ -->
<section class="featured-tours">

    <div class="container">

        <div class="section-heading centered-heading" style="margin-left:auto;margin-right:auto;text-align:center;">

            <p class="section-label">POPULAR EXPERIENCES</p>

            <h2>Featured Tours</h2>

            <p>
                A few of our most-loved experiences — but every tour
                can be tailored to you.
            </p>

        </div>


        <div class="featured-tour-grid">

            <?php
            $cardIndex = 1;
            foreach ($featuredTours as $slug => $tour):
                $cardClass = 'tour-' . ['one','two','three'][$cardIndex - 1];
                $cardIndex++;
            ?>

                <a href="/fetecation/tours/<?= htmlspecialchars($slug); ?>.php"
                   class="featured-tour-card">

                    <div class="featured-tour-image <?= $cardClass; ?>">
                        <span class="tour-overlay-label">
                            <?= htmlspecialchars($tour['tag']); ?>
                        </span>
                    </div>

                    <div class="featured-tour-content">

                        <h3><?= htmlspecialchars($tour['name']); ?></h3>

                        <p><?= htmlspecialchars($tour['tagline']); ?></p>

                        <span class="service-link" style="color:var(--fete-orange-dark);font-weight:800;">
                            View Tour →
                        </span>

                    </div>

                </a>

            <?php endforeach; ?>

        </div>


        <div class="center-button">
            <a href="/fetecation/tours.php" class="outline-button">
                See All Tours
            </a>
        </div>

    </div>

</section>


<!-- ============ WHY FETECATION ============ -->
<section class="why-section">

    <div class="container why-grid">

        <div class="why-content">

            <p class="section-label">WHY FETECATION</p>

            <h2>
                Travel Made Easy
            </h2>

            <p>
                We're a small local team that cares about your trip —
                not just getting you from A to B.
            </p>

            <div class="why-list">

                <div class="why-item">
                    <span class="why-icon">✓</span>
                    <div>
                        <h3>Local knowledge</h3>
                        <p>Guides and drivers who actually live here and know the island.</p>
                    </div>
                </div>

                <div class="why-item">
                    <span class="why-icon">✓</span>
                    <div>
                        <h3>Private by default</h3>
                        <p>Your ride, your tour, your pace — no shared groups, no rushed stops.</p>
                    </div>
                </div>

                <div class="why-item">
                    <span class="why-icon">✓</span>
                    <div>
                        <h3>Easy communication</h3>
                        <p>Book online or message us on WhatsApp — we reply fast.</p>
                    </div>
                </div>

                <div class="why-item">
                    <span class="why-icon">✓</span>
                    <div>
                        <h3>Flexible everything</h3>
                        <p>Schedules, stops and itineraries — all tailored around you.</p>
                    </div>
                </div>

            </div>

        </div>


        <div class="why-visual">

            <div class="why-visual-inner">

                <img src="/fetecation/images/logo.png" alt="FeteCation">

                <p>RIDE · EXPLORE · ENJOY</p>

            </div>

        </div>

    </div>

</section>


<!-- ============ TESTIMONIALS ============ -->
<section class="testimonials-section">

    <div class="container">

        <div class="section-heading centered-heading" style="margin-left:auto;margin-right:auto;text-align:center;">

            <p class="section-label">WHAT GUESTS SAY</p>

            <h2>Trusted by travellers</h2>

            <p>
                Real experiences from guests who trusted FeteCation
                with their island stay.
            </p>

        </div>

    </div>


    <div class="testimonials-slider-wrap">

        <button type="button"
                class="testimonials-nav testimonials-nav-prev"
                aria-label="Previous testimonial">
            ‹
        </button>

        <div class="testimonials-slider" id="testimonialsSlider">

            <?php foreach ($TESTIMONIALS as $t): ?>

                <figure class="testimonial-card">

                    <div class="testimonial-stars">
                        <?php for ($i = 0; $i < (int)$t['rating']; $i++): ?>
                            ★
                        <?php endfor; ?>
                    </div>

                    <blockquote class="testimonial-text">
                        "<?= htmlspecialchars($t['text']); ?>"
                    </blockquote>

                    <figcaption class="testimonial-author">

                        <?php if (!empty($t['photo'])): ?>

                            <img
                                class="testimonial-avatar testimonial-avatar-img"
                                src="<?= htmlspecialchars($t['photo']); ?>"
                                alt="<?= htmlspecialchars($t['name']); ?>"
                                loading="lazy"
                            >

                        <?php else: ?>

                            <span class="testimonial-avatar">
                                <?= htmlspecialchars(strtoupper(substr($t['name'], 0, 1))); ?>
                            </span>

                        <?php endif; ?>

                        <div>
                            <strong><?= htmlspecialchars($t['name']); ?></strong>
                            <span class="testimonial-location">
                                <?= htmlspecialchars($t['location']); ?>
                            </span>
                        </div>

                    </figcaption>

                </figure>

            <?php endforeach; ?>

        </div>

        <button type="button"
                class="testimonials-nav testimonials-nav-next"
                aria-label="Next testimonial">
            ›
        </button>

    </div>

</section>


<!-- ============ BOOKING CTA ============ -->
<section class="home-booking-cta">

    <div class="container booking-cta-content">

        <p class="section-label">READY WHEN YOU ARE</p>

        <h2>Let's Make Your Next Journey Memorable</h2>

        <p>
            Tell us what you need and we'll take care of the rest —
            book online in under a minute.
        </p>

        <a href="/fetecation/booking.php" class="hero-primary-button">
            Book With FeteCation
        </a>

    </div>

</section>


<script src="/fetecation/js/testimonials.js"></script>


<?php

require_once __DIR__ . "/includes/footer.php";

?>