<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/database.php';

/* ---------------------------------------------------------
   Pagination setup
   --------------------------------------------------------- */
const PER_PAGE = 20;

$bookingsPage = max(1, (int)($_GET['bpage'] ?? 1));
$messagesPage = max(1, (int)($_GET['mpage'] ?? 1));

/* ---------------------------------------------------------
   Search + filter input
   --------------------------------------------------------- */
$search       = trim($_GET['q']      ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$sourceFilter = trim($_GET['source'] ?? '');

$allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
if ($statusFilter !== '' && !in_array(strtolower($statusFilter), $allowedStatuses, true)) {
    $statusFilter = '';
}

$allowedSources = ['website', 'whatsapp', 'phone', 'walk-in', 'referral', 'other'];
if ($sourceFilter !== '' && !in_array(strtolower($sourceFilter), $allowedSources, true)) {
    $sourceFilter = '';
}

/* ---------------------------------------------------------
   Build WHERE clause for bookings
   --------------------------------------------------------- */
$where  = [];
$params = [];

if ($search !== '') {
    $where[] = '(
        Name       LIKE :search
        OR Email   LIKE :search
        OR Phone   LIKE :search
        OR Reference LIKE :search
    )';
    $params[':search'] = '%' . $search . '%';
}

if ($statusFilter !== '') {
    $where[] = 'LOWER(Status) = :status';
    $params[':status'] = strtolower($statusFilter);
}

if ($sourceFilter !== '') {
    $where[] = 'LOWER(Source) = :source';
    $params[':source'] = strtolower($sourceFilter);
}

$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

/* ---------------------------------------------------------
   Load filtered bookings (paginated)
   --------------------------------------------------------- */
$bookings = [];
$bookingsTotal = 0;
$bookingsPages = 1;

try {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM bookings' . $whereSql);
    $stmt->execute($params);
    $bookingsTotal = (int)$stmt->fetchColumn();

    $bookingsPages  = max(1, (int)ceil($bookingsTotal / PER_PAGE));
    $bookingsPage   = min($bookingsPage, $bookingsPages);
    $bookingsOffset = ($bookingsPage - 1) * PER_PAGE;

    $stmt = $pdo->prepare(
        'SELECT * FROM bookings' . $whereSql . '
         ORDER BY SubmittedAt DESC
         LIMIT :limit OFFSET :offset'
    );

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit',  PER_PAGE, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $bookingsOffset, PDO::PARAM_INT);
    $stmt->execute();
    $bookings = $stmt->fetchAll();

} catch (Throwable $e) {
    error_log('admin load bookings failed: ' . $e->getMessage());
}

/* ---------------------------------------------------------
   Load messages
   --------------------------------------------------------- */
$messages = [];
$messagesTotal = 0;
$messagesPages = 1;

try {
    $messagesTotal = (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
    $messagesPages = max(1, (int)ceil($messagesTotal / PER_PAGE));

    if ($messagesPage > $messagesPages) {
        $messagesPage = $messagesPages;
    }
    $messagesOffset = ($messagesPage - 1) * PER_PAGE;

    $stmt = $pdo->prepare(
        'SELECT * FROM messages
         ORDER BY CreatedAt DESC
         LIMIT :limit OFFSET :offset'
    );
    $stmt->bindValue(':limit',  PER_PAGE, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $messagesOffset, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll();

} catch (Throwable $e) {
    error_log('admin load messages failed: ' . $e->getMessage());
}

/* ---------------------------------------------------------
   Stats
   --------------------------------------------------------- */
$thisMonth = date('Y-m');
$countThisMonth = 0;
$confirmed = 0;
$grandTotalBookings = 0;

try {
    $grandTotalBookings = (int)$pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn();

    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM bookings
         WHERE DATE_FORMAT(SubmittedAt, "%Y-%m") = ?'
    );
    $stmt->execute([$thisMonth]);
    $countThisMonth = (int)$stmt->fetchColumn();

    $confirmed = (int)$pdo->query(
        'SELECT COUNT(*) FROM bookings WHERE LOWER(Status) = "confirmed"'
    )->fetchColumn();

} catch (Throwable $e) {
    error_log('admin stats failed: ' . $e->getMessage());
}

/* ---------------------------------------------------------
   Return query string for action links
   --------------------------------------------------------- */
$returnParams = [
    'bpage'  => $bookingsPage,
    'mpage'  => $messagesPage,
];
if ($search !== '')       $returnParams['q']      = $search;
if ($statusFilter !== '') $returnParams['status'] = $statusFilter;
if ($sourceFilter !== '') $returnParams['source'] = $sourceFilter;

$returnQs = urlencode('?' . http_build_query($returnParams));

/* ---------------------------------------------------------
   Helper — build a paginated URL
   --------------------------------------------------------- */
function admin_page_url(
    int $bookingsPage,
    int $messagesPage,
    string $search,
    string $status,
    string $source
): string {
    $params = [];
    if ($bookingsPage > 1) $params['bpage']  = $bookingsPage;
    if ($messagesPage > 1) $params['mpage']  = $messagesPage;
    if ($search !== '')    $params['q']      = $search;
    if ($status !== '')    $params['status'] = $status;
    if ($source !== '')    $params['source'] = $source;
    $qs = $params ? '?' . http_build_query($params) : '';
    return '/fetecation/staff-7742/index.php' . $qs;
}

/* ---------------------------------------------------------
   Helper — render a pagination bar
   --------------------------------------------------------- */
function admin_pagination(
    int $currentPage,
    int $totalPages,
    string $paramName,
    int $bookingsPage,
    int $messagesPage,
    int $totalItems,
    string $search,
    string $status,
    string $source
): string {

    if ($totalPages <= 1) return '';

    $pages = [];
    $pages[] = 1;
    for ($i = max(2, $currentPage - 2); $i <= min($totalPages - 1, $currentPage + 2); $i++) {
        $pages[] = $i;
    }
    if ($totalPages > 1) $pages[] = $totalPages;
    $pages = array_values(array_unique($pages));
    sort($pages);

    $urlFor = function (int $page) use ($paramName, $bookingsPage, $messagesPage, $search, $status, $source): string {
        $b = $paramName === 'bpage' ? $page : $bookingsPage;
        $m = $paramName === 'mpage' ? $page : $messagesPage;
        return admin_page_url($b, $m, $search, $status, $source);
    };

    $html  = '<nav class="admin-pagination" aria-label="Pagination">';
    $html .= '<div class="admin-pagination-info">'
           . number_format($totalItems) . ' total · page ' . $currentPage . ' of ' . $totalPages
           . '</div>';
    $html .= '<div class="admin-pagination-pages">';

    if ($currentPage > 1) {
        $html .= '<a class="admin-page-link" href="' . htmlspecialchars($urlFor($currentPage - 1)) . '">← Prev</a>';
    } else {
        $html .= '<span class="admin-page-link is-disabled">← Prev</span>';
    }

    $last = 0;
    foreach ($pages as $p) {
        if ($last && $p - $last > 1) {
            $html .= '<span class="admin-page-ellipsis">…</span>';
        }
        if ($p === $currentPage) {
            $html .= '<span class="admin-page-link is-current">' . $p . '</span>';
        } else {
            $html .= '<a class="admin-page-link" href="' . htmlspecialchars($urlFor($p)) . '">' . $p . '</a>';
        }
        $last = $p;
    }

    if ($currentPage < $totalPages) {
        $html .= '<a class="admin-page-link" href="' . htmlspecialchars($urlFor($currentPage + 1)) . '">Next →</a>';
    } else {
        $html .= '<span class="admin-page-link is-disabled">Next →</span>';
    }

    $html .= '</div></nav>';

    return $html;
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
        <img src="/fetecation/images/logo.png" alt="FeteCation" class="admin-logo">
        <div>
            <h1>FeteCation Admin</h1>
            <p>Bookings &amp; Messages</p>
        </div>
    </div>

    <div class="admin-header-right">
        <span class="admin-user">
            Signed in as <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? 'admin'); ?></strong>
        </span>
        <a href="/fetecation/staff-7742/new-booking.php" class="admin-btn admin-btn-success">➕ New Booking</a>
        <a href="/fetecation/" class="admin-btn admin-btn-ghost">View Site</a>
        <a href="/fetecation/staff-7742/logout.php" class="admin-btn admin-btn-danger">Log Out</a>
    </div>

</header>


<main class="admin-main">

    <!-- ============ STATS ============ -->
    <section class="admin-stats">

        <div class="stat-card">
            <span class="stat-label">Total Bookings</span>
            <strong class="stat-value"><?= number_format($grandTotalBookings); ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">This Month</span>
            <strong class="stat-value"><?= number_format($countThisMonth); ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">Confirmed</span>
            <strong class="stat-value"><?= number_format($confirmed); ?></strong>
        </div>

        <div class="stat-card">
            <span class="stat-label">Messages</span>
            <strong class="stat-value"><?= number_format($messagesTotal); ?></strong>
        </div>

    </section>


    <!-- ============ BOOKINGS ============ -->
    <section class="admin-section">

        <div class="admin-section-header">
            <h2>Bookings</h2>
            <span class="admin-count">
                <?= number_format($bookingsTotal); ?> shown
                <?php if ($bookingsTotal !== $grandTotalBookings): ?>
                    of <?= number_format($grandTotalBookings); ?>
                <?php else: ?>
                    total
                <?php endif; ?>
            </span>
        </div>


        <!-- ============ SEARCH + FILTERS ============ -->
        <form method="get" action="/fetecation/staff-7742/index.php" class="admin-filters">

            <div class="admin-filter-group admin-filter-search">
                <label for="q">Search</label>
                <input
                    type="text"
                    id="q"
                    name="q"
                    placeholder="Name, email, phone or reference…"
                    value="<?= htmlspecialchars($search); ?>"
                >
            </div>

            <div class="admin-filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    <option value="pending"   <?= strtolower($statusFilter) === 'pending'   ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?= strtolower($statusFilter) === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="cancelled" <?= strtolower($statusFilter) === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="completed" <?= strtolower($statusFilter) === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>

            <div class="admin-filter-group">
                <label for="source">Source</label>
                <select id="source" name="source">
                    <option value="">All sources</option>
                    <option value="website"  <?= strtolower($sourceFilter) === 'website'  ? 'selected' : ''; ?>>🌐 Website</option>
                    <option value="whatsapp" <?= strtolower($sourceFilter) === 'whatsapp' ? 'selected' : ''; ?>>💬 WhatsApp</option>
                    <option value="phone"    <?= strtolower($sourceFilter) === 'phone'    ? 'selected' : ''; ?>>📞 Phone</option>
                    <option value="walk-in"  <?= strtolower($sourceFilter) === 'walk-in'  ? 'selected' : ''; ?>>🚶 Walk-in</option>
                    <option value="referral" <?= strtolower($sourceFilter) === 'referral' ? 'selected' : ''; ?>>👥 Referral</option>
                    <option value="other"    <?= strtolower($sourceFilter) === 'other'    ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="admin-filter-actions">
                <button type="submit" class="admin-btn admin-btn-success">🔍 Apply</button>

                <?php if ($search !== '' || $statusFilter !== '' || $sourceFilter !== ''): ?>
                    <a href="/fetecation/staff-7742/index.php" class="admin-btn admin-btn-ghost">Clear</a>
                <?php endif; ?>
            </div>

        </form>


        <?php if (empty($bookings)): ?>

            <div class="admin-empty">
                <?php if ($search !== '' || $statusFilter !== '' || $sourceFilter !== ''): ?>
                    <p>No bookings match your filters.</p>
                    <a href="/fetecation/staff-7742/index.php" class="admin-btn admin-btn-ghost" style="margin-top:14px;">
                        Clear filters
                    </a>
                <?php else: ?>
                    <p>No bookings yet. When someone books through the site, they'll appear here.</p>
                <?php endif; ?>
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
                            <th>Source</th>
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

                                $source = $b['Source'] ?? 'website';
                                $sourceIcons = [
                                    'website'  => '🌐',
                                    'whatsapp' => '💬',
                                    'phone'    => '📞',
                                    'walk-in'  => '🚶',
                                    'referral' => '👥',
                                    'other'    => '•',
                                ];
                                $sourceIcon = $sourceIcons[$source] ?? '•';

                                /* Refund/late-cancel flag from admin notes */
                                $adminNotesRaw = $b['AdminNotes'] ?? '';
                                $needsRefund   = stripos($adminNotesRaw, 'REFUND NEEDED') !== false;
                                $lateCancel    = stripos($adminNotesRaw, 'LATE CANCEL') !== false;
                            ?>
                            <tr>
                                <td class="cell-mono"><?= htmlspecialchars($b['Reference'] ?? '—'); ?></td>
                                <td class="cell-date"><?= htmlspecialchars(substr($b['SubmittedAt'] ?? '', 0, 16)); ?></td>
                                <td><strong><?= htmlspecialchars($b['Name'] ?? '—'); ?></strong></td>
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
                                <td><?= htmlspecialchars($tourName); ?></td>
                                <td class="cell-date"><?= htmlspecialchars($b['BookingDate'] ?? '—'); ?></td>
                                <td class="cell-center"><?= htmlspecialchars($b['NumberOfPassengers'] ?? '—'); ?></td>
                                <td class="cell-center">
                                    <small style="font-size:12px;color:#666;white-space:nowrap;">
                                        <?= htmlspecialchars($sourceIcon . ' ' . ucfirst($source)); ?>
                                    </small>
                                </td>
                                <td class="cell-center">
                                    <span class="status-badge <?= htmlspecialchars($statusClass); ?>">
                                        <?= htmlspecialchars(ucfirst($status)); ?>
                                    </span>
                                    <?php if ($needsRefund): ?>
                                        <br>
                                        <small style="display:inline-block;margin-top:4px;padding:3px 8px;background:#eaf4ff;color:#1e60a8;font-size:10px;font-weight:800;letter-spacing:0.5px;border-radius:8px;">
                                            💳 REFUND
                                        </small>
                                    <?php elseif ($lateCancel): ?>
                                        <br>
                                        <small style="display:inline-block;margin-top:4px;padding:3px 8px;background:#fff6e4;color:#8a5c10;font-size:10px;font-weight:800;letter-spacing:0.5px;border-radius:8px;">
                                            ⚠️ LATE
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="cell-center">
                                    <small style="font-size:12px;color:#666;white-space:nowrap;">
                                        <?= htmlspecialchars($payLabel); ?>
                                    </small>
                                </td>
                                <td class="cell-actions">
                                    <?php if ($status !== 'confirmed'): ?>
                                        <a href="/fetecation/staff-7742/action.php?type=booking&action=confirm&id=<?= (int)$b['BookingID']; ?>&return=<?= $returnQs; ?>"
                                           class="admin-btn admin-btn-sm admin-btn-success">Confirm</a>
                                    <?php endif; ?>

                                    <?php if ($status !== 'cancelled'): ?>
                                        <a href="/fetecation/staff-7742/action.php?type=booking&action=cancel&id=<?= (int)$b['BookingID']; ?>&return=<?= $returnQs; ?>"
                                           class="admin-btn admin-btn-sm admin-btn-warn">Cancel</a>
                                    <?php endif; ?>

                                    <?php if ($status !== 'cancelled' && $payOpt !== 'on-day'): ?>
                                        <a href="/fetecation/staff-7742/action.php?type=booking&action=send-payment&id=<?= (int)$b['BookingID']; ?>&return=<?= $returnQs; ?>"
                                           class="admin-btn admin-btn-sm admin-btn-ghost"
                                           title="Email the customer a PayPal payment link">💳 Send Payment</a>
                                    <?php endif; ?>

                                    <a href="/fetecation/staff-7742/action.php?type=booking&action=delete&id=<?= (int)$b['BookingID']; ?>&return=<?= $returnQs; ?>"
                                       class="admin-btn admin-btn-sm admin-btn-danger"
                                       onclick="return confirm('Delete this booking permanently?');">Delete</a>
                                </td>
                            </tr>

                            <?php if (!empty($b['Notes']) || !empty($b['PickupLocation']) || !empty($b['AdminNotes'])): ?>
                                <tr class="detail-row">
                                    <td colspan="11">
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
                                        <?php if (!empty($b['AdminNotes'])): ?>
                                            <span class="detail-item">
                                                <strong>Admin:</strong> <?= nl2br(htmlspecialchars($b['AdminNotes'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?= admin_pagination(
                $bookingsPage,
                $bookingsPages,
                'bpage',
                $bookingsPage,
                $messagesPage,
                $bookingsTotal,
                $search,
                $statusFilter,
                $sourceFilter
            ); ?>

        <?php endif; ?>

    </section>


    <!-- ============ MESSAGES ============ -->
    <section class="admin-section">

        <div class="admin-section-header">
            <h2>Contact Messages</h2>
            <span class="admin-count"><?= number_format($messagesTotal); ?> total</span>
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
                                <td class="cell-date"><?= htmlspecialchars(substr($m['CreatedAt'] ?? '', 0, 16)); ?></td>
                                <td><strong><?= htmlspecialchars($m['Name'] ?? '—'); ?></strong></td>
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
                                <td><?= htmlspecialchars($m['Subject'] ?? '—'); ?></td>
                                <td class="cell-message"><?= nl2br(htmlspecialchars($m['Message'] ?? '')); ?></td>
                                <td class="cell-actions">
                                    <a href="/fetecation/staff-7742/action.php?type=message&action=delete&id=<?= (int)$m['MessageID']; ?>&return=<?= $returnQs; ?>"
                                       class="admin-btn admin-btn-sm admin-btn-danger"
                                       onclick="return confirm('Delete this message permanently?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?= admin_pagination(
                $messagesPage,
                $messagesPages,
                'mpage',
                $bookingsPage,
                $messagesPage,
                $messagesTotal,
                $search,
                $statusFilter,
                $sourceFilter
            ); ?>

        <?php endif; ?>

    </section>

</main>

</body>
</html>