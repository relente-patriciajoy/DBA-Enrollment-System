<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = intval($_POST['course_id']);
    $prerequisite_course_id = intval($_POST['prerequisite_course_id']);
    
    // Validation
    if(empty($course_id) || empty($prerequisite_course_id)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Check if course and prerequisite are the same
    if($course_id == $prerequisite_course_id) {
        echo json_encode(['success' => false, 'message' => 'A course cannot be a prerequisite of itself']);
        exit;
    }
    
    // Check for duplicate prerequisite
    $checkStmt = $conn->prepare("SELECT prereq_id FROM tblcourse_prerequisite WHERE course_id = ? AND prerequisite_course_id = ? AND is_deleted = 0");
    $checkStmt->bind_param("ii", $course_id, $prerequisite_course_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'This prerequisite already exists']);
        $checkStmt->close();
        exit;
    }
    $checkStmt->close();
    
    // Insert new prerequisite
    $stmt = $conn->prepare("INSERT INTO tblcourse_prerequisite (course_id, prerequisite_course_id, is_deleted) VALUES (?, ?, 0)");
    $stmt->bind_param("ii", $course_id, $prerequisite_course_id);
    
    if($stmt->execute()) {
        $new_prereq_id = $conn->insert_id;
        echo json_encode(['success' => true, 'message' => 'Prerequisite added successfully', 'prereq_id' => $new_prereq_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add prerequisite: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>