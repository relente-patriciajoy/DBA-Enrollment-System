<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';
$id = $_GET['id'];
$conn->query("DELETE FROM tblprogram WHERE program_id=$id");
header("Location: index.php");
exit;
?>