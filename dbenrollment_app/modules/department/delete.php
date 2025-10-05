<?php
include_once '../../config/database.php';

$id = $_GET['id'];
$sql = "DELETE FROM tbldepartment WHERE dept_id = $id";

if ($conn->query($sql)) {
    header("Location: index.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}
?>