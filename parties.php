<?php
$pageTitle = "Parties & Events";

require_once __DIR__ . "/data/tours-data.php";
require_once __DIR__ . "/includes/header.php";

$parties = fete_all_parties();
?>

<section class="page-hero">
    <div class="container page-hero-content">
        <p class="section-label">NIGHTLIFE & CELEBRATIONS</p>
        <h1>Parties & Events</h1>
        <p>Book the ultimate party experience. We drive, you celebrate.</p>
    </div>
</section>


<section class="content-section">
    <div class="container">

        <div class="section-heading centered-heading" style="margin-left:auto;margin-right:auto;text-align:center;">
            <p class="section-label">UPCOMING EVENTS</p>
            <h2>Book Your Night Out</h2>
            <p>Choose your party, bring your friends, and leave the driving to us.</p>
        </div>

        <div class="tour-grid">
            <?php foreach ($parties as $slug => $t): ?>
                
                <!-- FIXED: Points to the booking folder now -->
                <a href="/fetecation/booking/party-booking.php?tour=<?= htmlspecialchars($slug); ?>" class="tour-card">
                    
                    <!-- The Flyer Image -->
                    <div class="tour-image" style="background-image: url('<?= htmlspecialchars($t['image']); ?>'); background-size:cover; background-position:center; height:380px;">
                        <span class="tour-overlay-label" style="background:var(--fete-orange); color:white; font-size:12px; padding:8px 14px;">
                            <?= htmlspecialchars($t['tag']); ?>
                        </span>
                    </div>
                    
                    <div class="tour-content">
                        <h3><?= htmlspecialchars($t['name']); ?></h3>
                        <p><?= htmlspecialchars($t['tagline']); ?></p>
                        
                        <div class="tour-details" style="margin-bottom:20px;">
                            <span>🕐 <?= htmlspecialchars($t['duration']); ?></span>
                            <span>👥 <?= htmlspecialchars($t['group']); ?></span>
                            <span>💵 <?= htmlspecialchars($t['price']); ?></span>
                        </div>

                        <span class="tour-button" style="width:100%; text-align:center; display:block; box-sizing:border-box;">
                            Book This Party
                        </span>
                    </div>

                </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>