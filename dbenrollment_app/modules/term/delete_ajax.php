<?php
include_once '../../config/database.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $term_id = intval($_POST['term_id']);
    
    if(empty($term_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid term ID']);
        exit;
    }
    
    // Soft delete - set is_deleted to 1
    $stmt = $conn->prepare("UPDATE tblterm SET is_deleted = 1 WHERE term_id = ?");
    $stmt->bind_param("i", $term_id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Term deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete term: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>