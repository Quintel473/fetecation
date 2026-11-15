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
                            <!-- Clock Icon -->
                            <span style="display:flex;align-items:center;gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?= htmlspecialchars($t['duration']); ?>
                            </span>
                            <!-- Users Icon -->
                            <span style="display:flex;align-items:center;gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <?= htmlspecialchars($t['group']); ?>
                            </span>
                            <!-- Dollar Icon -->
                            <span style="display:flex;align-items:center;gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                <?= htmlspecialchars($t['price']); ?>
                            </span>
                        </div>

                        <div class="party-card-footer">
                            <span class="tour-button" style="width:100%; text-align:center; display:block; box-sizing:border-box;">
                                Book This Party
                            </span>
                        </div>

                    </div>

                </a>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . "/includes/footer.php"; ?>