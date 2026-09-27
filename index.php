<?php

$pageTitle = "Home";

require_once __DIR__ . "/includes/header.php";

?>

<section class="hero">

    <div class="hero-overlay"></div>

    <div class="container hero-content">

        <p class="hero-subtitle">
            FETECATION TAXI & TOURS
        </p>

        <h1>
            Explore. Ride.<br>
            Experience.
        </h1>

        <p class="hero-text">
            Discover unforgettable Caribbean experiences with
            reliable taxi services and exciting island tours.
        </p>

        <div class="hero-buttons">

            <a href="/fetecation/booking.php" class="primary-button">
                Book a Ride
            </a>

            <a href="/fetecation/tours.php" class="secondary-button">
                Explore Tours
            </a>

        </div>

    </div>

</section>


<section class="welcome-section">

    <div class="container welcome-container">

        <div class="welcome-content">

            <p class="section-label">
                WELCOME TO FETECATION
            </p>

            <h2>
                Your Journey Starts Here
            </h2>

            <p>
                Whether you need a comfortable taxi ride or want to
                explore the island, FeteCation Taxi & Tours is here
                to make your experience convenient, enjoyable and
                memorable.
            </p>

            <a href="/fetecation/about.php" class="text-button">
                Learn More →
            </a>

        </div>

        <div class="welcome-card">

            <div class="welcome-card-icon">
                🚕
            </div>

            <h3>
                Taxi & Tours
            </h3>

            <p>
                Transportation and experiences designed around you.
            </p>

        </div>

    </div>

</section>


<section class="services-section">

    <div class="container">

        <div class="section-heading">

            <p class="section-label">
                WHAT WE OFFER
            </p>

            <h2>
                Travel With FeteCation
            </h2>

            <p>
                From getting around to discovering new places,
                we've got your journey covered.
            </p>

        </div>

        <div class="service-grid">

            <div class="service-card">

                <div class="service-icon">
                    🚕
                </div>

                <h3>
                    Taxi Services
                </h3>

                <p>
                    Reliable transportation for airport transfers,
                    hotels, events and everyday travel.
                </p>

                <a href="/fetecation/taxis.php">
                    View Taxi Services →
                </a>

            </div>


            <div class="service-card">

                <div class="service-icon">
                    🌴
                </div>

                <h3>
                    Island Tours
                </h3>

                <p>
                    Explore beautiful destinations, attractions
                    and unforgettable Caribbean experiences.
                </p>

                <a href="/fetecation/tours.php">
                    Explore Tours →
                </a>

            </div>


            <div class="service-card">

                <div class="service-icon">
                    📅
                </div>

                <h3>
                    Easy Booking
                </h3>

                <p>
                    Tell us what you need and when you need it.
                    We'll handle the rest.
                </p>

                <a href="/fetecation/booking.php">
                    Book Now →
                </a>

            </div>

        </div>

    </div>

</section>


<section class="cta-section">

    <div class="container cta-content">

        <p class="section-label">
            READY TO GO?
        </p>

        <h2>
            Let's Make Your Next Journey Memorable.
        </h2>

        <a href="/fetecation/booking.php" class="primary-button">
            Book With FeteCation
        </a>

    </div>

</section>


<?php

require_once __DIR__ . "/includes/footer.php";

?>