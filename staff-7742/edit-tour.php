<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/database.php';

$id = (int)($_GET['id'] ?? 0);
if ($id < 1) {
    header('Location: /fetecation/staff-7742/tours.php');
    exit;
}

$success = '';
$error   = '';

/* ---------------------------------------------------------
   Handle DELETE request
   --------------------------------------------------------- */
if (isset($_GET['delete']) && $_GET['delete'] == '1') {
    try {
        $stmt = $pdo->prepare('DELETE FROM tours WHERE id = ?');
        $stmt->execute([$id]);
        header('Location: /fetecation/staff-7742/tours.php');
        exit;
    } catch (Throwable $e) {
        $error = 'Failed to delete item from database.';
    }
}

/* ---------------------------------------------------------
   Handle POST (Save changes)
   --------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Collect fields
    $name          = trim($_POST['name'] ?? '');
    $tagline       = trim($_POST['tagline'] ?? '');
    $duration      = trim($_POST['duration'] ?? '');
    $groupSize     = trim($_POST['group_size'] ?? '');
    $priceDisplay  = trim($_POST['price_display'] ?? '');
    $priceAmount   = (float)($_POST['price_amount'] ?? 0);
    $pickup        = trim($_POST['pickup'] ?? '');
    
    // Default to existing image
    $imagePath = $_POST['current_image'] ?? '';

    // Handle image upload if a new file was provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../images/tours/'; // We'll default to tours folder for simplicity
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
        
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $filename = uniqid('img_') . '.' . $ext;
            $dest = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                // Save the web path to the DB
                $imagePath = '/fetecation/images/tours/' . $filename;
            } else {
                $error = 'Failed to upload image. Check folder permissions.';
            }
        } else {
            $error = 'Invalid image format. Use JPG, PNG or WebP.';
        }
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare(
                'UPDATE tours 
                 SET name = ?, tagline = ?, duration = ?, group_size = ?, 
                     price_display = ?, price_amount = ?, pickup = ?, image = ?
                 WHERE id = ?'
            );
            $stmt->execute([
                $name, $tagline, $duration, $groupSize, 
                $priceDisplay, $priceAmount, $pickup, $imagePath, $id
            ]);
            $success = 'Changes saved successfully!';
        } catch (Throwable $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

/* ---------------------------------------------------------
   Fetch current data
   --------------------------------------------------------- */
try {
    $stmt = $pdo->prepare('SELECT * FROM tours WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $tour = $stmt->fetch();
} catch (Throwable $e) {
    $tour = null;
}

if (!$tour) {
    header('Location: /fetecation/staff-7742/tours.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tour | FeteCation Admin</title>
    <link rel="stylesheet" href="/fetecation/staff-7742/style.css">
</head>
<body class="admin-body">

<header class="admin-header">
    <div class="admin-header-left">
        <img src="/fetecation/images/logo.png" alt="FeteCation" class="admin-logo">
        <div>
            <h1>Edit Tour / Party</h1>
            <p>Update details and pricing</p>
        </div>
    </div>
    <div class="admin-header-right">
        <a href="/fetecation/staff-7742/tours.php" class="admin-btn admin-btn-ghost">← Back to List</a>
    </div>
</header>

<main class="admin-main">

    <div class="admin-new-booking-wrap">

        <?php if ($success): ?>
            <div class="admin-success" style="padding: 20px; margin-bottom: 20px;">
                <strong>✅ <?= htmlspecialchars($success); ?></strong>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="admin-form-errors">
                <strong>Error:</strong> <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="" class="admin-new-booking-form" enctype="multipart/form-data">
            
            <fieldset>
                <span class="form-section-title">Basic Details</span>

                <div class="admin-form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($tour['name']); ?>" required>
                </div>

                <div class="admin-form-group">
                    <label>Tagline (Short description)</label>
                    <input type="text" name="tagline" value="<?= htmlspecialchars($tour['tagline']); ?>">
                </div>

                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label>Duration</label>
                        <input type="text" name="duration" value="<?= htmlspecialchars($tour['duration']); ?>">
                    </div>
                    <div class="admin-form-group">
                        <label>Group Size</label>
                        <input type="text" name="group_size" value="<?= htmlspecialchars($tour['group_size']); ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <span class="form-section-title">Pricing</span>

                <div class="admin-form-row">
                    <div class="admin-form-group">
                        <label>Display Price (e.g. "From $120 / person")</label>
                        <input type="text" name="price_display" value="<?= htmlspecialchars($tour['price_display']); ?>">
                    </div>
                    <div class="admin-form-group">
                        <label>Numerical Price (for calculations)</label>
                        <input type="number" step="0.01" name="price_amount" value="<?= htmlspecialchars($tour['price_amount']); ?>">
                    </div>
                </div>
                
                <div class="admin-form-group">
                    <label>Pickup Info</label>
                    <input type="text" name="pickup" value="<?= htmlspecialchars($tour['pickup']); ?>">
                </div>
            </fieldset>

            <fieldset>
                <span class="form-section-title">Image / Flyer</span>
                
                <div class="admin-form-group">
                    <label>Current Image</label>
                    <?php if (!empty($tour['image'])): ?>
                        <img src="<?= htmlspecialchars($tour['image']); ?>" style="max-width:200px; border-radius:8px; margin-bottom:10px; display:block;">
                    <?php else: ?>
                        <p style="color:#666; font-size:14px; margin-bottom:10px;">No image uploaded yet.</p>
                    <?php endif; ?>
                    
                    <input type="hidden" name="current_image" value="<?= htmlspecialchars($tour['image']); ?>">
                    
                    <label style="margin-top:10px; display:block;">Upload New Image (Overrides current)</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" style="background:white; padding:10px;">
                </div>
            </fieldset>

            <div class="admin-form-actions" style="justify-content: space-between;">
                
                <div>
                    <button type="submit" class="admin-btn admin-btn-success" style="padding:14px 30px;font-size:15px;">
                        Save Changes
                    </button>
                    <a href="/fetecation/staff-7742/tours.php" class="admin-btn admin-btn-ghost">Cancel</a>
                </div>

                <a href="?id=<?= $id; ?>&delete=1" 
                   class="admin-btn admin-btn-danger" 
                   onclick="return confirm('Are you sure you want to permanently delete this item? This cannot be undone.');">
                    🗑️ Delete
                </a>

            </div>

        </form>

    </div>

</main>

</body>
</html>