<?php
/**
 * Customer-initiated booking cancellation.
 * Only logged-in customers can cancel their own upcoming bookings.
 */

require_once __DIR__ . '/../includes/customer-auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/mailer.php';

/* Must be logged in */
if (!customer_is_logged_in()) {
    header('Location: /fetecation/auth/login.php');
    exit;
}

$customer = customer_current();
if (!$customer) {
    customer_logout();
    header('Location: /fetecation/auth/login.php');
    exit;
}

$bookingId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($bookingId < 1) {
    header('Location: /fetecation/account/index.php');
    exit;
}

/* Load the booking and confirm it belongs to this customer */
try {
    $stmt = $pdo->prepare(
        'SELECT * FROM bookings
         WHERE BookingID = ?
           AND (CustomerID = ? OR Email = ?)
         LIMIT 1'
    );
    $stmt->execute([
        $bookingId,
        (int)$customer['CustomerID'],
        $customer['Email'],
    ]);
    $booking = $stmt->fetch();
} catch (Throwable $e) {
    $booking = null;
}

if (!$booking) {
    header('Location: /fetecation/account/index.php');
    exit;
}

$error   = '';
$success = false;

/* ---------------------------------------------------------
   Handle POST — perform the cancellation
   --------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['confirm'])) {

    /* Refuse if already cancelled */
    if (strtolower($booking['Status'] ?? '') === 'cancelled') {
        $error = 'This booking has already been cancelled.';
    }

    /* Refuse if the trip has already passed */
    elseif (!empty($booking['BookingDate']) && $booking['BookingDate'] < date('Y-m-d')) {
        $error = 'This trip has already taken place and cannot be cancelled online.';
    }

    else {

        /* How much time until the trip? */
        $tripTs  = strtotime($booking['BookingDate'] . ' 00:00:00');
        $nowTs   = time();
        $hoursUntil = ($tripTs - $nowTs) / 3600;

        $lateCancel    = $hoursUntil < 24;   // under 24 hours
        $wasPaid       = in_array($booking['PaymentOption'] ?? 'on-day', ['full', 'deposit'], true);

        $refundOwed    = $wasPaid && !$lateCancel;

        /* Build admin note */
        $cancelledBy   = 'customer';
        $adminNote     = 'Cancelled by customer on ' . date('Y-m-d H:i') . '. ';
        if ($lateCancel) {
            $adminNote .= 'LATE CANCEL (under 24h) — no refund per policy. ';
        }
        if ($refundOwed) {
            $adminNote .= 'REFUND NEEDED — issue via PayPal. ';
        }

        /* Merge with any existing admin notes */
        if (!empty($booking['AdminNotes'])) {
            $adminNote = $booking['AdminNotes'] . "\n\n" . $adminNote;
        }

        /* Update the booking */
        try {
            $stmt = $pdo->prepare(
                'UPDATE bookings
                 SET Status = "Cancelled",
                     AdminNotes = ?
                 WHERE BookingID = ?'
            );
            $stmt->execute([$adminNote, $bookingId]);

            /* Refresh local copy for the email */
            $booking['Status']     = 'Cancelled';
            $booking['AdminNotes'] = $adminNote;

            /* Send notification emails */
            send_customer_cancelled_notice_to_customer($booking, $refundOwed, $lateCancel);
            send_customer_cancelled_notice_to_admin($booking, $refundOwed, $lateCancel);

            $success = true;

        } catch (Throwable $e) {
            error_log('customer cancel failed: ' . $e->getMessage());
            $error = 'Could not cancel the booking. Please try again.';
        }
    }
}

/* ---------------------------------------------------------
   Compute display values (for GET preview)
   --------------------------------------------------------- */
$tripTs      = !empty($booking['BookingDate']) ? strtotime($booking['BookingDate'] . ' 00:00:00') : 0;
$hoursUntil  = $tripTs ? ($tripTs - time()) / 3600 : 0;
$lateCancel  = $hoursUntil < 24;
$wasPaid     = in_array($booking['PaymentOption'] ?? 'on-day', ['full', 'deposit'], true);
$refundOwed  = $wasPaid && !$lateCancel;

$tourLabel = ($booking['TourSlug'] === 'taxi' || empty($booking['TourSlug']))
             ? 'Taxi Service'
             : ($booking['TourSlug'] ?: 'Tour');

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">MY FETECATION</p>
        <h1><?= $success ? 'Booking Cancelled' : 'Cancel This Booking?'; ?></h1>
        <p>
            <?= $success
                ? 'Your booking has been cancelled.'
                : 'Please confirm you want to cancel this booking.'; ?>
        </p>
    </div>
</section>


<section class="content-section">
    <div class="container">

        <div class="cancel-card">

            <?php if ($success): ?>

                <div class="cancel-icon cancel-icon-success">✓</div>

                <h2>Booking cancelled</h2>

                <p class="cancel-subtitle">
                    We've cancelled booking <strong><?= htmlspecialchars($booking['Reference']); ?></strong>.
                    <?php if ($refundOwed): ?>
                        A refund will be issued to your original payment method within 5 business days.
                    <?php elseif ($wasPaid && $lateCancel): ?>
                        As this was cancelled within 24 hours of the trip, the deposit is non-refundable per our policy.
                    <?php else: ?>
                        No refund is owed.
                    <?php endif; ?>
                </p>

                <?php if ($refundOwed): ?>
                    <div class="cancel-notice cancel-notice-info">
                        <strong>💳 Refund pending</strong>
                        <p>
                            We'll process your refund via PayPal within 5 business days. You'll
                            receive a confirmation email when it's been sent.
                        </p>
                    </div>
                <?php elseif ($wasPaid && $lateCancel): ?>
                    <div class="cancel-notice cancel-notice-warn">
                        <strong>⚠️ Late cancellation</strong>
                        <p>
                            Because this was cancelled within 24 hours of the trip, the deposit
                            is not refundable per our <a href="/fetecation/terms.php">Terms of Service</a>.
                        </p>
                    </div>
                <?php endif; ?>

                <div class="cancel-actions">
                    <a href="/fetecation/account/index.php" class="primary-button">
                        Back to My Bookings
                    </a>
                    <a href="/fetecation/booking.php" class="secondary-button">
                        Book Another Trip
                    </a>
                </div>

            <?php else: ?>

                <div class="cancel-icon cancel-icon-warn">⚠</div>

                <h2>Cancel this booking?</h2>

                <?php if ($error !== ''): ?>
                    <div class="cancel-notice cancel-notice-error">
                        <strong><?= htmlspecialchars($error); ?></strong>
                    </div>
                <?php endif; ?>

                <div class="cancel-summary">
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
                    </ul>
                </div>

                <?php if ($refundOwed): ?>
                    <div class="cancel-notice cancel-notice-info">
                        <strong>💳 Refund eligible</strong>
                        <p>
                            You're cancelling more than 24 hours before your trip, so
                            you'll receive a full refund of what you paid.
                        </p>
                    </div>
                <?php elseif ($wasPaid && $lateCancel): ?>
                    <div class="cancel-notice cancel-notice-warn">
                        <strong>⚠️ No refund available</strong>
                        <p>
                            Your trip is less than 24 hours away. Per our
                            <a href="/fetecation/terms.php">Terms of Service</a>,
                            cancellations within 24 hours are not eligible for a refund.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="cancel-notice cancel-notice-info">
                        <strong>No refund owed</strong>
                        <p>
                            You chose to pay on the day, so no payment was taken.
                            No refund is needed.
                        </p>
                    </div>
                <?php endif; ?>

                <form method="post" action="/fetecation/account/cancel-booking.php" class="cancel-form">
                    <input type="hidden" name="id" value="<?= (int)$bookingId; ?>">
                    <input type="hidden" name="confirm" value="1">

                    <div class="cancel-actions">
                        <button type="submit" class="danger-button">
                            Yes, Cancel This Booking
                        </button>
                        <a href="/fetecation/account/index.php" class="secondary-button">
                            Keep My Booking
                        </a>
                    </div>
                </form>

            <?php endif; ?>

        </div>

    </div>
</section>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>