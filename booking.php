<?php

$pageTitle = "Book Your Ride";

require_once __DIR__ . "/tours-data.php";
require_once __DIR__ . "/includes/mailer.php";
require_once __DIR__ . "/includes/customer-auth.php";
require_once __DIR__ . "/includes/database.php";
require_once __DIR__ . "/includes/paypal.php";

/* ---------------------------------------------------------
   Handle form submission
   --------------------------------------------------------- */
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- Collect + trim inputs ----
    $name          = trim($_POST['name']            ?? '');
    $email         = trim($_POST['email']           ?? '');
    $phone         = trim($_POST['phone']           ?? '');
    $date          = trim($_POST['date']            ?? '');
    $guests        = trim($_POST['guests']          ?? '');
    $pickup        = trim($_POST['pickup']          ?? '');
    $notes         = trim($_POST['notes']           ?? '');
    $tour          = trim($_POST['tour']            ?? 'taxi');
    $paymentOption = trim($_POST['payment_option']  ?? 'on-day');

    // ---- Validate ----
    if ($name === '')    $errors[] = 'Please enter your name.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
                         $errors[] = 'Please enter a valid email address.';
    if ($phone === '')   $errors[] = 'Please enter a phone number.';
    if ($date === '')    $errors[] = 'Please choose a date.';
    if ($guests === '' || (int)$guests < 1)
                         $errors[] = 'Please choose how many guests.';
    if (!in_array($paymentOption, ['full', 'deposit', 'on-day'], true))
                         $paymentOption = 'on-day';
    if (empty($_POST['agree']))
                         $errors[] = 'Please agree to the Terms of Service and Privacy Policy.';

    // ---- If valid: save + email + redirect ----
    if (empty($errors)) {

        $reference   = strtoupper(bin2hex(random_bytes(3)));
        $submittedAt = date('Y-m-d H:i:s');

        // Friendly tour name
        $tourName = 'Taxi Service';
        if ($tour !== 'taxi') {
            $t = fete_tour($tour);
            if ($t) $tourName = $t['name'];
        }

        // Link to logged-in customer, if any
        $customerId = null;
        if (customer_is_logged_in()) {
            $current = customer_current();
            if ($current) {
                $customerId = (int)$current['CustomerID'];
            }
        }

        $booking = [
            'reference'      => $reference,
            'customer_id'    => $customerId,
            'tour'           => $tourName,
            'tour_slug'      => $tour,
            'name'           => $name,
            'email'          => $email,
            'phone'          => $phone,
            'date'           => $date,
            'guests'         => $guests,
            'pickup'         => $pickup,
            'notes'          => $notes,
            'payment_option' => $paymentOption,
            'submitted_at'   => $submittedAt,
            'ip'             => $_SERVER['REMOTE_ADDR'] ?? '',
        ];

        // ---- Save to MySQL ----
        try {
            $bookingType = ($tour === 'taxi') ? 'Taxi' : 'Tour';

            /* We use TourSlug as the identifier — no TourID lookup needed */
            $tourId = null;

            /* PaymentOption column — add this to the INSERT */
            $stmt = $pdo->prepare(
                'INSERT INTO bookings
                    (Reference, Name, Email, Phone,
                     CustomerID, BookingType, TourID, TourSlug,
                     PickupLocation, BookingDate, NumberOfPassengers,
                     SpecialRequests, Notes, PaymentOption, Status, SubmittedAt, IP)
                 VALUES
                    (:ref, :name, :email, :phone,
                     :cid, :type, :tid, :slug,
                     :pickup, :bdate, :guests,
                     :notes, :notes2, :payopt, :status, :submitted, :ip)'
            );

            $stmt->execute([
                ':ref'       => $reference,
                ':name'      => $name,
                ':email'     => $email,
                ':phone'     => $phone,
                ':cid'       => $customerId,
                ':type'      => $bookingType,
                ':tid'       => $tourId,
                ':slug'      => $tour,
                ':pickup'    => $pickup,
                ':bdate'     => $date,
                ':guests'    => (int)$guests,
                ':notes'     => $notes,
                ':notes2'    => $notes,
                ':payopt'    => $paymentOption,
                ':status'    => 'Pending',
                ':submitted' => $submittedAt,
                ':ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

        } catch (Throwable $e) {
            error_log('booking insert failed: ' . $e->getMessage());
            $errors[] = 'Could not save your booking. Please try again.';
        }

        // ---- Backup to disk ----
        if (empty($errors)) {
            $dir = __DIR__ . '/data/bookings';
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $filename = $dir . '/' . date('Y-m-d_His') . '_' . $reference . '.json';
            @file_put_contents($filename, json_encode($booking, JSON_PRETTY_PRINT));
        }

        if (empty($errors)) {
            // ---- Send emails ----
            send_booking_notification($booking);
            send_customer_confirmation($booking);

            // ---- Redirect (POST → GET pattern) ----
            header('Location: /fetecation/booking-success.php?ref=' . urlencode($reference));
            exit;
        }
    }
}

/* ---------------------------------------------------------
   Preselect tour from query string (?tour=...)
   --------------------------------------------------------- */
$selectedTour = isset($_GET['tour']) ? htmlspecialchars($_GET['tour']) : '';

/* ---------------------------------------------------------
   Prefill from logged-in customer, if applicable
   --------------------------------------------------------- */
$prefillName  = $_POST['name']  ?? '';
$prefillEmail = $_POST['email'] ?? '';
$prefillPhone = $_POST['phone'] ?? '';

if (customer_is_logged_in() && empty($_POST)) {
    $me = customer_current();
    if ($me) {
        $prefillName  = $prefillName  ?: trim(($me['FirstName'] ?? '') . ' ' . ($me['LastName'] ?? ''));
        $prefillEmail = $prefillEmail ?: ($me['Email'] ?? '');
        $prefillPhone = $prefillPhone ?: ($me['Phone'] ?? '');
    }
}

require_once __DIR__ . "/includes/header.php";

?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">BOOK WITH FETECATION</p>
        <h1>Book Your Ride</h1>
        <p>Tell us a few details and we'll confirm your booking within 24 hours.</p>
    </div>
</section>


<section class="content-section">
    <div class="container booking-container">

        <div class="booking-intro">

            <p class="section-label">BOOKING DETAILS</p>

            <h2>Plan Your Experience</h2>

            <p>
                Whether it's a quick airport transfer or a full day
                of island exploring, we'll take care of it.
            </p>

            <div class="booking-note">
                <strong>What happens next?</strong>
                <p>
                    You'll receive an email confirmation immediately,
                    and a personalised reply from our team within 24 hours.
                </p>
            </div>

            <?php if (!customer_is_logged_in()): ?>
                <div class="booking-note">
                    <strong>Already have an account?</strong>
                    <p>
                        <a href="/fetecation/login.php" style="color:var(--fete-orange-dark);font-weight:700;">
                            Sign in
                        </a>
                        to pre-fill your details and keep track of your bookings.
                    </p>
                </div>
            <?php endif; ?>

        </div>


        <div class="booking-form-card">

            <?php if (!empty($errors)): ?>
                <div class="form-errors">
                    <strong>Please fix the following:</strong>
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="/fetecation/booking.php">

                <div class="form-row">

                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            required
                            value="<?= htmlspecialchars($prefillName); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            value="<?= htmlspecialchars($prefillEmail); ?>"
                        >
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label for="phone">Phone / WhatsApp *</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            required
                            value="<?= htmlspecialchars($prefillPhone); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="guests">Guests *</label>
                        <input
                            type="number"
                            id="guests"
                            name="guests"
                            min="1"
                            max="20"
                            required
                            value="<?= htmlspecialchars($_POST['guests'] ?? ''); ?>"
                        >
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group">
                        <label for="date">Preferred Date *</label>
                        <input
                            type="date"
                            id="date"
                            name="date"
                            required
                            value="<?= htmlspecialchars($_POST['date'] ?? ''); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="pickup">Pickup Location</label>
                        <input
                            type="text"
                            id="pickup"
                            name="pickup"
                            placeholder="Hotel name, cruise port, etc."
                            value="<?= htmlspecialchars($_POST['pickup'] ?? ''); ?>"
                        >
                    </div>

                </div>

                <div class="form-group">
                    <label for="tour">What would you like to book? *</label>
                    <select id="tour" name="tour" required>

                        <option value="taxi" <?= $selectedTour === 'taxi' ? 'selected' : ''; ?>>
                            Taxi Service
                        </option>

                        <?php foreach (fete_all_tours() as $slug => $t): ?>
                            <option
                                value="<?= htmlspecialchars($slug); ?>"
                                <?= $selectedTour === $slug ? 'selected' : ''; ?>
                            >
                                <?= htmlspecialchars($t['name']); ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Additional Notes</label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        placeholder="Flight number, special requests, etc."
                    ><?= htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="payment_option">Payment option *</label>
                    <select id="payment_option" name="payment_option" required>
                        <option value="full" <?= ($_POST['payment_option'] ?? '') === 'full' ? 'selected' : ''; ?>>
                            Pay in full now
                        </option>
                        <option value="deposit" <?= ($_POST['payment_option'] ?? '') === 'deposit' ? 'selected' : ''; ?>>
                            Pay <?= DEPOSIT_PERCENT; ?>% deposit now, rest on the day
                        </option>
                        <option value="on-day" <?= ($_POST['payment_option'] ?? 'on-day') === 'on-day' ? 'selected' : ''; ?>>
                            Pay on the day
                        </option>
                    </select>
                </div>

                <div class="form-group form-group-checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="agree" required>
                        <span>
                            I agree to the
                            <a href="/fetecation/terms.php" target="_blank">Terms of Service</a>
                            and
                            <a href="/fetecation/privacy.php" target="_blank">Privacy Policy</a>.
                        </span>
                    </label>
                </div>

                <button type="submit" class="primary-button">
                    Request Booking
                </button>

            </form>

        </div>

    </div>
</section>


<?php require_once __DIR__ . "/includes/footer.php"; ?>