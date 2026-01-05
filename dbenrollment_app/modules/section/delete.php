<?php
// 1. Prevent accidental HTML/White-space
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

// 2. Correct paths to your includes
include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');

// 3. AJAX-friendly check (This function must not use header("Location..."))
if (function_exists('requireRoleAjax')) {
    requireRoleAjax('admin');
}

include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // The key here must match section.js: { section_id: id }
    $id = isset($_POST['section_id']) ? intval($_POST['section_id']) : 0;

    if ($id <= 0) {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Invalid Section ID']);
        exit;
    }

    // Soft delete: set is_deleted = 1
    $stmt = $conn->prepare("UPDATE tblsection SET is_deleted = 1 WHERE section_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        ob_clean(); // Clear any accidental output/warnings
        echo json_encode(['success' => true]);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $stmt->close();
}
$conn->close();
?>