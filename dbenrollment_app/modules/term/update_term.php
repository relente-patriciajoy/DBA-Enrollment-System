<?php
include_once '../../config/database.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $term_id = intval($_POST['term_id']);
    $term_code = trim($_POST['term_code']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Validation
    if(empty($term_id) || empty($term_code) || empty($start_date) || empty($end_date)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Check if end date is after start date
    if(strtotime($end_date) <= strtotime($start_date)) {
        echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
        exit;
    }
    
    // Check for duplicate term code (excluding current term)
    $checkStmt = $conn->prepare("SELECT term_id FROM tblterm WHERE term_code = ? AND term_id != ? AND is_deleted = 0");
    $checkStmt->bind_param("si", $term_code, $term_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Term code already exists']);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();
    
    // Update term
    $stmt = $conn->prepare("UPDATE tblterm SET term_code = ?, start_date = ?, end_date = ? WHERE term_id = ?");
    $stmt->bind_param("sssi", $term_code, $start_date, $end_date, $term_id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Term updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update term: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>