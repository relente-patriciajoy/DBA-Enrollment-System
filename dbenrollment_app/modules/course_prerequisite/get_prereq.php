<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $prereq_id = intval($_GET['prereq_id']);
    
    if(empty($prereq_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid prerequisite ID']);
        exit;
    }
    
    // Get prerequisite data
    $stmt = $conn->prepare("SELECT prereq_id, course_id, prerequisite_course_id FROM tblcourse_prerequisite WHERE prereq_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $prereq_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $prereq = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $prereq]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Prerequisite not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>