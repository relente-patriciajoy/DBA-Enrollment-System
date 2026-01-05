<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $dept_id = $_POST['dept_id'];

    $sql = "INSERT INTO tblinstructor (first_name, last_name, email, dept_id)
            VALUES ('$first_name', '$last_name', '$email', '$dept_id')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Instructor</title>
</head>
<body>
    <h2>Add Instructor</h2>
    <form method="POST">
        <label>First Name:</label><br>
        <input type="text" name="first_name" required><br><br>

        <label>Last Name:</label><br>
        <input type="text" name="last_name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Department ID:</label><br>
        <input type="number" name="dept_id" required><br><br>

        <button type="submit">Save</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>