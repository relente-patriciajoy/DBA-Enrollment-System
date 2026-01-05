<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');
requireRoleAjax('admin'); // Ensure this doesn't redirect to a login page

include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_code = trim($_POST['room_code']);
    $building = trim($_POST['building']);
    $capacity = intval($_POST['capacity']);
    
    // Check for empty fields
    if (empty($room_code) || empty($building) || $capacity < 1) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }
    
    // Check for duplicate room code
    $check = $conn->prepare("SELECT room_id FROM tblroom WHERE room_code = ? AND is_deleted = 0");
    $check->bind_param("s", $room_code);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Room code already exists.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO tblroom (room_code, building, capacity, is_deleted) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("ssi", $room_code, $building, $capacity);
    
    if ($stmt->execute()) {
        ob_clean();
        echo json_encode(['success' => true, 'message' => 'Room added successfully!', 'room_id' => $conn->insert_id]);
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}
$conn->close();