<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type         = $_POST['type'] ?? 'tour';
    $slug         = strtolower(trim($_POST['slug'] ?? ''));
    $name         = trim($_POST['name'] ?? '');
    $tagline      = trim($_POST['tagline'] ?? '');
    $duration     = trim($_POST['duration'] ?? '');
    $groupSize    = trim($_POST['group_size'] ?? '');
    $priceDisplay = trim($_POST['price_display'] ?? '');
    $priceAmount  = (float)($_POST['price_amount'] ?? 0);
    $pickup       = trim($_POST['pickup'] ?? '');
    $priceType    = $_POST['price_type'] ?? 'per_person';
    
    // Create a URL-friendly slug if they didn't provide one
    if ($slug === '') {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    }

    $imagePath = '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images/tours/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
        
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $filename = uniqid('img_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = '/fetecation/images/tours/' . $filename;
            }
        }
    }

    if (empty($name) || empty($slug)) {
        $error = 'Name and Slug are required.';
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO tours (type, slug, name, tagline, duration, group_size, 
                                    price_display, price_amount, price_type, pickup, image) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $type, $slug, $name, $tagline, $duration, $groupSize, 
                $priceDisplay, $priceAmount, $priceType, $pickup, $imagePath
            ]);
            
            header('Location: /fetecation/staff-7742/tours.php');
            exit;
        } catch (Throwable $e) {
            $error = 'Error saving to database. Slug might already exist.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tour | FeteCation Admin</title>
    <link rel="stylesheet" href="/fetecation/staff-7742/style.css">
</head>
<body class="admin-body">
<header class="admin-header">
    <div class="admin-header-left">
        <img src="/fetecation/images/logo.png" alt="FeteCation" class="admin-logo">
        <div><h1>Add New Item</h1></div>
    </div>
    <div class="admin-header-right">
        <a href="/fetecation/staff-7742/tours.php" class="admin-btn admin-btn-ghost">← Back</a>
    </div>
</header>
<main class="admin-main">
    <div class="admin-new-booking-wrap">
        <?php if ($error): ?>
            <div class="admin-form-errors"><strong><?= htmlspecialchars($error); ?></strong></div>
        <?php endif; ?>
        <form method="post" action="" class="admin-new-booking-form" enctype="multipart/form-data">
            <fieldset>
                <span class="form-section-title">Type & Identity</span>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label>Type</label>
                        <select name="type">
                            <option value="tour">Regular Tour</option>
                            <option value="party">Party Package</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label>URL Slug (leave blank to auto-generate)</label>
                        <input type="text" name="slug" placeholder="e.g. sunset-cruise">
                    </div>
                </div>
                <div class="admin-form-group">
                    <label>Name *</label>
                    <input type="text" name="name" required>
                </div>
                <div class="admin-form-group">
                    <label>Tagline</label>
                    <input type="text" name="tagline">
                </div>
            </fieldset>

            <fieldset>
                <span class="form-section-title">Details & Pricing</span>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label>Duration</label>
                        <input type="text" name="duration" placeholder="e.g. 4 hours">
                    </div>
                    <div class="admin-form-group">
                        <label>Group Size</label>
                        <input type="text" name="group_size" placeholder="e.g. Up to 10 people">
                    </div>
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label>Display Price</label>
                        <input type="text" name="price_display" placeholder="e.g. $120 / person">
                    </div>
                    <div class="admin-form-group">
                        <label>Price Amount (Number)</label>
                        <input type="number" step="0.01" name="price_amount" placeholder="120.00">
                    </div>
                </div>
                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label>Pricing Type</label>
                        <select name="price_type">
                            <option value="per_person">Per Person</option>
                            <option value="per_group">Per Group (Party)</option>
                            <option value="custom">Custom Quote</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label>Pickup Info</label>
                        <input type="text" name="pickup" placeholder="e.g. Hotel or cruise port">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <span class="form-section-title">Image</span>
                <div class="admin-form-group">
                    <label>Upload Image</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" style="background:white; padding:10px;">
                </div>
            </fieldset>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn admin-btn-success" style="padding:14px 30px;">Create Item</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>