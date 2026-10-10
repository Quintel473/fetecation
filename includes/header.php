<?php

if (!isset($pageTitle)) {
    $pageTitle = "FeteCation Taxi & Tours";
}

/* Load customer session state if available (for the nav link) */
if (file_exists(__DIR__ . '/customer-auth.php')) {
    require_once __DIR__ . '/customer-auth.php';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle); ?> | FeteCation Taxi & Tours</title>

    <link rel="stylesheet" href="/fetecation/css/style.css">
</head>

<body>

<header class="site-header">

    <div class="header-container">

        <a href="/fetecation/" class="site-logo">
            <span class="site-logo-circle">
                <img
                    src="/fetecation/images/logo.png"
                    alt="FeteCation Taxi & Tours"
                >
            </span>

            <span class="site-logo-text">
                <strong>FeteCation</strong>
                <small>Taxi &amp; Tours</small>
            </span>
        </a>

        <nav class="main-navigation">

            <a href="/fetecation/">Home</a>

            <a href="/fetecation/taxis.php">Taxi Services</a>

            <a href="/fetecation/tours.php">Tours</a>

            <a href="/fetecation/about.php">About</a>

            <a href="/fetecation/contact.php">Contact</a>

            <?php if (function_exists('customer_is_logged_in') && customer_is_logged_in()): ?>

                <a href="/fetecation/account/index.php" class="nav-account-link">
                    👤 My Account
                </a>

            <?php else: ?>

                <a href="/fetecation/login.php" class="nav-account-link">
                    Sign In
                </a>

            <?php endif; ?>

            <a href="/fetecation/booking.php" class="nav-book-button">
                Book Now
            </a>

        </nav>

    </div>

</header>

<main>