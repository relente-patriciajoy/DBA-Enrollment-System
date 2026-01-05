<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Handle form submission
    $room_code = $_POST['room_code'];
    $building = $_POST['building'];
    $capacity = (int)$_POST['capacity'];

    $stmt = $conn->prepare("INSERT INTO tblroom (room_code, building, capacity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $room_code, $building, $capacity);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Room</title></head>
<body>
<h2>Add Room</h2>
<form method="POST">
    <label>Room Code</label><br>
    <input type="text" name="room_code" required><br><br>

    <label>Building</label><br>
    <input type="text" name="building" required><br><br>

    <label>Capacity</label><br>
    <input type="number" name="capacity" required><br><br>

    <button type="submit">Save</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>