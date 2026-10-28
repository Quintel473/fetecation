<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/database.php';

// Fetch all tours and parties from the DB
$items = [];
try {
    $stmt = $pdo->query('SELECT * FROM tours ORDER BY type ASC, name ASC');
    $items = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('Failed to load tours: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tours & Parties | FeteCation Admin</title>
    <link rel="stylesheet" href="/fetecation/staff-7742/style.css">
</head>
<body class="admin-body">

<header class="admin-header">
    <div class="admin-header-left">
        <img src="/fetecation/images/logo.png" alt="FeteCation" class="admin-logo">
        <div>
            <h1>Manage Tours &amp; Parties</h1>
            <p>Update prices, images & details</p>
        </div>
    </div>
    <div class="admin-header-right">
        <a href="/fetecation/staff-7742/index.php" class="admin-btn admin-btn-ghost">← Back to Bookings</a>
        <a href="/fetecation/staff-7742/logout.php" class="admin-btn admin-btn-danger">Log Out</a>
    </div>
</header>

<main class="admin-main">

    <section class="admin-section">
        
        <div class="admin-section-header">
            <h2>All Items</h2>
            <div style="display:flex; align-items:center; gap:14px;">
                <span class="admin-count"><?= count($items); ?> total</span>
                <a href="/fetecation/staff-7742/add-tour.php" class="admin-btn admin-btn-success">
                    ➕ Add New Item
                </a>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['type'] === 'party'): ?>
                                    <span class="status-badge status-confirmed">Party</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">Tour</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($item['name']); ?></strong><br>
                                <small style="color:#666;"><?= htmlspecialchars($item['slug']); ?></small>
                            </td>
                            <td><?= htmlspecialchars($item['price_display']); ?></td>
                            <td><?= htmlspecialchars($item['duration']); ?></td>
                            <td class="cell-actions">
                                <a href="/fetecation/staff-7742/edit-tour.php?id=<?= (int)$item['id']; ?>" 
                                   class="admin-btn admin-btn-sm admin-btn-ghost">
                                    ✏️ Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </section>

</main>
</body>
</html>