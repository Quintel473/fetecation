<?php
$pageTitle = "Create Account";

require_once __DIR__ . '/includes/customer-auth.php';

if (customer_is_logged_in()) {
    header('Location: /fetecation/account/index.php');
    exit;
}

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first    = trim($_POST['first_name'] ?? '');
    $last     = trim($_POST['last_name']  ?? '');
    $email    = trim($_POST['email']      ?? '');
    $phone    = trim($_POST['phone']      ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirm  = (string)($_POST['confirm']  ?? '');

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($_POST['agree'])) {
        $errors[] = 'Please agree to the Terms of Service and Privacy Policy.';
    }

    if (empty($errors)) {
        $result = customer_register($first, $last, $email, $phone, $password);

        if ($result['ok']) {
            $success = true;
            $_POST = [];
        } else {
            $errors[] = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | FeteCation</title>
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

                <h1>Join Us</h1>
                <p class="acct-hero-sub">My FeteCation</p>

                <p class="acct-hero-text">
                    Create your free account to book faster and
                    keep every trip in one place.
                </p>

                <ul class="acct-hero-list">
                    <li>Book in seconds</li>
                    <li>View all your trips</li>
                    <li>Save your details</li>
                </ul>

            </div>

            <p class="acct-hero-footer">
                &copy; <?= date('Y'); ?> FeteCation. All rights reserved.
            </p>

        </aside>


        <main class="acct-panel">

            <div class="acct-panel-inner">

                <?php if ($success): ?>

                    <header class="acct-panel-header">
                        <h2>Check your inbox</h2>
                        <p>We've sent a confirmation link to your email.</p>
                    </header>

                    <div class="acct-success">
                        <span class="acct-success-icon">✓</span>
                        <div>
                            <strong>Account created!</strong><br>
                            Click the link in your email to activate it, then sign in.
                        </div>
                    </div>

                    <a href="/fetecation/login.php" class="acct-button" style="margin-top:8px;">
                        <span>Go to Sign In</span>
                        <span class="acct-button-arrow">→</span>
                    </a>

                <?php else: ?>

                    <header class="acct-panel-header">
                        <h2>Create Account</h2>
                        <p>It takes less than a minute.</p>
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

                    <form method="post" action="/fetecation/register.php" autocomplete="off">

                        <div class="acct-row">

                            <div class="acct-field">
                                <label for="first_name">First</label>
                                <div class="acct-input-wrap">
                                    <span class="acct-input-icon">👤</span>
                                    <input
                                        type="text"
                                        id="first_name"
                                        name="first_name"
                                        required
                                        autofocus
                                        placeholder="First name"
                                        value="<?= htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                    >
                                </div>
                            </div>

                            <div class="acct-field">
                                <label for="last_name">Last</label>
                                <div class="acct-input-wrap">
                                    <span class="acct-input-icon">👤</span>
                                    <input
                                        type="text"
                                        id="last_name"
                                        name="last_name"
                                        required
                                        placeholder="Last name"
                                        value="<?= htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                    >
                                </div>
                            </div>

                        </div>

                        <div class="acct-field">
                            <label for="email">Email</label>
                            <div class="acct-input-wrap">
                                <span class="acct-input-icon">✉️</span>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    placeholder="you@example.com"
                                    value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                                >
                            </div>
                        </div>

                        <div class="acct-field">
                            <label for="phone">Phone (optional)</label>
                            <div class="acct-input-wrap">
                                <span class="acct-input-icon">📞</span>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    placeholder="+1 (473) 000-0000"
                                    value="<?= htmlspecialchars($_POST['phone'] ?? ''); ?>"
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
                                    minlength="8"
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
                            <label for="confirm">Confirm Password</label>
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

                        <div class="acct-field acct-terms-field">
                            <label class="acct-terms-label">
                                <input type="checkbox" name="agree" required>
                                <span>
                                    I agree to the
                                    <a href="/fetecation/terms.php" target="_blank">Terms of Service</a>
                                    and
                                    <a href="/fetecation/privacy.php" target="_blank">Privacy Policy</a>.
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="acct-button">
                            <span>Create Account</span>
                            <span class="acct-button-arrow">→</span>
                        </button>

                    </form>

                    <p class="acct-panel-footer">
                        Already have an account?
                        <a href="/fetecation/login.php">Sign in →</a>
                    </p>

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