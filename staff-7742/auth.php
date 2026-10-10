<?php
/**
 * Admin authentication guard.
 * Include this at the top of every protected admin page.
 *
 * Usage:
 *   require_once __DIR__ . '/auth.php';
 */

require_once __DIR__ . '/config.php';

/* Start the session if not already started */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* If not logged in, redirect to login page */
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /fetecation/staff-7742/login.php');
    exit;
}

/* Auto-logout after 2 hours of inactivity */
$timeout = 60 * 60 * 2; // 2 hours
if (isset($_SESSION['admin_last_activity']) && (time() - $_SESSION['admin_last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header('Location: /fetecation/staff-7742/login.php?timeout=1');
    exit;
}
$_SESSION['admin_last_activity'] = time();