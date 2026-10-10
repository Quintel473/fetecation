<?php
$pageTitle = "Payment Received";

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/paypal.php';

$ref     = $_GET['ref']   ?? '';
$orderId = $_GET['token'] ?? '';

$booking = null;
if ($ref !== '') {
    try {
        $stmt = $pdo->prepare('SELECT * FROM bookings WHERE Reference = ? LIMIT 1');
        $stmt->execute([$ref]);
        $booking = $stmt->fetch();
    } catch (Throwable $e) {}
}

/* Capture the payment */
$captureOk = false;
if ($orderId !== '') {
    $capture   = paypal_capture_order($orderId);
    $captureOk = !empty($capture['ok']);
}

/* Mark booking as paid */
if ($captureOk && $booking) {
    try {
        $upd = $pdo->prepare(
            'UPDATE bookings
             SET Status = "Confirmed"
             WHERE BookingID = ?'
        );
        $upd->execute([$booking['BookingID']]);
    } catch (Throwable $e) {
        error_log('booking status update failed: ' . $e->getMessage());
    }
}

/* Log it */
paypal_log_payment([
    'reference'  => $ref,
    'order_id'   => $orderId,
    'status'     => $captureOk ? 'captured' : 'capture_failed',
    'created_at' => date('Y-m-d H:i:s'),
]);

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">PAYMENT</p>
        <h1>Thank you<?= $booking ? ', ' . htmlspecialchars($booking['Name']) : ''; ?>!</h1>
        <p>Your payment has been received.</p>
    </div>
</section>


<section class="content-section">
    <div class="container">
        <div class="success-card">

            <div class="success-icon">✓</div>

            <h2>Payment confirmed</h2>

            <p class="success-subtitle">
                We've received your payment and your booking is fully secured.
                You'll get a receipt by email shortly.
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
                <a href="/fetecation/" class="primary-button">Back to Home</a>
                <a href="https://wa.me/14734560954?text=Hi%20FeteCation!"
                   class="secondary-button"
                   target="_blank"
                   rel="noopener">
                    💬 WhatsApp Us
                </a>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>