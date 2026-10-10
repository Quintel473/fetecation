<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/../includes/paypal.php';


/**
 * Redirect back to the dashboard, preserving the caller's page
 * (?bpage=2&mpage=1) if it was passed in ?return=...
 */
function admin_redirect_back(): void
{
    $base = '/fetecation/staff-7742/index.php';
    $qs   = $_GET['return'] ?? '';

    if ($qs !== '' && preg_match('/^\?[a-zA-Z0-9_=&]+$/', $qs)) {
        header('Location: ' . $base . $qs);
    } else {
        header('Location: ' . $base);
    }
    exit;
}


/* ---------------------------------------------------------
   Parse + validate
   --------------------------------------------------------- */
$type   = $_GET['type']   ?? '';
$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!in_array($type, ['booking', 'message'], true)) {
    admin_redirect_back();
}

if (!in_array($action, ['confirm', 'cancel', 'delete', 'send-payment'], true)) {
    admin_redirect_back();
}

if ($id < 1) {
    admin_redirect_back();
}

/* ---------------------------------------------------------
   Perform action
   --------------------------------------------------------- */
try {

    if ($type === 'booking') {

        if ($action === 'delete') {

            $stmt = $pdo->prepare('DELETE FROM bookings WHERE BookingID = ?');
            $stmt->execute([$id]);

        } else {

            /* Load the booking first so we can email the customer */
            $lookup = $pdo->prepare('SELECT * FROM bookings WHERE BookingID = ? LIMIT 1');
            $lookup->execute([$id]);
            $booking = $lookup->fetch();

            if (!$booking) {
                admin_redirect_back();
            }

            /* ---- Send payment link ---- */
            if ($action === 'send-payment') {
                send_payment_link_email($booking);
                admin_redirect_back();
            }

            /* ---- Confirm / Cancel ---- */
            $status = $action === 'confirm' ? 'Confirmed' : 'Cancelled';

            $stmt = $pdo->prepare(
                'UPDATE bookings SET Status = ? WHERE BookingID = ?'
            );
            $stmt->execute([$status, $id]);

            /* Send the appropriate customer email */
            if ($action === 'confirm') {
                send_booking_confirmed_email($booking);
            } else {
                send_booking_cancelled_email($booking);
            }
        }

    } else { /* message */

        if ($action === 'delete') {
            $stmt = $pdo->prepare('DELETE FROM messages WHERE MessageID = ?');
            $stmt->execute([$id]);
        }
        /* Messages only support delete currently */

    }

} catch (Throwable $e) {
    error_log('admin action failed: ' . $e->getMessage());
}

admin_redirect_back();