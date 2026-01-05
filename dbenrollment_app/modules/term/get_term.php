<?php
session_start();

// Set header
header('Content-Type: application/json');

// Then do auth checks
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

// Then database connection
include_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $term_id = intval($_GET['term_id']);
    
    if(empty($term_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid term ID']);
        exit;
    }
    
    // Get term data
    $stmt = $conn->prepare("SELECT term_id, term_code, start_date, end_date FROM tblterm WHERE term_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $term_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $term = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $term]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Term not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>