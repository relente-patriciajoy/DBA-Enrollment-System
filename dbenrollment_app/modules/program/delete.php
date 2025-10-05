<?php
include_once '../../config/database.php';
$id = $_GET['id'];
$conn->query("DELETE FROM tblprogram WHERE program_id=$id");
header("Location: index.php");
exit;
?>