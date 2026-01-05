<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

// 1. Point to the ACTUAL files in your includes folder
include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');

// 2. Use the AJAX-friendly function already in your role_check.php
if (function_exists('requireRoleAjax')) {
    requireRoleAjax('admin');
}

include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure the key 'room_id' matches what your JS sends
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    
    if ($room_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid Room ID']);
        exit;
    }
    
    // Soft delete
    $stmt = $conn->prepare("UPDATE tblroom SET is_deleted = 1 WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    $stmt->close();
}
$conn->close();
?>