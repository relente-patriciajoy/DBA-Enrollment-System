<?php
include_once '../../config/database.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = intval($_POST['room_id']);
    
    if(empty($room_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid room ID']);
        exit;
    }
    
    // Soft delete - set is_deleted to 1
    $stmt = $conn->prepare("UPDATE tblroom SET is_deleted = 1 WHERE room_id = ?");
    $stmt->bind_param("i", $room_id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete room: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>