<?php
$pageTitle = "Taxi Services";

require_once __DIR__ . "/includes/header.php";
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">RELIABLE & COMFORTABLE</p>
        <h1>Taxi Services</h1>
        <p>From airport transfers to island exploring — we get you there safely, on time, and in comfort.</p>
    </div>
</section>


<section class="content-section">
    <div class="container">

        <div class="section-heading">
            <p class="section-label">WHAT WE OFFER</p>
            <h2>Travel With FeteCation</h2>
            <p>Hover over a card to learn more, or click to book instantly.</p>
        </div>


        <!-- Using the same grid layout as your homepage services -->
        <div class="service-grid">

            <!-- Card 1 -->
            <a href="/fetecation/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon">✈️</div>
                <h3>Airport Transfers</h3>
                <p>Stress-free pickups and drop-offs.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>We monitor your flight in real-time. If your plane is delayed, we're still there waiting for you when you land.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 2 -->
            <a href="/fetecation/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon">🏖️</div>
                <h3>Island & Beach Hopping</h3>
                <p>Your private ride around the island.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>See three different beaches in one day. We provide a private vehicle and driver for as long as you need.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 3 -->
            <a href="/fetecation/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon">🍹</div>
                <h3>Nightlife & Events</h3>
                <p>Safe, reliable rides after dark.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>Heading out for dinner or a party? Our drivers know the best spots and ensure you get home safely.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 4 -->
            <a href="/fetecation/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon">🛳️</div>
                <h3>Cruise Port Pickups</h3>
                <p>Punctual transfers for your stay.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>Arriving on a cruise ship? We offer punctual, air-conditioned transfers with plenty of room for your luggage.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 5 -->
            <a href="/fetecation/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon">🚐</div>
                <h3>Hourly & Full-Day Hire</h3>
                <p>A driver on standby, all day.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>Perfect for business trips or exploring. Book a vehicle by the hour or for the whole day at your own pace.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 6 -->
            <a href="/fetecation/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon">💼</div>
                <h3>Corporate & Business</h3>
                <p>Professional transport for work.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>Impress clients or get to meetings on time. We offer discreet, professional drivers and immaculate vehicles.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

        </div>

    </div>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>