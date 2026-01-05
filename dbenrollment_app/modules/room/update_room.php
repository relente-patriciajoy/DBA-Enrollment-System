<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = intval($_POST['room_id']);
    $room_code = trim($_POST['room_code']);
    $building = trim($_POST['building']);
    $capacity = intval($_POST['capacity']);
    
    // Validation
    if(empty($room_id) || empty($room_code) || empty($building) || empty($capacity)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if($capacity < 1) {
        echo json_encode(['success' => false, 'message' => 'Capacity must be at least 1']);
        exit;
    }
    
    // Check for duplicate room code (excluding current room)
    $checkStmt = $conn->prepare("SELECT room_id FROM tblroom WHERE room_code = ? AND room_id != ? AND is_deleted = 0");
    $checkStmt->bind_param("si", $room_code, $room_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Room code already exists']);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();
    
    // Update room
    $stmt = $conn->prepare("UPDATE tblroom SET room_code = ?, building = ?, capacity = ? WHERE room_id = ?");
    $stmt->bind_param("ssii", $room_code, $building, $capacity, $room_id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update room: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>