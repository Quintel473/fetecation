<?php
$pageTitle = "Taxi Services";

require_once __DIR__ . "/data/tours-data.php";
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


        <div class="service-grid">

            <!-- Card 1: Airport -->
            <a href="/fetecation/booking/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"></path></svg>
                </div>
                <h3>Airport Transfers</h3>
                <p>Stress-free pickups and drop-offs.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>We monitor your flight in real-time. If your plane is delayed, we're still there waiting for you when you land.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 2: Beach -->
            <a href="/fetecation/booking/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 17h20"></path><path d="M12 9v8"></path><path d="M22 21a9 9 0 0 0-15-8.2"></path><path d="M2 21a9 9 0 0 1 15-8.2"></path></svg>
                </div>
                <h3>Island & Beach Hopping</h3>
                <p>Your private ride around the island.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>See three different beaches in one day. We provide a private vehicle and driver for as long as you need.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 3: Nightlife -->
            <a href="/fetecation/booking/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 22H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h4"></path><path d="M16 22h3a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-4"></path><path d="M12 2v20"></path><path d="M8 7h8"></path></svg>
                </div>
                <h3>Nightlife & Events</h3>
                <p>Safe, reliable rides after dark.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>Heading out for dinner or a party? Our drivers know the best spots and ensure you get home safely.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 4: Cruise -->
            <a href="/fetecation/booking/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.5 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M19.38 20A11.6 11.6 0 0 0 21 14l-9-4-9 4c0 2.9.94 5.34 2.81 7.76"></path><path d="M19 13V7a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v6"></path><path d="M12 10v4"></path><path d="M12 2v3"></path></svg>
                </div>
                <h3>Cruise Port Pickups</h3>
                <p>Punctual transfers for your stay.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>Arriving on a cruise ship? We offer punctual, air-conditioned transfers with plenty of room for your luggage.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 5: Hourly -->
            <a href="/fetecation/booking/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                </div>
                <h3>Hourly & Full-Day Hire</h3>
                <p>A driver on standby, all day.</p>
                
                <div class="taxi-expand-content">
                    <div class="taxi-expand-inner">
                        <p>Perfect for business trips or exploring. Book a vehicle by the hour or for the whole day at your own pace.</p>
                        <span class="text-button">Book Now →</span>
                    </div>
                </div>
            </a>

            <!-- Card 6: Corporate -->
            <a href="/fetecation/booking/booking.php?tour=taxi" class="service-card taxi-dropdown-card">
                <div class="service-icon" style="color: var(--fete-orange);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
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