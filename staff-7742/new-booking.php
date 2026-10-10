<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/../tours-data.php';
require_once __DIR__ . '/../includes/paypal.php';
$errors  = [];
$success = false;

/* ---------------------------------------------------------
   Preserve form values on error
   --------------------------------------------------------- */
$form = [
    'name'           => '',
    'email'          => '',
    'phone'          => '',
    'tour'           => 'taxi',
    'date'           => '',
    'guests'         => '1',
    'pickup'         => '',
    'notes'          => '',
    'admin_notes'    => '',
    'payment_option' => 'full',
    'source'         => 'whatsapp',
    'custom_price'   => '',
    'send_email'     => '1',
    'send_to_admin'  => '1',
];

/* ---------------------------------------------------------
   Handle form submission
   --------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($form as $k => $_) {
        $form[$k] = trim((string)($_POST[$k] ?? ''));
    }

    /* Simple checkboxes */
    $form['send_email']    = !empty($_POST['send_email'])    ? '1' : '0';
    $form['send_to_admin'] = !empty($_POST['send_to_admin']) ? '1' : '0';

    /* Validate */
    if ($form['name'] === '')    $errors[] = 'Customer name is required.';
    if ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL))
                                 $errors[] = 'A valid customer email is required.';
    if ($form['phone'] === '')   $errors[] = 'Customer phone is required.';
    if ($form['date'] === '')    $errors[] = 'Please choose a date.';
    if ((int)$form['guests'] < 1) $errors[] = 'At least 1 guest required.';

    if (!in_array($form['payment_option'], ['full', 'deposit', 'on-day'], true)) {
        $form['payment_option'] = 'full';
    }

    if (!in_array($form['source'], ['website','whatsapp','phone','walk-in','referral','other'], true)) {
        $form['source'] = 'other';
    }

    $customPrice = null;
    if ($form['custom_price'] !== '') {
        $customPrice = (float)$form['custom_price'];
        if ($customPrice <= 0) {
            $errors[] = 'Custom price must be greater than zero.';
        }
    }

    /* Validate payment option against tour rules */
    if (empty($errors)) {
        $allowed = fete_tour_payment_options($form['tour']);
        if (!in_array($form['payment_option'], $allowed, true)) {
            $form['payment_option'] = $allowed[0] ?? 'full';
        }
    }

    /* Create booking */
    if (empty($errors)) {

        $reference   = fete_generate_reference();
        $submittedAt = date('Y-m-d H:i:s');

        $tourName = 'Taxi Service';
        if ($form['tour'] !== 'taxi') {
            $t = fete_tour($form['tour']);
            if ($t) $tourName = $t['name'];
        }

        try {
            $bookingType = ($form['tour'] === 'taxi') ? 'Taxi' : 'Tour';

            $stmt = $pdo->prepare(
                'INSERT INTO bookings
                    (Reference, Name, Email, Phone,
                     CustomerID, BookingType, TourSlug,
                     PickupLocation, BookingDate, NumberOfPassengers,
                     Notes, PaymentOption, Source, CustomPrice, AdminNotes,
                     Status, SubmittedAt, IP)
                 VALUES
                    (:ref, :name, :email, :phone,
                     NULL, :type, :slug,
                     :pickup, :bdate, :guests,
                     :notes, :payopt, :source, :customprice, :adminnotes,
                     :status, :submitted, :ip)'
            );

            $stmt->execute([
                ':ref'         => $reference,
                ':name'        => $form['name'],
                ':email'       => $form['email'],
                ':phone'       => $form['phone'],
                ':type'        => $bookingType,
                ':slug'        => $form['tour'],
                ':pickup'      => $form['pickup'],
                ':bdate'       => $form['date'],
                ':guests'      => (int)$form['guests'],
                ':notes'       => $form['notes'],
                ':payopt'      => $form['payment_option'],
                ':source'      => $form['source'],
                ':customprice' => $customPrice,
                ':adminnotes'  => $form['admin_notes'],
                ':status'      => 'Pending',
                ':submitted'   => $submittedAt,
                ':ip'          => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            $bookingId = (int)$pdo->lastInsertId();

            /* Load the full row for emailing */
            $stmt = $pdo->prepare('SELECT * FROM bookings WHERE BookingID = ? LIMIT 1');
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();

            /* Send emails */
            if ($form['send_email'] === '1') {
                send_manual_booking_confirmation($booking, false);
            }
            if ($form['send_to_admin'] === '1') {
                send_admin_manual_booking_notice($booking);
            }

            $success = true;

        } catch (Throwable $e) {
            error_log('manual booking insert failed: ' . $e->getMessage());
            $errors[] = 'Could not save the booking. Please try again.';
        }
    }
}

/* ---------------------------------------------------------
   Prefill from query params (e.g. ?tour=island-highlights)
   --------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (!empty($_GET['tour'])) {
        $form['tour'] = $_GET['tour'];
    }
    if (!empty($_GET['source'])) {
        $form['source'] = $_GET['source'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking | FeteCation Admin</title>
    <link rel="stylesheet" href="/fetecation/staff-7742/style.css">
</head>
<body class="admin-body">

<header class="admin-header">

    <div class="admin-header-left">
        <img src="/fetecation/images/logo.png" alt="FeteCation" class="admin-logo">
        <div>
            <h1>New Booking</h1>
            <p>Manual entry</p>
        </div>
    </div>

    <div class="admin-header-right">
        <a href="/fetecation/staff-7742/index.php" class="admin-btn admin-btn-ghost">← Back to Dashboard</a>
        <a href="/fetecation/staff-7742/logout.php" class="admin-btn admin-btn-danger">Log Out</a>
    </div>

</header>


<main class="admin-main">

    <div class="admin-new-booking-wrap">

        <?php if ($success): ?>

            <div class="admin-success">
                <div class="admin-success-icon">✓</div>
                <h2>Booking created</h2>
                <p>
                    Reference <strong><?= htmlspecialchars($reference ?? ''); ?></strong> has been saved.
                    <?php if ($form['send_email'] === '1'): ?>
                        The customer has been emailed a confirmation.
                    <?php endif; ?>
                </p>

                <div class="admin-success-actions">
                    <a href="/fetecation/staff-7742/index.php" class="admin-btn admin-btn-success">View Dashboard</a>
                    <a href="/fetecation/staff-7742/new-booking.php" class="admin-btn admin-btn-ghost">Create Another</a>
                </div>
            </div>

        <?php else: ?>

            <?php if (!empty($errors)): ?>
                <div class="admin-form-errors">
                    <strong>Please fix the following:</strong>
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="/fetecation/staff-7742/new-booking.php" class="admin-new-booking-form">

                <!-- ============ CUSTOMER ============ -->
                <fieldset>
                    <legend>Customer</legend>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required
                                   value="<?= htmlspecialchars($form['name']); ?>">
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required
                                   value="<?= htmlspecialchars($form['email']); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label for="phone">Phone *</label>
                            <input type="tel" id="phone" name="phone" required
                                   value="<?= htmlspecialchars($form['phone']); ?>">
                        </div>
                    </div>
                </fieldset>


                <!-- ============ BOOKING ============ -->
                <fieldset>
                    <legend>Booking Details</legend>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="tour">Tour / Service *</label>
                            <select id="tour" name="tour" required>
                                <option value="taxi" <?= $form['tour']==='taxi'?'selected':''; ?>>Taxi Service</option>
                                <?php foreach (fete_all_tours() as $slug => $t): ?>
                                    <option value="<?= htmlspecialchars($slug); ?>" <?= $form['tour']===$slug?'selected':''; ?>>
                                        <?= htmlspecialchars($t['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="admin-form-group">
                            <label for="source">Source</label>
                            <select id="source" name="source">
                                <?php foreach (['whatsapp','phone','walk-in','website','referral','other'] as $src): ?>
                                    <option value="<?= $src; ?>" <?= $form['source']===$src?'selected':''; ?>>
                                        <?= ucfirst($src); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="date">Date *</label>
                            <input type="date" id="date" name="date" required
                                   value="<?= htmlspecialchars($form['date']); ?>">
                        </div>
                        <div class="admin-form-group">
                            <label for="guests">Guests *</label>
                            <input type="number" id="guests" name="guests" min="1" max="30" required
                                   value="<?= htmlspecialchars($form['guests']); ?>">
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label for="pickup">Pickup Location</label>
                        <input type="text" id="pickup" name="pickup"
                               value="<?= htmlspecialchars($form['pickup']); ?>">
                    </div>

                    <div class="admin-form-group">
                        <label for="notes">Customer Notes</label>
                        <textarea id="notes" name="notes" rows="3"><?= htmlspecialchars($form['notes']); ?></textarea>
                    </div>

                    <div class="admin-form-group">
                        <label for="admin_notes">Internal Admin Notes (not emailed)</label>
                        <textarea id="admin_notes" name="admin_notes" rows="2"><?= htmlspecialchars($form['admin_notes']); ?></textarea>
                    </div>
                </fieldset>


                <!-- ============ PAYMENT ============ -->
                <fieldset>
                    <legend>Payment</legend>

                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="payment_option">Payment Option</label>
                            <select id="payment_option" name="payment_option">
                                <option value="full"    <?= $form['payment_option']==='full'?'selected':''; ?>>Pay in full</option>
                                <option value="deposit" <?= $form['payment_option']==='deposit'?'selected':''; ?>>Pay <?= DEPOSIT_PERCENT; ?>% deposit</option>
                                <option value="on-day"  <?= $form['payment_option']==='on-day'?'selected':''; ?>>Pay on the day</option>
                            </select>
                        </div>

                        <div class="admin-form-group">
                            <label for="custom_price">Custom Price Override ($)</label>
                            <input type="number" id="custom_price" name="custom_price" step="0.01" min="0"
                                   placeholder="Leave blank to auto-calculate"
                                   value="<?= htmlspecialchars($form['custom_price']); ?>">
                        </div>
                    </div>

                    <div class="admin-price-preview" id="adminPricePreview">
                        <!-- Live price preview -->
                    </div>
                </fieldset>


                <!-- ============ NOTIFICATIONS ============ -->
                <fieldset>
                    <legend>Notifications</legend>

                    <label class="admin-checkbox">
                        <input type="checkbox" name="send_email" value="1" <?= $form['send_email']==='1'?'checked':''; ?>>
                        <span>Send a confirmation email to the customer</span>
                    </label>

                    <label class="admin-checkbox">
                        <input type="checkbox" name="send_to_admin" value="1" <?= $form['send_to_admin']==='1'?'checked':''; ?>>
                        <span>Send a notification email to me (admin)</span>
                    </label>
                </fieldset>


                <div class="admin-form-actions">
                    <button type="submit" class="admin-btn admin-btn-success" style="padding:14px 30px;font-size:15px;">
                        Create Booking
                    </button>
                    <a href="/fetecation/staff-7742/index.php" class="admin-btn admin-btn-ghost">Cancel</a>
                </div>

            </form>

        <?php endif; ?>

    </div>

</main>


<script>
(function () {
    'use strict';

    var tourSelect  = document.getElementById('tour');
    var guestsInput = document.getElementById('guests');
    var paySelect   = document.getElementById('payment_option');
    var customInput = document.getElementById('custom_price');
    var previewBox  = document.getElementById('adminPricePreview');

    if (!tourSelect || !previewBox) return;

    var tourPrices = {
        'taxi':              { type: 'per_person', amount: 40.00  },
        'island-highlights': { type: 'per_person', amount: 120.00 },
        'nature-adventure':  { type: 'per_person', amount: 150.00 },
        'custom-island':     { type: 'custom',     amount: null   }
    };

    var depositPercent = <?= (int)DEPOSIT_PERCENT; ?>;

    function money(n) {
        var s = (Math.round(n * 100) / 100).toFixed(2);
        if (s.endsWith('.00')) s = s.slice(0, -3);
        return '$' + s;
    }

    function updatePreview() {
        var tour    = tourSelect.value;
        var guests  = parseInt(guestsInput.value, 10) || 1;
        var option  = paySelect.value;
        var custom  = parseFloat(customInput.value);

        var price = tourPrices[tour];
        var total, perPerson, isCustom = false;

        if (!isNaN(custom) && custom > 0) {
            total = custom;
        } else if (!price || price.type === 'custom' || price.amount === null) {
            isCustom = true;
            total = null;
        } else {
            perPerson = price.amount;
            total = perPerson * guests;
        }

        if (isCustom) {
            previewBox.innerHTML = '<p class="admin-price-hint">Custom tour — no fixed price. Set a Custom Price above to preview totals.</p>';
            return;
        }

        var deposit = total * (depositPercent / 100);
        var due     = total - deposit;

        var html = '<div class="admin-price-row"><span>Total</span><strong>' + money(total) + '</strong></div>';

        if (option === 'deposit') {
            html += '<div class="admin-price-row"><span>Deposit (' + depositPercent + '%)</span><span>' + money(deposit) + '</span></div>';
            html += '<div class="admin-price-row"><span>Due on the day</span><span>' + money(due) + '</span></div>';
        } else if (option === 'on-day') {
            html += '<div class="admin-price-row"><span>Due on the day</span><span>' + money(total) + '</span></div>';
        }

        previewBox.innerHTML = html;
    }

    tourSelect.addEventListener('change', updatePreview);
    guestsInput.addEventListener('input', updatePreview);
    paySelect.addEventListener('change', updatePreview);
    customInput.addEventListener('input', updatePreview);

    updatePreview();
})();
</script>

</body>
</html>