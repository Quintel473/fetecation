<?php
$pageTitle = "Payment Cancelled";

require_once __DIR__ . '/../includes/database.php';

$ref = $_GET['ref'] ?? '';

$booking = null;
if ($ref !== '') {
    try {
        $stmt = $pdo->prepare('SELECT * FROM bookings WHERE Reference = ? LIMIT 1');
        $stmt->execute([$ref]);
        $booking = $stmt->fetch();
    } catch (Throwable $e) {}
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">PAYMENT</p>
        <h1>Payment Cancelled</h1>
        <p>No charge was made — your booking is still safe.</p>
    </div>
</section>


<section class="content-section">
    <div class="container">
        <div class="success-card">

            <div class="success-icon" style="background:#c94f2c;">!</div>

            <h2>No payment was taken</h2>

            <p class="success-subtitle">
                It looks like you cancelled the payment. That's totally fine —
                your booking request has been saved and our team will be in touch.
                You can also try the payment again whenever you're ready.
            </p>

            <?php if ($booking): ?>
                <ul class="success-details">

                    <li>
                        <span>Reference</span>
                        <strong><?= htmlspecialchars($booking['Reference']); ?></strong>
                    </li>

                    <li>
                        <span>Experience</span>
                        <strong>
                            <?= htmlspecialchars(
                                ($booking['TourSlug'] === 'taxi' || empty($booking['TourSlug']))
                                    ? 'Taxi Service'
                                    : $booking['TourSlug']
                            ); ?>
                        </strong>
                    </li>

                    <li>
                        <span>Date</span>
                        <strong><?= htmlspecialchars($booking['BookingDate']); ?></strong>
                    </li>

                </ul>
            <?php endif; ?>

            <div class="success-actions">
                <?php if ($ref !== ''): ?>
                    <a href="/fetecation/booking/payment-start.php?ref=<?= urlencode($ref); ?>"
                       class="primary-button">
                        Try Payment Again
                    </a>
                <?php else: ?>
                    <a href="/fetecation/" class="primary-button">Back to Home</a>
                <?php endif; ?>

                <a href="https://wa.me/14734597407?text=Hi%20FeteCation!"
                   class="secondary-button"
                   target="_blank"
                   rel="noopener">
                    💬 WhatsApp Us
                </a>
            </div>

            <p style="margin-top:24px; font-size:13px; color:#888;">
                <?php if ($ref !== ''): ?>
                    Or <a href="/fetecation/" style="color:#c94f2c;">return to the homepage</a>.
                <?php endif; ?>
            </p>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>