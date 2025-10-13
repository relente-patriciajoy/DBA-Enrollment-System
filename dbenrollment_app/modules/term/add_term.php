<?php
include_once '../../config/database.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $term_code = trim($_POST['term_code']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Validation
    if(empty($term_code) || empty($start_date) || empty($end_date)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Check if end date is after start date
    if(strtotime($end_date) <= strtotime($start_date)) {
        echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
        exit;
    }
    
    // Check for duplicate term code
    $checkStmt = $conn->prepare("SELECT term_id FROM tblterm WHERE term_code = ? AND is_deleted = 0");
    $checkStmt->bind_param("s", $term_code);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Term code already exists']);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();
    
    // Insert new term
    $stmt = $conn->prepare("INSERT INTO tblterm (term_code, start_date, end_date, is_deleted) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $term_code, $start_date, $end_date);
    
    if($stmt->execute()) {
        $new_term_id = $conn->insert_id;
        echo json_encode(['success' => true, 'message' => 'Term added successfully', 'term_id' => $new_term_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add term: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>