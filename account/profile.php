<?php
$pageTitle = "Profile Settings";

require_once __DIR__ . '/../includes/customer-auth.php';

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

$notice       = '';
$profileError = '';
$emailError   = '';
$pwError      = '';
$deleteError  = '';

/* ---------------------------------------------------------
   Handle actions
   --------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    /* ---- Update name + phone ---- */
    if ($action === 'profile') {

        $result = customer_update_profile(
            (int)$customer['CustomerID'],
            $_POST['first_name'] ?? '',
            $_POST['last_name']  ?? '',
            $_POST['phone']      ?? ''
        );

        if ($result['ok']) {
            $notice = 'Profile updated.';
            $customer = customer_current(); /* refresh */
        } else {
            $profileError = $result['error'];
        }
    }

    /* ---- Update email ---- */
    elseif ($action === 'email') {

        $result = customer_update_email(
            (int)$customer['CustomerID'],
            $_POST['email'] ?? ''
        );

        if ($result['ok'] && !empty($result['changed'])) {
            $notice = 'Email updated. Check your new inbox to confirm it.';
            $customer = customer_current();
        } elseif ($result['ok']) {
            $notice = 'That is already your current email.';
        } else {
            $emailError = $result['error'];
        }
    }

    /* ---- Change password ---- */
    elseif ($action === 'password') {

        $new     = (string)($_POST['new_password']     ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        $current = (string)($_POST['current_password'] ?? '');

        if ($new !== $confirm) {
            $pwError = 'New passwords do not match.';
        } else {
            $result = customer_change_password(
                (int)$customer['CustomerID'],
                $current,
                $new
            );

            if ($result['ok']) {
                $notice = 'Password changed successfully.';
            } else {
                $pwError = $result['error'];
            }
        }
    }

    /* ---- Delete account ---- */
    elseif ($action === 'delete') {

        $password = (string)($_POST['confirm_password_delete'] ?? '');

        $result = customer_delete_account((int)$customer['CustomerID'], $password);

        if ($result['ok']) {
            customer_logout();
            header('Location: /fetecation/login.php?deleted=1');
            exit;
        } else {
            $deleteError = $result['error'];
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">

    <div class="container page-hero-content">

        <p class="section-label">MY FETECATION</p>
        <h1>Profile Settings</h1>
        <p>Manage your account information and security.</p>

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

                    <a href="/fetecation/account/index.php">
                        <span>📋</span> My Bookings
                    </a>

                    <a href="/fetecation/account/profile.php" class="active">
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

                <?php if ($notice !== ''): ?>
                    <div class="form-success" style="margin-bottom:28px;">
                        <strong>✅ <?= htmlspecialchars($notice); ?></strong>
                    </div>
                <?php endif; ?>


                <!-- ============ PROFILE DETAILS ============ -->
                <section class="profile-card">

                    <header class="profile-card-header">
                        <h2>Personal Details</h2>
                        <p>Your basic account information.</p>
                    </header>

                    <?php if ($profileError !== ''): ?>
                        <div class="form-errors"><strong><?= htmlspecialchars($profileError); ?></strong></div>
                    <?php endif; ?>

                    <form method="post" action="/fetecation/account/profile.php">

                        <input type="hidden" name="action" value="profile">

                        <div class="form-row">

                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    required
                                    value="<?= htmlspecialchars($customer['FirstName']); ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    required
                                    value="<?= htmlspecialchars($customer['LastName']); ?>"
                                >
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="<?= htmlspecialchars($customer['Phone'] ?? ''); ?>"
                            >
                        </div>

                        <button type="submit" class="primary-button">
                            Save Changes
                        </button>

                    </form>

                </section>


                <!-- ============ EMAIL ============ -->
                <section class="profile-card">

                    <header class="profile-card-header">
                        <h2>Email Address</h2>
                        <p>
                            Current status:
                            <?php if (!empty($customer['EmailVerified'])): ?>
                                <span class="badge badge-success">Verified</span>
                            <?php else: ?>
                                <span class="badge badge-warn">Not verified</span>
                            <?php endif; ?>
                        </p>
                    </header>

                    <?php if ($emailError !== ''): ?>
                        <div class="form-errors"><strong><?= htmlspecialchars($emailError); ?></strong></div>
                    <?php endif; ?>

                    <form method="post" action="/fetecation/account/profile.php">

                        <input type="hidden" name="action" value="email">

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                required
                                value="<?= htmlspecialchars($customer['Email']); ?>"
                            >
                            <small class="form-hint">
                                Changing this will require re-verification.
                            </small>
                        </div>

                        <button type="submit" class="primary-button">
                            Update Email
                        </button>

                    </form>

                </section>


                <!-- ============ PASSWORD ============ -->
                <section class="profile-card">

                    <header class="profile-card-header">
                        <h2>Change Password</h2>
                        <p>Choose a strong password at least 8 characters long.</p>
                    </header>

                    <?php if ($pwError !== ''): ?>
                        <div class="form-errors"><strong><?= htmlspecialchars($pwError); ?></strong></div>
                    <?php endif; ?>

                    <form method="post" action="/fetecation/account/profile.php">

                        <input type="hidden" name="action" value="password">

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                autocomplete="current-password"
                            >
                        </div>

                        <div class="form-row">

                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input
                                    type="password"
                                    id="new_password"
                                    name="new_password"
                                    required
                                    minlength="8"
                                    autocomplete="new-password"
                                >
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input
                                    type="password"
                                    id="confirm_password"
                                    name="confirm_password"
                                    required
                                    minlength="8"
                                    autocomplete="new-password"
                                >
                            </div>

                        </div>

                        <button type="submit" class="primary-button">
                            Update Password
                        </button>

                    </form>

                </section>


                <!-- ============ DANGER ZONE ============ -->
                <section class="profile-card profile-card-danger">

                    <header class="profile-card-header">
                        <h2>Delete Account</h2>
                        <p>
                            This will sign you out and disable your account.
                            Your past bookings will be kept for record-keeping.
                            Contact us if you'd like it restored.
                        </p>
                    </header>

                    <?php if ($deleteError !== ''): ?>
                        <div class="form-errors"><strong><?= htmlspecialchars($deleteError); ?></strong></div>
                    <?php endif; ?>

                    <form method="post"
                          action="/fetecation/account/profile.php"
                          onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone from your side.');">

                        <input type="hidden" name="action" value="delete">

                        <div class="form-group">
                            <label for="confirm_password_delete">
                                Type your password to confirm
                            </label>
                            <input
                                type="password"
                                id="confirm_password_delete"
                                name="confirm_password_delete"
                                required
                                autocomplete="current-password"
                            >
                        </div>

                        <button type="submit" class="danger-button">
                            Delete My Account
                        </button>

                    </form>

                </section>

            </main>

        </div>

    </div>

</section>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>