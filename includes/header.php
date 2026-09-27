<?php

if (!isset($pageTitle)) {
    $pageTitle = "FeteCation Taxi & Tours";
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

    <div class="container header-container">

        <a href="/fetecation/" class="site-logo">
            <img
                src="/fetecation/images/logo.png"
                alt="FeteCation Taxi & Tours"
            >
        </a>

        <nav class="main-navigation">

            <a href="/fetecation/">Home</a>

            <a href="/fetecation/taxis.php">Taxi Services</a>

            <a href="/fetecation/tours.php">Tours</a>

            <a href="/fetecation/about.php">About</a>

            <a href="/fetecation/contact.php">Contact</a>

            <a href="/fetecation/booking.php" class="nav-book-button">
                Book Now
            </a>

        </nav>

    </div>

</header>

<main>