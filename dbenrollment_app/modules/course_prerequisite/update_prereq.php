<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prereq_id = intval($_POST['prereq_id']);
    $course_id = intval($_POST['course_id']);
    $prerequisite_course_id = intval($_POST['prerequisite_course_id']);
    
    // Validation
    if(empty($prereq_id) || empty($course_id) || empty($prerequisite_course_id)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Check if course and prerequisite are the same
    if($course_id == $prerequisite_course_id) {
        echo json_encode(['success' => false, 'message' => 'A course cannot be a prerequisite of itself']);
        exit;
    }
    
    // Check for duplicate prerequisite (excluding current record)
    $checkStmt = $conn->prepare("SELECT prereq_id FROM tblcourse_prerequisite WHERE course_id = ? AND prerequisite_course_id = ? AND prereq_id != ? AND is_deleted = 0");
    $checkStmt->bind_param("iii", $course_id, $prerequisite_course_id, $prereq_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'This prerequisite already exists']);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();
    
    // Update prerequisite
    $stmt = $conn->prepare("UPDATE tblcourse_prerequisite SET course_id = ?, prerequisite_course_id = ? WHERE prereq_id = ?");
    $stmt->bind_param("iii", $course_id, $prerequisite_course_id, $prereq_id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Prerequisite updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update prerequisite: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>