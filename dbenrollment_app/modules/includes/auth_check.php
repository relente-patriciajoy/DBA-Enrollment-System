<?php
/**
 * Authentication Check
 * Include this file at the top of protected pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // FIX: Path adjusted to correctly find the root login page
    header("Location: ../../index.php");
    exit();
}
?>