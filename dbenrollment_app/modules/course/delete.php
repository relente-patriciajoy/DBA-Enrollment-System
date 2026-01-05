<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM tblcourse WHERE course_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: index.php");
exit;
?>