<?php
$pageTitle = "Reset Password";

require_once __DIR__ . '/includes/customer-auth.php';

if (customer_is_logged_in()) {
    header('Location: /fetecation/account/index.php');
    exit;
}

$token   = trim($_GET['token'] ?? $_POST['token'] ?? '');
$errors  = [];
$success = false;

/* Validate token presence */
$tokenValid = false;
if ($token !== '') {
    try {
        $stmt = db()->prepare(
            'SELECT CustomerID FROM customers
             WHERE ResetToken = ?
               AND (ResetExpires IS NULL OR ResetExpires > NOW())
             LIMIT 1'
        );
        $stmt->execute([$token]);
        $tokenValid = (bool)$stmt->fetch();
    } catch (Throwable $e) {
        $tokenValid = false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = (string)($_POST['password'] ?? '');
    $confirm  = (string)($_POST['confirm']  ?? '');

    if (!$tokenValid) {
        $errors[] = 'This reset link is invalid or has expired.';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        if (customer_reset_password($token, $password)) {
            $success = true;
        } else {
            $errors[] = 'Could not update password. The link may have expired.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | FeteCation</title>
    <link rel="stylesheet" href="/fetecation/css/account.css">
    <link rel="icon" type="image/png" href="/fetecation/images/favicon.png">
    <link rel="apple-touch-icon" href="/fetecation/images/apple-touch-icon.png">
</head>
<body class="acct-body">

    <div class="acct-bg-shape acct-bg-shape-1"></div>
    <div class="acct-bg-shape acct-bg-shape-2"></div>
    <div class="acct-bg-shape acct-bg-shape-3"></div>

    <div class="acct-shell">

        <aside class="acct-hero">

            <div class="acct-hero-top">

                <span class="acct-hero-badge">NEW PASSWORD</span>

                <div class="acct-hero-logo">
                    <img src="/fetecation/images/logo.png" alt="FeteCation">
                </div>

            </div>

            <div class="acct-hero-body">

                <h1>Almost there</h1>
                <p class="acct-hero-sub">Set a new password</p>

                <p class="acct-hero-text">
                    Choose a strong password you'll remember.
                    You'll be signed in right after.
                </p>

            </div>

            <p class="acct-hero-footer">
                &copy; <?= date('Y'); ?> FeteCation. All rights reserved.
            </p>

        </aside>


        <main class="acct-panel">

            <div class="acct-panel-inner">

                <?php if ($success): ?>

                    <header class="acct-panel-header">
                        <h2>Password updated</h2>
                        <p>Your new password is active.</p>
                    </header>

                    <div class="acct-success">
                        <span class="acct-success-icon">✓</span>
                        <div>
                            <strong>All set!</strong><br>
                            You can now sign in with your new password.
                        </div>
                    </div>

                    <a href="/fetecation/login.php" class="acct-button" style="margin-top:8px;">
                        <span>Sign In</span>
                        <span class="acct-button-arrow">→</span>
                    </a>

                <?php elseif (!$tokenValid): ?>

                    <header class="acct-panel-header">
                        <h2>Link not valid</h2>
                        <p>This reset link is broken or has expired.</p>
                    </header>

                    <div class="acct-error">
                        <span class="acct-error-icon">!</span>
                        <div>
                            Reset links are only valid for 1 hour. Please request a new one.
                        </div>
                    </div>

                    <a href="/fetecation/forgot-password.php" class="acct-button" style="margin-top:8px;">
                        <span>Request a New Link</span>
                        <span class="acct-button-arrow">→</span>
                    </a>

                <?php else: ?>

                    <header class="acct-panel-header">
                        <h2>Set New Password</h2>
                        <p>Choose a password with at least 8 characters.</p>
                    </header>

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

                    <form method="post" action="/fetecation/reset-password.php" autocomplete="off">

                        <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">

                        <div class="acct-field">
                            <label for="password">New Password</label>
                            <div class="acct-input-wrap has-toggle">
                                <span class="acct-input-icon">🔒</span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    minlength="8"
                                    autofocus
                                    placeholder="At least 8 characters"
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

                        <div class="acct-field">
                            <label for="confirm">Confirm New Password</label>
                            <div class="acct-input-wrap has-toggle">
                                <span class="acct-input-icon">🔒</span>
                                <input
                                    type="password"
                                    id="confirm"
                                    name="confirm"
                                    required
                                    minlength="8"
                                    placeholder="Repeat password"
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

                        <button type="submit" class="acct-button">
                            <span>Update Password</span>
                            <span class="acct-button-arrow">→</span>
                        </button>

                    </form>

                <?php endif; ?>

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