<?php
require_once __DIR__ . '/auth.php';

/* ---------------------------------------------------------
   Load all bookings
   --------------------------------------------------------- */
function admin_load_json_files(string $dir): array
{
    $items = [];
    if (!is_dir($dir)) return $items;

    foreach (glob($dir . '/*.json') as $file) {
        $data = json_decode(file_get_contents($file), true);
        if (is_array($data)) {
            $data['_file'] = basename($file);
            $items[] = $data;
        }
    }

    /* Newest first */
    usort($items, function ($a, $b) {
        return strcmp($b['submitted_at'] ?? '', $a['submitted_at'] ?? '');
    });

    return $items;
}

$bookings  = admin_load_json_files(ADMIN_BOOKINGS_DIR);
$messages  = admin_load_json_files(ADMIN_MESSAGES_DIR);

/* Simple counts */
$thisMonth = date('Y-m');
$countThisMonth = 0;
foreach ($bookings as $b) {
    if (strpos($b['submitted_at'] ?? '', $thisMonth) === 0) {
        $countThisMonth++;
    }
}

$confirmed = 0;
foreach ($bookings as $b) {
    if (($b['status'] ?? '') === 'confirmed') $confirmed++;
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <?php
                                $status = $b['status'] ?? 'new';
                                $statusClass = 'status-' . $status;
                            ?>
                            <tr>
                                <td class="cell-mono">
                                    <?= htmlspecialchars($b['reference'] ?? '—'); ?>
                                </td>

                                <td class="cell-date">
                                    <?= htmlspecialchars(substr($b['submitted_at'] ?? '', 0, 16)); ?>
                                </td>

                                <td>
                                    <strong><?= htmlspecialchars($b['name'] ?? '—'); ?></strong>
                                </td>

                                <td class="cell-contact">
                                    <a href="mailto:<?= htmlspecialchars($b['email'] ?? ''); ?>">
                                        <?= htmlspecialchars($b['email'] ?? ''); ?>
                                    </a>
                                    <br>
                                    <a href="tel:<?= htmlspecialchars($b['phone'] ?? ''); ?>">
                                        <?= htmlspecialchars($b['phone'] ?? ''); ?>
                                    </a>
                                </td>

                                <td>
                                    <?= htmlspecialchars($b['tour'] ?? '—'); ?>
                                </td>

                                <td class="cell-date">
                                    <?= htmlspecialchars($b['date'] ?? '—'); ?>
                                </td>

                                <td class="cell-center">
                                    <?= htmlspecialchars($b['guests'] ?? '—'); ?>
                                </td>

                                <td class="cell-center">
                                    <span class="status-badge <?= htmlspecialchars($statusClass); ?>">
                                        <?= htmlspecialchars(ucfirst($status)); ?>
                                    </span>
                                </td>

                                <td class="cell-actions">

                                    <?php if ($status !== 'confirmed'): ?>
                                        <a href="/fetecation/staff-7742/action.php?type=booking&action=confirm&file=<?= urlencode($b['_file']); ?>"
                                           class="admin-btn admin-btn-sm admin-btn-success">
                                            Confirm
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($status !== 'cancelled'): ?>
                                        <a href="/fetecation/staff-7742/action.php?type=booking&action=cancel&file=<?= urlencode($b['_file']); ?>"
                                           class="admin-btn admin-btn-sm admin-btn-warn">
                                            Cancel
                                        </a>
                                    <?php endif; ?>

                                    <a href="/fetecation/staff-7742/action.php?type=booking&action=delete&file=<?= urlencode($b['_file']); ?>"
                                       class="admin-btn admin-btn-sm admin-btn-danger"
                                       onclick="return confirm('Delete this booking permanently?');">
                                        Delete
                                    </a>

                                </td>
                            </tr>

                            <?php if (!empty($b['notes']) || !empty($b['pickup'])): ?>
                                <tr class="detail-row">
                                    <td colspan="9">
                                        <?php if (!empty($b['pickup'])): ?>
                                            <span class="detail-item">
                                                <strong>Pickup:</strong> <?= htmlspecialchars($b['pickup']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($b['notes'])): ?>
                                            <span class="detail-item">
                                                <strong>Notes:</strong> <?= htmlspecialchars($b['notes']); ?>
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
                                    <?= htmlspecialchars(substr($m['submitted_at'] ?? '', 0, 16)); ?>
                                </td>

                                <td>
                                    <strong><?= htmlspecialchars($m['name'] ?? '—'); ?></strong>
                                </td>

                                <td class="cell-contact">
                                    <a href="mailto:<?= htmlspecialchars($m['email'] ?? ''); ?>">
                                        <?= htmlspecialchars($m['email'] ?? ''); ?>
                                    </a>
                                    <?php if (!empty($m['phone'])): ?>
                                        <br>
                                        <a href="tel:<?= htmlspecialchars($m['phone']); ?>">
                                            <?= htmlspecialchars($m['phone']); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($m['subject'] ?? '—'); ?>
                                </td>

                                <td class="cell-message">
                                    <?= nl2br(htmlspecialchars($m['message'] ?? '')); ?>
                                </td>

                                <td class="cell-actions">
                                    <a href="/fetecation/staff-7742/action.php?type=message&action=delete&file=<?= urlencode($m['_file']); ?>"
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