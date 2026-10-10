<?php
require_once __DIR__ . '/auth.php';

/* ---------------------------------------------------------
   Parse + validate
   --------------------------------------------------------- */
$type   = $_GET['type']   ?? '';
$action = $_GET['action'] ?? '';
$file   = $_GET['file']   ?? '';

if (!in_array($type, ['booking', 'message'], true)) {
    header('Location: /fetecation/staff-7742/index.php');
    exit;
}

if (!in_array($action, ['confirm', 'cancel', 'delete'], true)) {
    header('Location: /fetecation/staff-7742/index.php');
    exit;
}

/* Only allow safe filenames */
if ($file === '' || strpos($file, '/') !== false || strpos($file, '\\') !== false || strpos($file, '..') !== false) {
    header('Location: /fetecation/staff-7742/index.php');
    exit;
}

if (!preg_match('/^[A-Za-z0-9_\-\.]+\.json$/', $file)) {
    header('Location: /fetecation/staff-7742/index.php');
    exit;
}

/* ---------------------------------------------------------
   Locate the file
   --------------------------------------------------------- */
$dir = $type === 'booking' ? ADMIN_BOOKINGS_DIR : ADMIN_MESSAGES_DIR;
$path = $dir . '/' . $file;

if (!is_file($path)) {
    header('Location: /fetecation/staff-7742/index.php');
    exit;
}

/* ---------------------------------------------------------
   Perform action
   --------------------------------------------------------- */
if ($action === 'delete') {

    @unlink($path);

} else {

    $data = json_decode(file_get_contents($path), true);
    if (!is_array($data)) {
        header('Location: /fetecation/staff-7742/index.php');
        exit;
    }

    $data['status'] = $action === 'confirm' ? 'confirmed' : 'cancelled';
    $data['status_updated_at'] = date('Y-m-d H:i:s');

    @file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
}

header('Location: /fetecation/staff-7742/index.php');
exit;