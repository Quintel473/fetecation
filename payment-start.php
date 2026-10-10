<?php
/**
 * Starts the PayPal payment flow for a booking.
 */

$pageTitle = "Complete Payment";

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/paypal.php';

$ref = $_GET['ref'] ?? '';

if ($ref === '' || !ctype_alnum($ref)) {
    header('Location: /fetecation/');
    exit;
}

/* Load booking */
try {
    $stmt = $pdo->prepare('SELECT * FROM bookings WHERE Reference = ? LIMIT 1');
    $stmt->execute([$ref]);
    $booking = $stmt->fetch();
} catch (Throwable $e) {
    $booking = null;
}

if (!$booking) {
    header('Location: /fetecation/');
    exit;
}

/* Determine amount based on payment option */
$paymentOption = $booking['PaymentOption'] ?? 'full';

/* If they chose "pay on day", nothing to pay now */
if ($paymentOption === 'on-day') {
    header('Location: /fetecation/booking-success.php?ref=' . urlencode($ref));
    exit;
}

$amount = paypal_calculate_amount(
    $booking['TourSlug'] ?? 'taxi',
    (int)($booking['NumberOfPassengers'] ?? 1),
    $paymentOption
);

$description = 'FeteCation booking ' . $booking['Reference'];

/* Create the PayPal order */
$order = paypal_create_order($amount, $booking['Reference'], $description);

if (!$order['ok']) {

    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero">
        <div class="container page-hero-content">
            <p class="section-label">PAYMENT</p>
            <h1>Payment Unavailable</h1>
            <p>We couldn't start the payment process right now.</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="account-auth-card" style="text-align:center;">
                <div class="success-icon" style="background:#c94f2c;">!</div>
                <h2>Something went wrong</h2>
                <p class="auth-card-subtitle">
                    Please try again in a moment, or contact us directly
                    and we'll send you a payment link.
                </p>
                <div style="margin-top:26px;">
                    <a href="/fetecation/payment-start.php?ref=<?= urlencode($ref); ?>"
                       class="primary-button">Try Again</a>
                    <a href="/fetecation/" style="display:inline-block;margin-left:14px;color:#666;">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

/* Log the attempt */
paypal_log_payment([
    'reference'      => $booking['Reference'],
    'customer_email' => $booking['Email'],
    'customer_name'  => $booking['Name'],
    'amount'         => $amount,
    'currency'       => PAYPAL_CURRENCY,
    'option'         => $paymentOption,
    'order_id'       => $order['order_id'],
    'status'         => 'initiated',
    'created_at'     => date('Y-m-d H:i:s'),
]);

/* Redirect to PayPal */
header('Location: ' . $order['url']);
exit;