<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if (!isset($_POST['dept_id'])) {
    echo json_encode(['success'=>false,'error'=>'Missing dept_id']);
    exit;
}

$id = intval($_POST['dept_id']);
$stmt = $conn->prepare("UPDATE tbldepartment SET is_deleted = 1 WHERE dept_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>