<?php
include_once '../../config/database.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM tblroom WHERE room_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: index.php");
exit;
?>