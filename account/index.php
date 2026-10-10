<?php
$pageTitle = "My Account";

require_once __DIR__ . '/../includes/customer-auth.php';

/* Must be logged in */
if (!customer_is_logged_in()) {
    header('Location: /fetecation/login.php');
    exit;
}

$customer = customer_current();

if (!$customer) {
    customer_logout();
    header('Location: /fetecation/login.php');
    exit;
}

/* ---------------------------------------------------------
   Load this customer's bookings
   Matches by email, since bookings are currently stored as
   JSON files with an email field.
   --------------------------------------------------------- */
$myBookings = [];

$bookingDir = __DIR__ . '/../data/bookings';

if (is_dir($bookingDir)) {

    $email = strtolower($customer['Email']);

    foreach (glob($bookingDir . '/*.json') as $file) {
        $data = json_decode(file_get_contents($file), true);
        if (!is_array($data)) continue;

        $bookingEmail = strtolower($data['email'] ?? '');

        if ($bookingEmail === $email) {
            $myBookings[] = $data;
        }
    }

    /* Newest first */
    usort($myBookings, function ($a, $b) {
        return strcmp($b['submitted_at'] ?? '', $a['submitted_at'] ?? '');
    });
}

/* Split into upcoming vs past */
$today = date('Y-m-d');
$upcoming = [];
$past     = [];

foreach ($myBookings as $b) {
    $date = $b['date'] ?? '';
    if ($date !== '' && $date >= $today) {
        $upcoming[] = $b;
    } else {
        $past[] = $b;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">

    <div class="container page-hero-content">

        <p class="section-label">MY FETECATION</p>

        <h1>Welcome back, <?= htmlspecialchars($customer['FirstName']); ?></h1>

        <p>
            View your bookings, plan your next trip, and manage
            your account in one place.
        </p>

    </div>

</section>


<section class="content-section">

    <div class="container">

        <div class="account-layout">

            <!-- ============ SIDEBAR ============ -->
            <aside class="account-sidebar">

                <div class="account-user-card">

                    <div class="account-avatar">
                        <?= htmlspecialchars(strtoupper(substr($customer['FirstName'], 0, 1))); ?>
                    </div>

                    <h2>
                        <?= htmlspecialchars($customer['FirstName'] . ' ' . $customer['LastName']); ?>
                    </h2>

                    <p class="account-email">
                        <?= htmlspecialchars($customer['Email']); ?>
                    </p>

                    <?php if (!empty($customer['Phone'])): ?>
                        <p class="account-phone">
                            <?= htmlspecialchars($customer['Phone']); ?>
                        </p>
                    <?php endif; ?>

                </div>

                <nav class="account-nav">

                    <a href="/fetecation/account/index.php" class="active">
                        <span>📋</span> My Bookings
                    </a>

                    <a href="/fetecation/account/profile.php">
                        <span>⚙️</span> Profile Settings
                    </a>

                    <a href="/fetecation/booking.php">
                        <span>➕</span> New Booking
                    </a>

                    <a href="/fetecation/account/logout.php" class="account-nav-logout">
                        <span>🚪</span> Sign Out
                    </a>

                </nav>

            </aside>


            <!-- ============ MAIN ============ -->
            <main class="account-main">

                <!-- Stats -->
                <div class="account-stats">

                    <div class="account-stat">
                        <span class="account-stat-label">Total Trips</span>
                        <strong class="account-stat-value"><?= count($myBookings); ?></strong>
                    </div>

                    <div class="account-stat">
                        <span class="account-stat-label">Upcoming</span>
                        <strong class="account-stat-value"><?= count($upcoming); ?></strong>
                    </div>

                    <div class="account-stat">
                        <span class="account-stat-label">Past</span>
                        <strong class="account-stat-value"><?= count($past); ?></strong>
                    </div>

                </div>


                <!-- Upcoming bookings -->
                <section class="account-block">

                    <div class="account-block-header">
                        <h2>Upcoming Trips</h2>
                        <a href="/fetecation/booking.php" class="acct-link">
                            Book a new trip →
                        </a>
                    </div>

                    <?php if (empty($upcoming)): ?>

                        <div class="account-empty">
                            <p>No upcoming trips yet.</p>
                            <a href="/fetecation/booking.php" class="primary-button" style="display:inline-block;margin-top:14px;">
                                Book a Ride
                            </a>
                        </div>

                    <?php else: ?>

                        <div class="account-booking-list">

                            <?php foreach ($upcoming as $b): ?>

                                <article class="account-booking">

                                    <div class="account-booking-head">
                                        <span class="account-booking-ref">
                                            <?= htmlspecialchars($b['reference'] ?? '—'); ?>
                                        </span>
                                        <span class="account-booking-status status-pending">
                                            <?= htmlspecialchars(ucfirst($b['status'] ?? 'pending')); ?>
                                        </span>
                                    </div>

                                    <h3><?= htmlspecialchars($b['tour'] ?? 'Taxi Service'); ?></h3>

                                    <dl class="account-booking-meta">
                                        <div>
                                            <dt>Date</dt>
                                            <dd><?= htmlspecialchars($b['date'] ?? '—'); ?></dd>
                                        </div>
                                        <div>
                                            <dt>Guests</dt>
                                            <dd><?= htmlspecialchars($b['guests'] ?? '—'); ?></dd>
                                        </div>
                                        <?php if (!empty($b['pickup'])): ?>
                                            <div>
                                                <dt>Pickup</dt>
                                                <dd><?= htmlspecialchars($b['pickup']); ?></dd>
                                            </div>
                                        <?php endif; ?>
                                    </dl>

                                    <?php if (!empty($b['notes'])): ?>
                                        <p class="account-booking-notes">
                                            <?= htmlspecialchars($b['notes']); ?>
                                        </p>
                                    <?php endif; ?>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    <?php endif; ?>

                </section>


                <!-- Past bookings -->
                <?php if (!empty($past)): ?>

                    <section class="account-block">

                        <div class="account-block-header">
                            <h2>Past Trips</h2>
                        </div>

                        <div class="account-booking-list account-booking-list-compact">

                            <?php foreach ($past as $b): ?>

                                <article class="account-booking account-booking-compact">

                                    <div>
                                        <strong><?= htmlspecialchars($b['tour'] ?? 'Taxi Service'); ?></strong>
                                        <span class="account-booking-meta-inline">
                                            <?= htmlspecialchars($b['date'] ?? '—'); ?>
                                        </span>
                                    </div>

                                    <span class="account-booking-ref">
                                        <?= htmlspecialchars($b['reference'] ?? ''); ?>
                                    </span>

                                </article>

                            <?php endforeach; ?>

                        </div>

                    </section>

                <?php endif; ?>

            </main>

        </div>

    </div>

</section>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>