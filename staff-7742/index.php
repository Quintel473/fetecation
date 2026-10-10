<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/database.php';

/* ---------------------------------------------------------
   Load bookings from MySQL
   --------------------------------------------------------- */
$bookings = [];
try {
    $stmt = $pdo->query(
        'SELECT * FROM bookings
         ORDER BY SubmittedAt DESC'
    );
    $bookings = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('admin load bookings failed: ' . $e->getMessage());
}

/* ---------------------------------------------------------
   Load messages from MySQL
   --------------------------------------------------------- */
$messages = [];
try {
    $stmt = $pdo->query(
        'SELECT * FROM messages
         ORDER BY CreatedAt DESC'
    );
    $messages = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('admin load messages failed: ' . $e->getMessage());
}

/* ---------------------------------------------------------
   Stats
   --------------------------------------------------------- */
$thisMonth = date('Y-m');
$countThisMonth = 0;
foreach ($bookings as $b) {
    if (strpos($b['SubmittedAt'] ?? '', $thisMonth) === 0) {
        $countThisMonth++;
    }
}

$confirmed = 0;
foreach ($bookings as $b) {
    if (strtolower($b['Status'] ?? '') === 'confirmed') $confirmed++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | FeteCation</title>
    <link rel="stylesheet" href="/fetecation/staff-7742/style.css">
</head>
<body class="admin-body">

<header class="admin-header">

    <div class="admin-header-left">

        <img
            src="/fetecation/images/logo.png"
            alt="FeteCation"
            class="admin-logo"
        >

        <div>
            <h1>FeteCation Admin</h1>
            <p>Bookings &amp; Messages</p>
        </div>

    </div>

    <div class="admin-header-right">
        <span class="admin-user">
            Signed in as <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?></strong>
        </span>
        <a href="/fetecation/" class="admin-btn admin-btn-ghost">View Site</a>
        <a href="/fetecation/staff-7742/logout.php" class="admin-btn admin-btn-danger">Log Out</a>
    </div>

</header>


<main class="admin-main">

    <!-- ============ STATS ============ -->
    <section class="admin-stats">

        <div class="stat-card">
            <span class="stat-label">Total Bookings</span>
            <strong class="stat-value"><?= count($bookings); ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">This Month</span>
            <strong class="stat-value"><?= $countThisMonth; ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">Confirmed</span>
            <strong class="stat-value"><?= $confirmed; ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">Messages</span>
            <strong class="stat-value"><?= count($messages); ?></strong>
        </div>

    </section>


    <!-- ============ BOOKINGS ============ -->
    <section class="admin-section">

        <div class="admin-section-header">
            <h2>Bookings</h2>
            <span class="admin-count"><?= count($bookings); ?> total</span>
        </div>

        <?php if (empty($bookings)): ?>

            <div class="admin-empty">
                <p>No bookings yet. When someone books through the site, they'll appear here.</p>
            </div>

        <?php else: ?>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Submitted</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Tour</th>
                            <th>Date</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <?php
                                $status = strtolower($b['Status'] ?? 'pending');
                                $statusClass = 'status-' . $status;
                                $tourName = $b['TourSlug'] === 'taxi'
                                    ? 'Taxi Service'
                                    : ($b['TourSlug'] ?: 'Tour');

                                $payOpt    = $b['PaymentOption'] ?? 'on-day';
                                $payLabels = [
                                    'full'    => '💰 Full',
                                    'deposit' => '🔒 Deposit',
                                    'on-day'  => '💵 On Day',
                                ];
                                $payLabel = $payLabels[$payOpt] ?? '💵 On Day';
                            ?>
                            <tr>
                                <td class="cell-mono">
                                    <?= htmlspecialchars($b['Reference'] ?? '—'); ?>
                                </td>

                                <td class="cell-date">
                                    <?= htmlspecialchars(substr($b['SubmittedAt'] ?? '', 0, 16)); ?>
                                </td>

                                <td>
                                    <strong><?= htmlspecialchars($b['Name'] ?? '—'); ?></strong>
                                </td>

                                <td class="cell-contact">
                                    <a href="mailto:<?= htmlspecialchars($b['Email'] ?? ''); ?>">
                                        <?= htmlspecialchars($b['Email'] ?? ''); ?>
                                    </a>
                                    <?php if (!empty($b['Phone'])): ?>
                                        <br>
                                        <a href="tel:<?= htmlspecialchars($b['Phone']); ?>">
                                            <?= htmlspecialchars($b['Phone']); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($tourName); ?>
                                </td>

                                <td class="cell-date">
                                    <?= htmlspecialchars($b['BookingDate'] ?? '—'); ?>
                                </td>

                                <td class="cell-center">
                                    <?= htmlspecialchars($b['NumberOfPassengers'] ?? '—'); ?>
                                </td>

                                <td class="cell-center">
                                    <span class="status-badge <?= htmlspecialchars($statusClass); ?>">
                                        <?= htmlspecialchars(ucfirst($status)); ?>
                                    </span>
                                </td>

                                <td class="cell-center">
                                    <small style="font-size:12px;color:#666;white-space:nowrap;">
                                        <?= htmlspecialchars($payLabel); ?>
                                    </small>
                                </td>

                                <td class="cell-actions">

                                    <?php if ($status !== 'confirmed'): ?>
                                        <a href="/fetecation/staff-7742/action.php?type=booking&action=confirm&id=<?= (int)$b['BookingID']; ?>"
                                           class="admin-btn admin-btn-sm admin-btn-success">
                                            Confirm
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($status !== 'cancelled'): ?>
                                        <a href="/fetecation/staff-7742/action.php?type=booking&action=cancel&id=<?= (int)$b['BookingID']; ?>"
                                           class="admin-btn admin-btn-sm admin-btn-warn">
                                            Cancel
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($status !== 'cancelled' && $payOpt !== 'on-day'): ?>
                                        <a href="/fetecation/staff-7742/action.php?type=booking&action=send-payment&id=<?= (int)$b['BookingID']; ?>"
                                           class="admin-btn admin-btn-sm admin-btn-ghost"
                                           title="Email the customer a PayPal payment link">
                                            💳 Send Payment
                                        </a>
                                    <?php endif; ?>

                                    <a href="/fetecation/staff-7742/action.php?type=booking&action=delete&id=<?= (int)$b['BookingID']; ?>"
                                       class="admin-btn admin-btn-sm admin-btn-danger"
                                       onclick="return confirm('Delete this booking permanently?');">
                                        Delete
                                    </a>

                                </td>
                            </tr>

                            <?php if (!empty($b['Notes']) || !empty($b['PickupLocation'])): ?>
                                <tr class="detail-row">
                                    <td colspan="10">
                                        <?php if (!empty($b['PickupLocation'])): ?>
                                            <span class="detail-item">
                                                <strong>Pickup:</strong> <?= htmlspecialchars($b['PickupLocation']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($b['Notes'])): ?>
                                            <span class="detail-item">
                                                <strong>Notes:</strong> <?= htmlspecialchars($b['Notes']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </section>


    <!-- ============ MESSAGES ============ -->
    <section class="admin-section">

        <div class="admin-section-header">
            <h2>Contact Messages</h2>
            <span class="admin-count"><?= count($messages); ?> total</span>
        </div>

        <?php if (empty($messages)): ?>

            <div class="admin-empty">
                <p>No messages yet. When someone uses the contact form, their message will appear here.</p>
            </div>

        <?php else: ?>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Submitted</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                            <tr>
                                <td class="cell-date">
                                    <?= htmlspecialchars(substr($m['CreatedAt'] ?? '', 0, 16)); ?>
                                </td>

                                <td>
                                    <strong><?= htmlspecialchars($m['Name'] ?? '—'); ?></strong>
                                </td>

                                <td class="cell-contact">
                                    <a href="mailto:<?= htmlspecialchars($m['Email'] ?? ''); ?>">
                                        <?= htmlspecialchars($m['Email'] ?? ''); ?>
                                    </a>
                                    <?php if (!empty($m['Phone'])): ?>
                                        <br>
                                        <a href="tel:<?= htmlspecialchars($m['Phone']); ?>">
                                            <?= htmlspecialchars($m['Phone']); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($m['Subject'] ?? '—'); ?>
                                </td>

                                <td class="cell-message">
                                    <?= nl2br(htmlspecialchars($m['Message'] ?? '')); ?>
                                </td>

                                <td class="cell-actions">
                                    <a href="/fetecation/staff-7742/action.php?type=message&action=delete&id=<?= (int)$m['MessageID']; ?>"
                                       class="admin-btn admin-btn-sm admin-btn-danger"
                                       onclick="return confirm('Delete this message permanently?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>