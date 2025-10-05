<?php
include_once '../../config/database.php';

$id = $_GET['id'];

$sql = "DELETE FROM tblinstructor WHERE instructor_id = $id";

if ($conn->query($sql) === TRUE) {
    header("Location: index.php");
    exit;
} else {
    echo "Error deleting record: " . $conn->error;
}
?>