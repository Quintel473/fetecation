<?php
$pageTitle = "My Account";

require_once __DIR__ . '/../includes/customer-auth.php';
require_once __DIR__ . '/../includes/database.php';

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
   Load this customer's bookings from MySQL
   Match by CustomerID OR Email (for older anonymous bookings)
   --------------------------------------------------------- */
$myBookings = [];

try {
    $stmt = $pdo->prepare(
        'SELECT * FROM bookings
         WHERE CustomerID = :cid OR Email = :email
         ORDER BY SubmittedAt DESC'
    );
    $stmt->execute([
        ':cid'   => (int)$customer['CustomerID'],
        ':email' => $customer['Email'],
    ]);
    $myBookings = $stmt->fetchAll();

} catch (Throwable $e) {
    error_log('account bookings load failed: ' . $e->getMessage());
}

/* Split into upcoming vs past */
$today = date('Y-m-d');
$upcoming = [];
$past     = [];

foreach ($myBookings as $b) {
    $date = $b['BookingDate'] ?? '';
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
                                <?php
                                    $status       = strtolower($b['Status'] ?? 'pending');
                                    $tourLabel    = ($b['TourSlug'] === 'taxi' || empty($b['TourSlug']))
                                                    ? 'Taxi Service'
                                                    : ($b['TourSlug'] ?: 'Tour');
                                    $statusClass  = 'status-' . $status;
                                ?>

                                <article class="account-booking">

                                    <div class="account-booking-head">
                                        <span class="account-booking-ref">
                                            <?= htmlspecialchars($b['Reference'] ?? '—'); ?>
                                        </span>
                                        <span class="account-booking-status <?= htmlspecialchars($statusClass); ?>">
                                            <?= htmlspecialchars(ucfirst($status)); ?>
                                        </span>
                                    </div>

                                    <h3><?= htmlspecialchars($tourLabel); ?></h3>

                                    <dl class="account-booking-meta">
                                        <div>
                                            <dt>Date</dt>
                                            <dd><?= htmlspecialchars($b['BookingDate'] ?? '—'); ?></dd>
                                        </div>
                                        <div>
                                            <dt>Guests</dt>
                                            <dd><?= htmlspecialchars($b['NumberOfPassengers'] ?? '—'); ?></dd>
                                        </div>
                                        <?php if (!empty($b['PickupLocation'])): ?>
                                            <div>
                                                <dt>Pickup</dt>
                                                <dd><?= htmlspecialchars($b['PickupLocation']); ?></dd>
                                            </div>
                                        <?php endif; ?>
                                    </dl>

                                    <?php if (!empty($b['Notes'])): ?>
                                        <p class="account-booking-notes">
                                            <?= htmlspecialchars($b['Notes']); ?>
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
                                <?php
                                    $tourLabel = ($b['TourSlug'] === 'taxi' || empty($b['TourSlug']))
                                                 ? 'Taxi Service'
                                                 : ($b['TourSlug'] ?: 'Tour');
                                ?>

                                <article class="account-booking account-booking-compact">

                                    <div>
                                        <strong><?= htmlspecialchars($tourLabel); ?></strong>
                                        <span class="account-booking-meta-inline">
                                            <?= htmlspecialchars($b['BookingDate'] ?? '—'); ?>
                                        </span>
                                    </div>

                                    <span class="account-booking-ref">
                                        <?= htmlspecialchars($b['Reference'] ?? ''); ?>
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