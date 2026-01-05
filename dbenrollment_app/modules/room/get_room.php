<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $room_id = intval($_GET['room_id']);
    
    if(empty($room_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid room ID']);
        exit;
    }
    
    // Get room data
    $stmt = $conn->prepare("SELECT room_id, room_code, building, capacity FROM tblroom WHERE room_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $room = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $room]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Room not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>