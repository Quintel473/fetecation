<?php

$pageTitle = "Tours";

require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/tours-data.php";

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

                <a href="/fetecation/tours/<?= htmlspecialchars($slug); ?>.php"
                   class="tour-card">

                    <div class="tour-image"
                         style="background-image: url('<?= htmlspecialchars($tour['image']); ?>');">
                    </div>

                    <div class="tour-content">

                        <span class="tour-tag">
                            <?= htmlspecialchars($tour['tag']); ?>
                        </span>

                        <h3>
                            <?= htmlspecialchars($tour['name']); ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($tour['tagline']); ?>
                        </p>

                        <div class="tour-details">

                            <span>🕐 <?= htmlspecialchars($tour['duration']); ?></span>
                            <span>🚐 <?= htmlspecialchars($tour['group']); ?></span>

                        </div>

                        <span class="tour-button">
                            View Tour
                        </span>

                    </div>

                </a>

            <?php endforeach; ?>

        </div>

    </div>

</section>


<?php

require_once __DIR__ . "/includes/footer.php";

?>