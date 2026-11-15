<?php
/**
 * Reusable tour detail template.
 * Usage (in a tour stub file):
 *
 *   $tourSlug = 'island-highlights';
 *   require __DIR__ . '/../includes/tour-detail.php';
 */

require_once __DIR__ . '/../data/tours-data.php';

if (!isset($tourSlug) || !$tour = fete_tour($tourSlug)) {
    header('HTTP/1.0 404 Not Found');
    exit('Tour not found.');
}

$pageTitle = $tour['name'];

require __DIR__ . '/header.php';
?>

<!-- ============ PAGE HERO ============ -->
<section class="page-hero tour-detail-hero">
    <div class="container page-hero-content">
        <p class="section-label"><?= htmlspecialchars($tour['tag']); ?> TOUR</p>
        <h1><?= htmlspecialchars($tour['name']); ?></h1>
        <p><?= htmlspecialchars($tour['tagline']); ?></p>
    </div>
</section>

<!-- ============ MAIN CONTENT ============ -->
<section class="content-section tour-detail-section">
    <div class="container tour-detail-layout">

        <!-- ============ LEFT COLUMN ============ -->
        <article class="tour-detail-main">

            <!-- Hero image -->
            <div class="tour-detail-image"
                 style="background-image: url('<?= htmlspecialchars($tour['image']); ?>');">
            </div>

            <!-- Quick stats -->
            <ul class="tour-stats">
                <li>
                    <!-- Clock Icon -->
                    <svg class="tour-stat-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span class="tour-stat-label">Duration</span>
                    <strong><?= htmlspecialchars($tour['duration']); ?></strong>
                </li>

                <li>
                    <!-- Users Icon -->
                    <svg class="tour-stat-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span class="tour-stat-label">Group Size</span>
                    <strong><?= htmlspecialchars($tour['group']); ?></strong>
                </li>

                <li>
                    <!-- Dollar Sign Icon -->
                    <svg class="tour-stat-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <span class="tour-stat-label">Price</span>
                    <strong><?= htmlspecialchars($tour['price']); ?></strong>
                </li>

                <li>
                    <!-- Map Pin Icon -->
                    <svg class="tour-stat-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span class="tour-stat-label">Pickup</span>
                    <strong><?= htmlspecialchars($tour['pickup']); ?></strong>
                </li>
            </ul>

            <!-- About -->
            <div class="tour-block">
                <h2>About This Tour</h2>
                <?php foreach ($tour['about'] as $para): ?>
                    <p><?= htmlspecialchars($para); ?></p>
                <?php endforeach; ?>
            </div>

            <!-- What's included -->
            <div class="tour-block">
                <h2>What's Included</h2>
                <ul class="tour-checklist">
                    <?php foreach ($tour['included'] as $item): ?>
                        <li><?= htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- What to bring -->
            <div class="tour-block">
                <h2>What to Bring</h2>
                <ul class="tour-checklist">
                    <?php foreach ($tour['bring'] as $item): ?>
                        <li><?= htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Itinerary -->
            <div class="tour-block">
                <h2>Itinerary</h2>
                <ol class="tour-itinerary">
                    <?php foreach ($tour['itinerary'] as $stop): ?>
                        <li>
                            <span class="tour-itinerary-time"><?= htmlspecialchars($stop['time']); ?></span>
                            <div>
                                <strong><?= htmlspecialchars($stop['title']); ?></strong>
                                <p><?= htmlspecialchars($stop['text']); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>

        </article>

        <!-- ============ SIDEBAR ============ -->
        <aside class="tour-detail-sidebar">
            <div class="tour-booking-card">
                <span class="tour-booking-label">FROM</span>
                <p class="tour-booking-price">
                    <?= htmlspecialchars($tour['price']); ?>
                </p>

                <a href="/fetecation/booking/booking.php?tour=<?= urlencode($tourSlug); ?>"
                   class="tour-booking-button">
                    Book This Tour
                </a>

                <p class="tour-booking-note">
                    Free cancellation up to 24 hours before your tour.
                </p>

                <hr>

                <div class="tour-booking-contact">
                    <p>
                        <strong>Questions?</strong>
                        Call or message us.
                    </p>
                    <!-- Phone Icon -->
                    <a href="tel:+14734597407" class="tour-contact-link" style="display:flex;align-items:center;gap:8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        +1 (473) 459-7407
                    </a>

                    <!-- WhatsApp Message Icon -->
                    <a href="https://wa.me/14734597407?text=Hi%20FeteCation!%20I'm%20interested%20in%20the%20<?= urlencode($tour['name']); ?>"
                       class="tour-contact-link"
                       target="_blank"
                       rel="noopener" style="display:flex;align-items:center;gap:8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                        Chat on WhatsApp
                    </a>
                </div>
            </div>
        </aside>

    </div>
</section>

<!-- ============ OTHER TOURS ============ -->
<section class="content-section tour-related">
    <div class="container">
        <div class="section-heading">
            <p class="section-label">EXPLORE MORE</p>
            <h2>You Might Also Like</h2>
        </div>

        <div class="tour-grid">
            <?php foreach (fete_all_tours() as $slug => $other): ?>
                <?php if ($slug === $tourSlug) continue; ?>

                <a href="/fetecation/tours/<?= htmlspecialchars($slug); ?>.php"
                   class="tour-card">
                    <div class="tour-image"
                         style="background-image: url('<?= htmlspecialchars($other['image']); ?>');">
                    </div>

                    <div class="tour-content">
                        <span class="tour-tag"><?= htmlspecialchars($other['tag']); ?></span>
                        <h3><?= htmlspecialchars($other['name']); ?></h3>
                        <p><?= htmlspecialchars($other['tagline']); ?></p>
                        <span class="tour-button">View Tour</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ BOTTOM CTA ============ -->
<section class="cta-section">
    <div class="container cta-content">
        <p class="section-label">READY WHEN YOU ARE</p>
        <h2>Book Your <?= htmlspecialchars($tour['name']); ?> Today</h2>
        <a href="/fetecation/booking/booking.php?tour=<?= urlencode($tourSlug); ?>"
           class="primary-button">
            Book This Tour
        </a>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>