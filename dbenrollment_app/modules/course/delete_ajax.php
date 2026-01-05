<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if (!isset($_POST['course_id'])) {
    echo json_encode(['success'=>false,'error'=>'Missing course_id']);
    exit;
}

$id = intval($_POST['course_id']);
$stmt = $conn->prepare("UPDATE tblcourse SET is_deleted = 1 WHERE course_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>