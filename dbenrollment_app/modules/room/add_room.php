<?php
include_once '../../config/database.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_code = trim($_POST['room_code']);
    $building = trim($_POST['building']);
    $capacity = intval($_POST['capacity']);
    
    // Validation
    if(empty($room_code) || empty($building) || empty($capacity)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if($capacity < 1) {
        echo json_encode(['success' => false, 'message' => 'Capacity must be at least 1']);
        exit;
    }
    
    // Check for duplicate room code
    $checkStmt = $conn->prepare("SELECT room_id FROM tblroom WHERE room_code = ? AND is_deleted = 0");
    $checkStmt->bind_param("s", $room_code);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Room code already exists']);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();
    
    // Insert new room
    $stmt = $conn->prepare("INSERT INTO tblroom (room_code, building, capacity, is_deleted) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("ssi", $room_code, $building, $capacity);
    
    if($stmt->execute()) {
        $new_room_id = $conn->insert_id;
        echo json_encode(['success' => true, 'message' => 'Room added successfully', 'room_id' => $new_room_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add room: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>