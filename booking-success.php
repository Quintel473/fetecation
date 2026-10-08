<?php

$pageTitle = "Booking Received";

require_once __DIR__ . "/tours-data.php";

$ref = $_GET['ref'] ?? '';

/* Try to find the booking file so we can show details */
$booking = null;
if ($ref !== '' && ctype_alnum($ref)) {
    $files = glob(__DIR__ . '/data/bookings/*_' . $ref . '.json');
    if (!empty($files)) {
        $booking = json_decode(file_get_contents($files[0]), true);
    }
}

require_once __DIR__ . "/includes/header.php";

?>

<section class="page-hero">
    <div class="container page-hero-content">

        <p class="section-label">BOOKING RECEIVED</p>

        <h1>Thank You<?= $booking ? ', ' . htmlspecialchars($booking['name']) : ''; ?>!</h1>

        <p>
            We've received your booking request and sent a confirmation
            to your email.
        </p>

    </div>
</section>


<section class="content-section">
    <div class="container">

        <div class="success-card">

            <div class="success-icon">✓</div>

            <h2>Your booking is being reviewed</h2>

            <p class="success-subtitle">
                We'll confirm availability and send you the final
                details within <strong>24 hours</strong>.
            </p>

            <?php if ($booking): ?>

                <ul class="success-details">

                    <li>
                        <span>Reference</span>
                        <strong><?= htmlspecialchars($booking['reference']); ?></strong>
                    </li>

                    <li>
                        <span>Experience</span>
                        <strong><?= htmlspecialchars($booking['tour']); ?></strong>
                    </li>

                    <li>
                        <span>Date</span>
                        <strong><?= htmlspecialchars($booking['date']); ?></strong>
                    </li>

                    <li>
                        <span>Guests</span>
                        <strong><?= htmlspecialchars($booking['guests']); ?></strong>
                    </li>

                    <?php if (!empty($booking['pickup'])): ?>
                        <li>
                            <span>Pickup</span>
                            <strong><?= htmlspecialchars($booking['pickup']); ?></strong>
                        </li>
                    <?php endif; ?>

                </ul>

                <p class="success-note">
                    Keep your reference number handy — it helps us find
                    your booking quickly if you contact us.
                </p>

            <?php else: ?>

                <p class="success-note">
                    If you don't hear from us within 24 hours, please
                    contact us directly at
                    <a href="mailto:quintelcharles@ccagrenada.com">quintelcharles@ccagrenada.com</a>.
                </p>

            <?php endif; ?>

            <div class="success-actions">

                <a href="/fetecation/" class="primary-button">
                    Back to Home
                </a>

                <a href="https://wa.me/15550000000?text=Hi%20FeteCation!%20I%20just%20submitted%20a%20booking%20(Ref%3A%20<?= urlencode($ref); ?>)"
                   class="secondary-button"
                   target="_blank"
                   rel="noopener">
                    💬 Message Us on WhatsApp
                </a>

            </div>

        </div>

    </div>
</section>


<?php require_once __DIR__ . "/includes/footer.php"; ?>