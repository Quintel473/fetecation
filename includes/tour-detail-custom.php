<?php
/**
 * Tour detail template — CUSTOM variant.
 * Used for the "Custom Island Experience" tour which has
 * a "how it works" flow instead of a fixed itinerary.
 *
 * Usage:
 *   $tourSlug = 'custom-island';
 *   require __DIR__ . '/../includes/tour-detail-custom.php';
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

        <p class="section-label"><?= htmlspecialchars($tour['tag']); ?> EXPERIENCE</p>

        <h1><?= htmlspecialchars($tour['name']); ?></h1>

        <p><?= htmlspecialchars($tour['tagline']); ?></p>

    </div>

</section>


<!-- ============ MAIN ============ -->
<section class="content-section tour-detail-section">

    <div class="container tour-detail-layout">

        <!-- ============ LEFT ============ -->
        <article class="tour-detail-main">

            <div class="tour-detail-image">
                <div class="tour-placeholder">
                    <?= htmlspecialchars($tour['emoji']); ?>
                </div>
            </div>

            <ul class="tour-stats">

                <li>
                    <span class="tour-stat-icon">🕐</span>
                    <span class="tour-stat-label">Schedule</span>
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

            <div class="tour-block">
                <h2>Build Your Perfect Day</h2>
                <?php foreach ($tour['about'] as $para): ?>
                    <p><?= htmlspecialchars($para); ?></p>
                <?php endforeach; ?>
            </div>

            <!-- ============ HOW IT WORKS ============ -->
            <?php if (!empty($tour['how_it_works'])): ?>
                <div class="tour-block">
                    <h2>How It Works</h2>
                    <ol class="custom-steps">
                        <?php foreach ($tour['how_it_works'] as $step): ?>
                            <li class="custom-step">
                                <span class="custom-step-number"><?= htmlspecialchars($step['step']); ?></span>
                                <div class="custom-step-body">
                                    <strong><?= htmlspecialchars($step['title']); ?></strong>
                                    <p><?= htmlspecialchars($step['text']); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>

            <!-- ============ IDEAS ============ -->
            <?php if (!empty($tour['ideas'])): ?>
                <div class="tour-block">
                    <h2>Great For</h2>
                    <ul class="custom-ideas">
                        <?php foreach ($tour['ideas'] as $idea): ?>
                            <li class="custom-idea">
                                <span class="custom-idea-icon"><?= htmlspecialchars($idea['icon']); ?></span>
                                <span class="custom-idea-title"><?= htmlspecialchars($idea['title']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="tour-block">
                <h2>What's Included</h2>
                <ul class="tour-checklist">
                    <?php foreach ($tour['included'] as $item): ?>
                        <li><?= htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tour-block">
                <h2>What to Bring</h2>
                <ul class="tour-checklist">
                    <?php foreach ($tour['bring'] as $item): ?>
                        <li><?= htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </article>


        <!-- ============ SIDEBAR ============ -->
        <aside class="tour-detail-sidebar">

            <div class="tour-booking-card">

                <span class="tour-booking-label">PRICING</span>
                <p class="tour-booking-price">
                    <?= htmlspecialchars($tour['price']); ?>
                </p>

                <a href="/fetecation/booking.php?tour=<?= urlencode($tourSlug); ?>"
                   class="tour-booking-button">
                    Request a Custom Quote
                </a>

                <p class="tour-booking-note">
                    We reply within 24 hours with your personalised itinerary.
                </p>

                <hr>

                <div class="tour-booking-contact">

                    <p>
                        <strong>Prefer to chat?</strong>
                        Reach out directly.
                    </p>

                    <a href="tel:+15550000000" class="tour-contact-link">
                        📞 +1 (555) 000-0000
                    </a>

                    <a href="https://wa.me/15550000000?text=Hi%20FeteCation!%20I'd%20like%20to%20plan%20a%20custom%20island%20experience."
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

                    <div class="tour-image">
                        <div class="tour-placeholder">
                            <?= htmlspecialchars($other['emoji']); ?>
                        </div>
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

        <h2>Let's Build Your Perfect Day</h2>

        <a href="/fetecation/booking.php?tour=<?= urlencode($tourSlug); ?>"
           class="primary-button">
            Request a Custom Quote
        </a>

    </div>

</section>


<?php require __DIR__ . '/footer.php'; ?>