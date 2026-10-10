<?php
$pageTitle = "Forgot Password";

require_once __DIR__ . '/includes/customer-auth.php';

if (customer_is_logged_in()) {
    header('Location: /fetecation/account/index.php');
    exit;
}

$sent  = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {

        $token = customer_create_reset_token($email);

        if ($token) {
            customer_send_reset_email($token);
        }

        /* Always show success — never reveal whether an email exists */
        $sent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | FeteCation</title>
    <link rel="stylesheet" href="/fetecation/css/account.css">
</head>
<body class="acct-body">

    <div class="acct-bg-shape acct-bg-shape-1"></div>
    <div class="acct-bg-shape acct-bg-shape-2"></div>
    <div class="acct-bg-shape acct-bg-shape-3"></div>

    <div class="acct-shell">

        <aside class="acct-hero">

            <div class="acct-hero-top">

                <span class="acct-hero-badge">RESET</span>

                <div class="acct-hero-logo">
                    <img src="/fetecation/images/logo.png" alt="FeteCation">
                </div>

            </div>

            <div class="acct-hero-body">

                <h1>Forgot?</h1>
                <p class="acct-hero-sub">No problem</p>

                <p class="acct-hero-text">
                    Enter your email and we'll send you a link to
                    set a new password.
                </p>

                <ul class="acct-hero-list">
                    <li>Check your inbox</li>
                    <li>Link expires in 1 hour</li>
                    <li>Then sign in as usual</li>
                </ul>

            </div>

            <p class="acct-hero-footer">
                &copy; <?= date('Y'); ?> FeteCation. All rights reserved.
            </p>

        </aside>


        <main class="acct-panel">

            <div class="acct-panel-inner">

                <?php if ($sent): ?>

                    <header class="acct-panel-header">
                        <h2>Check your email</h2>
                        <p>If an account exists, we've sent a reset link.</p>
                    </header>

                    <div class="acct-success">
                        <span class="acct-success-icon">✓</span>
                        <div>
                            <strong>Email sent</strong><br>
                            Check your inbox (and spam folder) for the reset link.
                        </div>
                    </div>

                    <a href="/fetecation/login.php" class="acct-button" style="margin-top:8px;">
                        <span>Back to Sign In</span>
                        <span class="acct-button-arrow">→</span>
                    </a>

                <?php else: ?>

                    <header class="acct-panel-header">
                        <h2>Reset Password</h2>
                        <p>We'll email you a secure link.</p>
                    </header>

                    <?php if ($error !== ''): ?>
                        <div class="acct-error">
                            <span class="acct-error-icon">!</span>
                            <div><?= htmlspecialchars($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/fetecation/forgot-password.php" autocomplete="off">

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

                        <button type="submit" class="acct-button">
                            <span>Send Reset Link</span>
                            <span class="acct-button-arrow">→</span>
                        </button>

                    </form>

                    <p class="acct-panel-footer">
                        Remembered it?
                        <a href="/fetecation/login.php">Sign in →</a>
                    </p>

                <?php endif; ?>

                <p class="acct-panel-footer">
                    <a href="/fetecation/">← Back to FeteCation.com</a>
                </p>

            </div>

        </main>

    </div>

</body>
</html>