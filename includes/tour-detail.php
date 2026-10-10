<?php
/**
 * Reusable tour detail template.
 * Usage (in a tour stub file):
 *
 *   $tourSlug = 'island-highlights';
 *   require __DIR__ . '/../includes/tour-detail.php';
 */

require_once __DIR__ . '/../tours-data.php';

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
                    <span class="tour-stat-icon">🕐</span>
                    <span class="tour-stat-label">Duration</span>
                    <strong><?= htmlspecialchars($tour['duration']); ?></strong>
                </li>

                <li>
                    <span class="tour-stat-icon">👥</span>
                    <span class="tour-stat-label">Group Size</span>
                    <strong><?= htmlspecialchars($tour['group']); ?></strong>
                </li>

                <li>
                    <span class="tour-stat-icon">💵</span>
                    <span class="tour-stat-label">Price</span>
                    <strong><?= htmlspecialchars($tour['price']); ?></strong>
                </li>

                <li>
                    <span class="tour-stat-icon">📍</span>
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

                <a href="/fetecation/booking.php?tour=<?= urlencode($tourSlug); ?>"
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

                    <a href="tel:+14734560954" class="tour-contact-link">
                        📞 +1 (473) 456-0954
                    </a>

                    <a href="https://wa.me/14734560954?text=Hi%20FeteCation!%20I'm%20interested%20in%20the%20<?= urlencode($tour['name']); ?>"
                       class="tour-contact-link"
                       target="_blank"
                       rel="noopener">
                        💬 Chat on WhatsApp
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

        <a href="/fetecation/booking.php?tour=<?= urlencode($tourSlug); ?>"
           class="primary-button">
            Book This Tour
        </a>

    </div>

</section>


<?php require __DIR__ . '/footer.php'; ?>