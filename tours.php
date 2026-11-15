<?php
$pageTitle = "Tours";

require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/data/tours-data.php";

$tours = fete_all_tours();
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">EXPLORE WITH FETECATION</p>
        <h1>Discover the Island</h1>
        <p>
            Experience beautiful destinations, culture, scenery
            and unforgettable moments.
        </p>
    </div>
</section>

<section class="content-section">
    <div class="container">

        <div class="section-heading">
            <p class="section-label">OUR TOURS</p>
            <h2>Explore With Us</h2>
            <p>
                Discover experiences designed to help you see,
                enjoy and experience more.
            </p>
        </div>

        <div class="tour-grid">
            <?php foreach ($tours as $slug => $tour): ?>
                <a href="/fetecation/tours/<?= htmlspecialchars($slug); ?>.php" class="tour-card">
                    <div class="tour-image" style="background-image: url('<?= htmlspecialchars($tour['image']); ?>');"></div>

                    <div class="tour-content">
                        <span class="tour-tag"><?= htmlspecialchars($tour['tag']); ?></span>
                        <h3><?= htmlspecialchars($tour['name']); ?></h3>
                        <p><?= htmlspecialchars($tour['tagline']); ?></p>

                        <div class="tour-details">
                            <!-- Clock Icon -->
                            <span style="display:flex;align-items:center;gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?= htmlspecialchars($tour['duration']); ?>
                            </span>
                            <!-- Users Icon -->
                            <span style="display:flex;align-items:center;gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <?= htmlspecialchars($tour['group']); ?>
                            </span>
                        </div>

                        <span class="tour-button">View Tour</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>