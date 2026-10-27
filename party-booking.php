<?php
$pageTitle = "Party Booking";

require_once __DIR__ . "/tours-data.php";
require_once __DIR__ . "/includes/mailer.php";
require_once __DIR__ . "/includes/customer-auth.php";
require_once __DIR__ . "/includes/database.php";
require_once __DIR__ . "/includes/paypal.php";

/* Default to the first party if none selected */
$selectedTour = isset($_GET['tour']) ? htmlspecialchars($_GET['tour']) : 'friday-night-lime';
$tour = fete_tour($selectedTour);

if (!$tour || ($tour['tag'] ?? '') !== 'PARTY PACKAGE') {
    header('Location: /fetecation/parties.php');
    exit;
}

$errors = [];

/* Prefill from customer account */
$prefillName = '';
$prefillEmail = '';
$prefillPhone = '';

if (customer_is_logged_in()) {
    $me = customer_current();
    if ($me) {
        $prefillName  = trim(($me['FirstName'] ?? '') . ' ' . ($me['LastName'] ?? ''));
        $prefillEmail = $me['Email'] ?? '';
        $prefillPhone = $me['Phone'] ?? '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']   ?? '');
    $email  = trim($_POST['email']  ?? '');
    $phone  = trim($_POST['phone']  ?? '');
    $date   = trim($_POST['date']   ?? '');
    $guests = (int)($_POST['guests'] ?? 1);
    $pickup = trim($_POST['pickup'] ?? '');
    $notes  = trim($_POST['notes']  ?? '');
    
    /* Collect and clean multiple stops */
    $stops = array_filter(array_map('trim', $_POST['stops'] ?? []));
    $multiStops = implode(', ', $stops);
    
    if ($name === '' || $email === '' || $date === '' || $pickup === '') {
        $errors[] = 'Please fill in all required fields.';
    }

    if (empty($errors)) {
        $reference   = fete_generate_reference();
        $submittedAt = date('Y-m-d H:i:s');

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO bookings
                    (Reference, Name, Email, Phone, BookingType, TourSlug,
                     PickupLocation, MultiStops, BookingDate, NumberOfPassengers,
                     SpecialRequests, PaymentOption, Status, SubmittedAt)
                 VALUES
                    (:ref, :name, :email, :phone, "Tour", :slug,
                     :pickup, :multistops, :bdate, :guests,
                     :notes, "deposit", "Pending", :submitted)'
            );
            
            $stmt->execute([
                ':ref'        => $reference,
                ':name'       => $name,
                ':email'      => $email,
                ':phone'      => $phone,
                ':slug'       => $selectedTour,
                ':pickup'     => $pickup,
                ':multistops' => $multiStops,
                ':bdate'      => $date,
                ':guests'     => $guests,
                ':notes'      => $notes,
                ':submitted'  => $submittedAt
            ]);

            /* Send emails using existing functions */
            $bookingEmailData = [
                'reference'    => $reference,
                'tour'         => $tour['name'],
                'name'         => $name,
                'email'        => $email,
                'phone'        => $phone,
                'date'         => $date,
                'guests'       => $guests,
                'pickup'       => $pickup . " | Stops: " . $multiStops,
                'notes'        => $notes,
                'submitted_at' => $submittedAt
            ];
            
            send_booking_notification($bookingEmailData);
            send_customer_confirmation($bookingEmailData);

            header('Location: /fetecation/booking-success.php?ref=' . urlencode($reference));
            exit;

        } catch (Throwable $e) {
            error_log('Party booking failed: ' . $e->getMessage());
            $errors[] = 'Could not save your booking. Please try again.';
        }
    }
}

require_once __DIR__ . "/includes/header.php";
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">PARTY PACKAGES</p>
        <h1>Book Your Party</h1>
        <p>Private vehicle, dedicated driver, and all your friends.</p>
    </div>
</section>

<section class="content-section">
    <div class="container booking-container">

        <!-- Sidebar: Flyer & Details -->
        <div class="booking-intro">
            <h2><?= htmlspecialchars($tour['name']); ?></h2>
            <p style="color:var(--fete-gray); margin-bottom:20px;"><?= htmlspecialchars($tour['tagline']); ?></p>
            
            <img src="<?= htmlspecialchars($tour['image']); ?>" alt="Flyer" style="width:100%; border-radius:12px; box-shadow:var(--shadow); margin-bottom:20px;">
            
            <div class="booking-note">
                <strong>Base Price: <?= htmlspecialchars($tour['price']); ?></strong>
                <p>Includes a private vehicle and driver for up to <?= htmlspecialchars($tour['group']); ?>.</p>
            </div>
            
            <div class="booking-note" style="margin-top:14px; border-left-color:var(--fete-yellow);">
                <strong>Extra Stops</strong>
                <p>Need to drop off friends at different locations? It's just $<?= number_format($tour['multi_stop_price'], 2); ?> per extra stop.</p>
            </div>
        </div>

        <!-- Main Form -->
        <div class="booking-form-card">
            <?php if (!empty($errors)): ?>
                <div class="form-errors">
                    <strong>Please fix:</strong>
                    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($prefillName); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($prefillEmail); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($prefillPhone); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Party *</label>
                        <input type="date" name="date" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Number of Guests *</label>
                        <input type="number" name="guests" min="1" max="30" value="10" required>
                    </div>
                    <div class="form-group">
                        <label>Main Pickup Location *</label>
                        <input type="text" name="pickup" placeholder="e.g., Radisson Hotel" required>
                    </div>
                </div>

                <!-- Multi-Stop Section -->
                <div class="form-group" style="background:var(--fete-light); padding:20px; border-radius:8px; border:1px solid var(--fete-border); margin-bottom:24px;">
                    <label style="margin-bottom:10px; display:block; color:var(--fete-orange-dark);">🚐 Drop-off Points / Club Stops</label>
                    <p style="font-size:13px; color:var(--fete-gray); margin-bottom:14px;">Add the locations you want to visit or where your friends need to be dropped off.</p>
                    
                    <div id="stops-container">
                        <input type="text" name="stops[]" placeholder="e.g., Bananas Nightclub" style="margin-bottom:10px;">
                    </div>
                    
                    <button type="button" onclick="addStop()" class="secondary-button" style="padding:8px 16px; font-size:13px; display:inline-block;">
                        + Add Another Stop ($<?= number_format($tour['multi_stop_price'], 2); ?>)
                    </button>
                </div>

                <div class="form-group">
                    <label>Special Requests (Drinks, music, VIP entry, etc.)</label>
                    <textarea name="notes" rows="3" placeholder="Let us know how to make the night perfect..."></textarea>
                </div>

                <button type="submit" class="primary-button" style="width:100%;">
                    Request Party Booking
                </button>

            </form>
        </div>

    </div>
</section>

<script>
function addStop() {
    var container = document.getElementById('stops-container');
    var input = document.createElement('input');
    input.type = 'text';
    input.name = 'stops[]';
    input.placeholder = 'Next stop...';
    input.style.marginBottom = '10px';
    container.appendChild(input);
}
</script>

<?php require_once __DIR__ . "/includes/footer.php"; ?>