<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../includes/security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* If already logged in, go straight to dashboard */
if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: /fetecation/staff-7742/index.php');
    exit;
}

$error      = '';
$stage      = $_SESSION['admin_2fa_pending'] ?? false ? '2fa' : 'password';
$lockoutMsg = '';

/* Lockout notice from GET (after redirect) */
if (isset($_GET['timeout'])) {
    $error = 'You were logged out due to inactivity. Please sign in again.';
}

if (isset($_GET['locked'])) {
    $lockoutMsg = 'Too many failed attempts. Please try again in ' . (int)$_GET['locked'] . ' minute(s).';
}


/* =========================================================
   Handle POST
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? 'password';

    /* ---------- Stage 1: username + password ---------- */
    if ($action === 'password') {

        if (security_is_locked_out()) {
            $mins = security_lockout_remaining_minutes();
            security_log('LOGIN_LOCKED', 'attempt while locked');
            header('Location: /fetecation/staff-7742/login.php?locked=' . $mins);
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Please enter both username and password.';
            security_record_failed_attempt();
            security_log('LOGIN_FAIL', 'empty username or password');
        } elseif ($username !== ADMIN_USERNAME) {
            $error = 'Invalid credentials.';
            security_record_failed_attempt();
            security_log('LOGIN_FAIL', 'unknown username: ' . $username);
            usleep(400000);
        } elseif (!password_verify($password, ADMIN_PASSWORD_HASH)) {
            $error = 'Invalid credentials.';
            security_record_failed_attempt();
            security_log('LOGIN_FAIL', 'bad password for: ' . $username);
            usleep(400000);
        } else {

            /* Password OK — proceed to 2FA */
            $code = security_generate_2fa_code();

            $_SESSION['admin_2fa_pending'] = true;
            $_SESSION['admin_2fa_code']    = $code;
            $_SESSION['admin_2fa_expires'] = time() + TWO_FA_CODE_TTL;
            $_SESSION['admin_2fa_user']    = $username;

            if (security_send_2fa_code($code)) {
                security_log('LOGIN_2FA_SENT', 'code emailed to admin');
                header('Location: /fetecation/staff-7742/login.php');
                exit;
            } else {
                $error = 'Could not send verification code. Please try again.';
                unset($_SESSION['admin_2fa_pending'], $_SESSION['admin_2fa_code']);
                security_log('LOGIN_2FA_FAIL', 'email send failed');
            }
        }
    }

    /* ---------- Stage 2: 2FA code ---------- */
    elseif ($action === '2fa') {

        if (empty($_SESSION['admin_2fa_pending'])) {
            header('Location: /fetecation/staff-7742/login.php');
            exit;
        }

        $entered = trim($_POST['code'] ?? '');
        $stored  = $_SESSION['admin_2fa_code']    ?? '';
        $expires = $_SESSION['admin_2fa_expires'] ?? 0;

        if (time() > $expires) {
            $error = 'Verification code expired. Please sign in again.';
            unset($_SESSION['admin_2fa_pending'], $_SESSION['admin_2fa_code']);
            security_log('LOGIN_2FA_EXPIRED', 'code expired');
        } elseif ($entered === '' || !hash_equals($stored, $entered)) {
            $error = 'Incorrect verification code.';
            security_record_failed_attempt();
            security_log('LOGIN_2FA_FAIL', 'wrong code entered');
        } else {

            /* Success — finalise login */
            security_clear_attempts();

            session_regenerate_id(true);
            $_SESSION['admin_logged_in']     = true;
            $_SESSION['admin_username']      = $_SESSION['admin_2fa_user'] ?? ADMIN_USERNAME;
            $_SESSION['admin_last_activity'] = time();

            unset(
                $_SESSION['admin_2fa_pending'],
                $_SESSION['admin_2fa_code'],
                $_SESSION['admin_2fa_expires'],
                $_SESSION['admin_2fa_user']
            );

            security_log('LOGIN_SUCCESS', 'admin signed in');

            header('Location: /fetecation/staff-7742/index.php');
            exit;
        }
    }
}

/* Refresh $stage after any POST */
$stage = !empty($_SESSION['admin_2fa_pending']) ? '2fa' : 'password';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | FeteCation</title>
    <link rel="stylesheet" href="/fetecation/staff-7742/style.css">
</head>
<body class="login-body">

    <div class="login-bg-shape login-bg-shape-1"></div>
    <div class="login-bg-shape login-bg-shape-2"></div>
    <div class="login-bg-shape login-bg-shape-3"></div>

    <div class="login-shell">

        <aside class="login-hero">

            <div class="login-hero-top">

                <span class="login-hero-badge">ADMIN</span>

                <div class="login-hero-logo">
                    <img src="/fetecation/images/logo.png" alt="FeteCation">
                </div>

            </div>

            <div class="login-hero-body">

                <h1>FeteCation</h1>
                <p class="login-hero-sub">Taxi &amp; Tours</p>

                <p class="login-hero-text">
                    Manage bookings, respond to guests, and keep
                    every ride on schedule.
                </p>

                <ul class="login-hero-list">
                    <li>View incoming bookings</li>
                    <li>Confirm or cancel rides</li>
                    <li>Read contact messages</li>
                </ul>

            </div>

            <p class="login-hero-footer">
                &copy; <?= date('Y'); ?> FeteCation. All rights reserved.
            </p>

        </aside>


        <main class="login-panel">

            <div class="login-panel-inner">

                <?php if ($stage === 'password'): ?>

                    <header class="login-panel-header">
                        <h2>Welcome back</h2>
                        <p>Sign in to continue to your dashboard.</p>
                    </header>

                    <?php if ($lockoutMsg !== ''): ?>
                        <div class="login-error">
                            <span class="login-error-icon">!</span>
                            <?= htmlspecialchars($lockoutMsg); ?>
                        </div>
                    <?php elseif ($error !== ''): ?>
                        <div class="login-error">
                            <span class="login-error-icon">!</span>
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/fetecation/staff-7742/login.php" autocomplete="off">

                        <input type="hidden" name="action" value="password">

                        <div class="login-field">
                            <label for="username">Username</label>
                            <div class="login-input-wrap">
                                <span class="login-input-icon">👤</span>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    required
                                    autofocus
                                    placeholder="Enter your username"
                                    value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>"
                                >
                            </div>
                        </div>

                        <div class="login-field">
                            <label for="password">Password</label>
                            <div class="login-input-wrap">
                                <span class="login-input-icon">🔒</span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    placeholder="Enter your password"
                                >
                            </div>
                        </div>

                        <button type="submit" class="login-button">
                            <span>Sign In</span>
                            <span class="login-button-arrow">→</span>
                        </button>

                    </form>

                <?php else: ?>

                    <header class="login-panel-header">
                        <h2>Check your email</h2>
                        <p>We sent a 6-digit code to your inbox.</p>
                    </header>

                    <?php if ($error !== ''): ?>
                        <div class="login-error">
                            <span class="login-error-icon">!</span>
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/fetecation/staff-7742/login.php" autocomplete="off">

                        <input type="hidden" name="action" value="2fa">

                        <div class="login-field">
                            <label for="code">Verification code</label>
                            <div class="login-input-wrap">
                                <span class="login-input-icon">🔑</span>
                                <input
                                    type="text"
                                    id="code"
                                    name="code"
                                    required
                                    autofocus
                                    inputmode="numeric"
                                    pattern="[0-9]{6}"
                                    maxlength="6"
                                    placeholder="6-digit code"
                                    style="letter-spacing:6px;font-weight:700;text-align:center;padding-left:14px;"
                                >
                            </div>
                        </div>

                        <button type="submit" class="login-button">
                            <span>Verify &amp; Sign In</span>
                            <span class="login-button-arrow">→</span>
                        </button>

                    </form>

                    <p style="text-align:center;margin-top:20px;font-size:13px;">
                        <a href="/fetecation/staff-7742/logout.php" style="color:#666;">← Start over</a>
                    </p>

                <?php endif; ?>

                <p class="login-panel-footer">
                    <a href="/fetecation/">← Back to FeteCation.com</a>
                </p>

            </div>

        </main>

    </div>

</body>
</html>