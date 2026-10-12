<?php
$pageTitle = "Sign In";

require_once __DIR__ . '/includes/customer-auth.php';

if (customer_is_logged_in()) {
    header('Location: /fetecation/account/index.php');
    exit;
}

$errors = [];
$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']    ?? '');
    $password = (string)($_POST['password'] ?? '');

    $result = customer_login($email, $password);

    if ($result['ok']) {
        header('Location: /fetecation/account/index.php');
        exit;
    } else {
        $errors[] = $result['error'];
    }
}

if (isset($_GET['verified']))   $notice = 'Your email has been confirmed. You can sign in now.';
if (isset($_GET['reset']))      $notice = 'Password updated. Sign in with your new password.';
if (isset($_GET['loggedout']))  $notice = 'You have been signed out.';
if (isset($_GET['registered'])) $notice = 'Account created. Check your email to confirm, then sign in.';
if (isset($_GET['deleted']))    $notice = 'Your account has been deleted. We\'re sorry to see you go.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | FeteCation</title>
    <link rel="stylesheet" href="/fetecation/css/account.css">
</head>
<body class="acct-body">

    <div class="acct-bg-shape acct-bg-shape-1"></div>
    <div class="acct-bg-shape acct-bg-shape-2"></div>
    <div class="acct-bg-shape acct-bg-shape-3"></div>

    <div class="acct-shell">

        <aside class="acct-hero">

            <div class="acct-hero-top">

                <span class="acct-hero-badge">CUSTOMER</span>

                <div class="acct-hero-logo">
                    <img src="/fetecation/images/logo.png" alt="FeteCation">
                </div>

            </div>

            <div class="acct-hero-body">

                <h1>FeteCation</h1>
                <p class="acct-hero-sub">Taxi &amp; Tours</p>

                <p class="acct-hero-text">
                    View your bookings, track upcoming trips, and
                    manage your account in one place.
                </p>

                <ul class="acct-hero-list">
                    <li>See your booking history</li>
                    <li>Track upcoming rides</li>
                    <li>Rebook in seconds</li>
                </ul>

            </div>

            <p class="acct-hero-footer">
                &copy; <?= date('Y'); ?> FeteCation. All rights reserved.
            </p>

        </aside>


        <main class="acct-panel">

            <div class="acct-panel-inner">

                <header class="acct-panel-header">
                    <h2>Welcome back</h2>
                    <p>Sign in to view your bookings.</p>
                </header>

                <?php if ($notice !== ''): ?>
                    <div class="acct-success">
                        <span class="acct-success-icon">✓</span>
                        <?= htmlspecialchars($notice); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="acct-error">
                        <span class="acct-error-icon">!</span>
                        <div>
                            <?php foreach ($errors as $e): ?>
                                <?= htmlspecialchars($e); ?><br>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="post" action="/fetecation/login.php" autocomplete="off">

                    <div class="acct-field">
                        <label for="email">Email</label>
                        <div class="acct-input-wrap">
                            <span class="acct-input-icon">✉️</span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                required
                                autofocus
                                placeholder="you@example.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                            >
                        </div>
                    </div>

                    <div class="acct-field">
                        <label for="password">Password</label>
                        <div class="acct-input-wrap has-toggle">
                            <span class="acct-input-icon">🔒</span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="Your password"
                            >
                            <button type="button"
                                    class="password-toggle"
                                    aria-label="Show password"
                                    onclick="togglePassword(this)">
                                <svg class="icon-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="acct-field acct-remember-field">
                        <label class="acct-remember-label">
                            <input type="checkbox" name="remember" value="1">
                            <span>Remember me for 30 days</span>
                        </label>
                    </div>

                    <button type="submit" class="acct-button">
                        <span>Sign In</span>
                        <span class="acct-button-arrow">→</span>
                    </button>

                </form>

                <p class="acct-panel-footer" style="margin-top:22px;">
                    <a href="/fetecation/forgot-password.php">Forgot password?</a>
                    &nbsp;·&nbsp;
                    <a href="/fetecation/register.php">Create an account</a>
                </p>

                <p class="acct-panel-footer">
                    <a href="/fetecation/">← Back to FeteCation.com</a>
                </p>

            </div>

        </main>

    </div>


    <script>
    function togglePassword(btn) {
        var wrap = btn.closest('.acct-input-wrap, .login-input-wrap');
        if (!wrap) return;
        var input = wrap.querySelector('input');
        if (!input) return;

        var isVisible = btn.classList.toggle('is-visible');
        input.type = isVisible ? 'text' : 'password';
        btn.setAttribute('aria-label', isVisible ? 'Hide password' : 'Show password');
    }
    </script>

</body>
</html>