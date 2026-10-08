<?php

$pageTitle = "Book Your Ride";

require_once __DIR__ . "/tours-data.php";
require_once __DIR__ . "/includes/mailer.php";

/* ---------------------------------------------------------
   Handle form submission
   --------------------------------------------------------- */
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- Collect + trim inputs ----
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $date    = trim($_POST['date']    ?? '');
    $guests  = trim($_POST['guests']  ?? '');
    $pickup  = trim($_POST['pickup']  ?? '');
    $notes   = trim($_POST['notes']   ?? '');
    $tour    = trim($_POST['tour']    ?? 'taxi');

    // ---- Validate ----
    if ($name === '')    $errors[] = 'Please enter your name.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
                         $errors[] = 'Please enter a valid email address.';
    if ($phone === '')   $errors[] = 'Please enter a phone number.';
    if ($date === '')    $errors[] = 'Please choose a date.';
    if ($guests === '' || (int)$guests < 1)
                         $errors[] = 'Please choose how many guests.';

    // ---- If valid: save + email + redirect ----
    if (empty($errors)) {

        $reference   = strtoupper(bin2hex(random_bytes(3))); // e.g. A3F9C1
        $submittedAt = date('Y-m-d H:i:s');

        // Friendly tour name
        $tourName = 'Taxi Service';
        if ($tour !== 'taxi') {
            $t = fete_tour($tour);
            if ($t) $tourName = $t['name'];
        }

        $booking = [
            'reference'    => $reference,
            'tour'         => $tourName,
            'tour_slug'    => $tour,
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'date'         => $date,
            'guests'       => $guests,
            'pickup'       => $pickup,
            'notes'        => $notes,
            'submitted_at' => $submittedAt,
            'ip'           => $_SERVER['REMOTE_ADDR'] ?? '',
        ];

        // ---- Save to disk ----
        $dir = __DIR__ . '/data/bookings';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $filename = $dir . '/' . date('Y-m-d_His') . '_' . $reference . '.json';
        @file_put_contents($filename, json_encode($booking, JSON_PRETTY_PRINT));

        // ---- Send emails ----
        send_booking_notification($booking);
        send_customer_confirmation($booking);

        // ---- Redirect (POST → GET pattern) ----
        header('Location: /fetecation/booking-success.php?ref=' . urlencode($reference));
        exit;
    }
}

/* ---------------------------------------------------------
   Preselect tour from query string (?tour=...)
   --------------------------------------------------------- */
$selectedTour = isset($_GET['tour']) ? htmlspecialchars($_GET['tour']) : '';

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
                            value="<?= htmlspecialchars($_POST['name'] ?? ''); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
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
                            value="<?= htmlspecialchars($_POST['phone'] ?? ''); ?>"
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

                <button type="submit" class="primary-button">
                    Request Booking
                </button>

            </form>

        </div>

    </div>
</section>


<?php require_once __DIR__ . "/includes/footer.php"; ?>