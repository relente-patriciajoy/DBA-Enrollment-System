<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prereq_id = intval($_POST['prereq_id']);
    
    if(empty($prereq_id)) {
        echo json_encode(['success' => false, 'message' => 'Invalid prerequisite ID']);
        exit;
    }
    
    // Soft delete - set is_deleted to 1
    $stmt = $conn->prepare("UPDATE tblcourse_prerequisite SET is_deleted = 1 WHERE prereq_id = ?");
    $stmt->bind_param("i", $prereq_id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Prerequisite deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete prerequisite: ' . $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>