<?php

$pageTitle = "Booking Received";

require_once __DIR__ . "/includes/database.php";
require_once __DIR__ . "/includes/paypal.php";

$ref = $_GET['ref'] ?? '';

/* Look up the booking in MySQL by reference */
$booking = null;
if ($ref !== '' && ctype_alnum($ref)) {
    try {
        $stmt = $pdo->prepare(
            'SELECT * FROM bookings WHERE Reference = ? LIMIT 1'
        );
        $stmt->execute([$ref]);
        $booking = $stmt->fetch();
    } catch (Throwable $e) {
        error_log('booking-success lookup failed: ' . $e->getMessage());
    }
}

require_once __DIR__ . "/includes/header.php";

?>

<section class="page-hero">
    <div class="container page-hero-content">

        <p class="section-label">BOOKING RECEIVED</p>

        <h1>Thank You<?= $booking ? ', ' . htmlspecialchars($booking['Name']) : ''; ?>!</h1>

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

                <?php
                    $tourLabel = ($booking['TourSlug'] === 'taxi' || empty($booking['TourSlug']))
                                 ? 'Taxi Service'
                                 : ($booking['TourSlug'] ?: 'Tour');

                    $paymentOption = $booking['PaymentOption'] ?? 'on-day';
                ?>

                <ul class="success-details">

                    <li>
                        <span>Reference</span>
                        <strong><?= htmlspecialchars($booking['Reference']); ?></strong>
                    </li>

                    <li>
                        <span>Experience</span>
                        <strong><?= htmlspecialchars($tourLabel); ?></strong>
                    </li>

                    <li>
                        <span>Date</span>
                        <strong><?= htmlspecialchars($booking['BookingDate']); ?></strong>
                    </li>

                    <li>
                        <span>Guests</span>
                        <strong><?= htmlspecialchars($booking['NumberOfPassengers']); ?></strong>
                    </li>

                    <?php if (!empty($booking['PickupLocation'])): ?>
                        <li>
                            <span>Pickup</span>
                            <strong><?= htmlspecialchars($booking['PickupLocation']); ?></strong>
                        </li>
                    <?php endif; ?>

                </ul>

                <p class="success-note">
                    Keep your reference number handy — it helps us find
                    your booking quickly if you contact us.
                </p>


                <!-- ============ PAYMENT CTA ============ -->
                <?php if ($paymentOption === 'full' || $paymentOption === 'deposit'): ?>

                    <div class="payment-cta">

                        <?php if ($paymentOption === 'deposit'): ?>
                            <p class="payment-note">
                                Your <strong><?= DEPOSIT_PERCENT; ?>% deposit</strong>
                                secures your spot. The rest is due on the day of your tour.
                            </p>
                        <?php else: ?>
                            <p class="payment-note">
                                Complete your payment now to lock in your booking.
                            </p>
                        <?php endif; ?>

                        <a href="/fetecation/payment-start.php?ref=<?= urlencode($booking['Reference']); ?>"
                           class="primary-button payment-button">
                            💳 Pay Now
                        </a>

                        <p class="payment-secure">
                            🔒 Secure payment powered by PayPal
                        </p>

                    </div>

                <?php else: ?>

                    <div class="payment-cta payment-cta-plain">
                        <p class="payment-note">
                            <strong>Payment on the day.</strong>
                            No online payment needed — pay your driver or guide directly.
                        </p>
                    </div>

                <?php endif; ?>

            <?php else: ?>

                <p class="success-note">
                    If you don't hear from us within 24 hours, please
                    contact us directly at
                    <a href="mailto:qunitelcharles@gmail.com">qunitelcharles@gmail.com</a>.
                </p>

            <?php endif; ?>

            <div class="success-actions">

                <a href="/fetecation/" class="primary-button">
                    Back to Home
                </a>

                <a href="https://wa.me/14734560954?text=Hi%20FeteCation!%20I%20just%20submitted%20a%20booking%20(Ref%3A%20<?= urlencode($ref); ?>)"
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