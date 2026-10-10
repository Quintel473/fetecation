<?php

if (!isset($pageTitle)) {
    $pageTitle = "FeteCation Taxi & Tours";
}

/* Meta defaults — can be overridden per page */
if (!isset($pageDescription)) {
    $pageDescription = "FeteCation Taxi & Tours — reliable taxi service and unforgettable island tours in Grenada. Book airport transfers, private rides and curated Caribbean experiences.";
}

if (!isset($pageImage)) {
    $pageImage = "/fetecation/images/hero.jpg";
}

if (!isset($pageKeywords)) {
    $pageKeywords = "Grenada taxi, Grenada tours, Caribbean taxi service, island tours, airport transfer Grenada, FeteCation";
}

/* Build full URLs for Open Graph */
$baseUrl   = "http://localhost"; // ← change to your real domain when deployed
$canonical = $baseUrl . $_SERVER['REQUEST_URI'];
$ogImage   = (strpos($pageImage, 'http') === 0) ? $pageImage : $baseUrl . $pageImage;

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

    <title><?= htmlspecialchars($pageTitle); ?> | FeteCation Taxi &amp; Tours</title>

    <!-- Primary SEO -->
    <meta name="description" content="<?= htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords); ?>">
    <meta name="author" content="FeteCation Taxi &amp; Tours">

    <!-- Canonical -->
    <link rel="canonical" href="<?= htmlspecialchars($canonical); ?>">

    <!-- Open Graph (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="FeteCation Taxi &amp; Tours">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical); ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="en_US">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle); ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription); ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage); ?>">

    <!-- Theme color (mobile browser chrome) -->
    <meta name="theme-color" content="#e9783f">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/fetecation/images/favicon.png">
    <link rel="apple-touch-icon" href="/fetecation/images/apple-touch-icon.png">

    <!-- Stylesheet (with cache-busting) -->
    <link rel="stylesheet" href="/fetecation/css/style.css?v=<?= filemtime(__DIR__ . '/../css/style.css') ?: time(); ?>">

    <!-- Structured data (LocalBusiness) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TaxiService",
        "name": "FeteCation Taxi & Tours",
        "url": "<?= htmlspecialchars($baseUrl); ?>",
        "logo": "<?= htmlspecialchars($baseUrl); ?>/fetecation/images/logo.png",
        "image": "<?= htmlspecialchars($ogImage); ?>",
        "description": "Reliable taxi service and island tours in Grenada, Caribbean.",
        "telephone": "+1-473-456-0954",
        "address": {
            "@type": "PostalAddress",
            "addressCountry": "GD",
            "addressLocality": "Grenada"
        },
        "areaServed": {
            "@type": "Country",
            "name": "Grenada"
        },
        "priceRange": "$$",
        "sameAs": [
            "https://wa.me/14734560954"
        ]
    }
    </script>
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