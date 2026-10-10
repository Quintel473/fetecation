<?php
$pageTitle = "Confirm Email";

require_once __DIR__ . '/includes/customer-auth.php';

$token   = trim($_GET['token'] ?? '');
$success = false;

if ($token !== '') {
    $success = customer_verify_email($token);
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container page-hero-content">

        <p class="section-label">EMAIL CONFIRMATION</p>

        <h1>
            <?= $success ? 'Email Confirmed' : 'Invalid Link'; ?>
        </h1>

        <p>
            <?php if ($success): ?>
                Your account is now active. You can sign in and view your bookings.
            <?php else: ?>
                This confirmation link is invalid or has expired.
            <?php endif; ?>
        </p>

    </div>
</section>


<section class="content-section">
    <div class="container">
        <div class="account-auth-card" style="text-align:center;">

            <div class="success-icon" style="margin-bottom:22px;">
                <?= $success ? '✓' : '✕'; ?>
            </div>

            <?php if ($success): ?>

                <h2>All set!</h2>
                <p class="auth-card-subtitle">
                    Your email has been confirmed. Sign in to continue.
                </p>
                <p style="margin-top:26px;">
                    <a href="/fetecation/login.php" class="primary-button">Sign In</a>
                </p>

            <?php else: ?>

                <h2>Link not valid</h2>
                <p class="auth-card-subtitle">
                    The link may be broken, expired (48-hour limit), or already used.
                </p>
                <p style="margin-top:26px;">
                    <a href="/fetecation/register.php" class="secondary-button">Create a new account</a>
                    &nbsp;&nbsp;
                    <a href="/fetecation/" style="color:var(--fete-gray);">Back to home</a>
                </p>

            <?php endif; ?>

        </div>
    </div>
</section>


<?php require_once __DIR__ . '/includes/footer.php'; ?>