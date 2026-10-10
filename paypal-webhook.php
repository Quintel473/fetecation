<?php
/**
 * PayPal webhook receiver.
 *
 * Configure this URL in your PayPal app dashboard:
 *   http://localhost/fetecation/paypal-webhook.php
 *
 * NOTE: For production, add webhook signature verification.
 * Currently we log every event and update matching bookings.
 */

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/paypal.php';

/* Read the raw body */
$raw   = file_get_contents('php://input');
$event = json_decode($raw, true);

if (!is_array($event)) {
    http_response_code(400);
    echo 'Invalid payload';
    exit;
}

$eventType = $event['event_type'] ?? 'unknown';

/* Log the raw event */
paypal_log_payment([
    'event_type' => $eventType,
    'payload'    => $event,
    'created_at' => date('Y-m-d H:i:s'),
]);

/* ---------------------------------------------------------
   Handle events we care about
   --------------------------------------------------------- */

try {

    if ($eventType === 'CHECKOUT.ORDER.APPROVED') {

        $orderId = $event['resource']['id'] ?? '';
        /* Log — customer approved, but capture happens on return */

    } elseif ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {

        /* Capture completed */
        $captureId = $event['resource']['id'] ?? '';
        $reference = $event['resource']['custom_id']
                     ?? $event['resource']['invoice_id']
                     ?? '';

        if ($reference !== '') {
            $upd = $pdo->prepare(
                'UPDATE bookings
                 SET Status = "Confirmed"
                 WHERE Reference = ?'
            );
            $upd->execute([$reference]);
        }

    } elseif ($eventType === 'PAYMENT.CAPTURE.DENIED'
           || $eventType === 'PAYMENT.CAPTURE.REFUNDED') {

        $reference = $event['resource']['custom_id']
                     ?? $event['resource']['invoice_id']
                     ?? '';

        if ($reference !== '') {
            $upd = $pdo->prepare(
                'UPDATE bookings
                 SET Status = "Cancelled"
                 WHERE Reference = ?'
            );
            $upd->execute([$reference]);
        }
    }

} catch (Throwable $e) {
    error_log('webhook processing failed: ' . $e->getMessage());
}

/* Always respond 200 so PayPal doesn't retry */
http_response_code(200);
echo 'ok';