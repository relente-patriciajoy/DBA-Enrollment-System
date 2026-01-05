<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $section_id = (int)$_POST['section_id'];
    $date_enrolled = $_POST['date_enrolled'];
    $status = $_POST['status'];
    $letter_grade = $_POST['letter_grade'];

    $stmt = $conn->prepare("INSERT INTO tblenrollment (student_id, section_id, date_enrolled, status, letter_grade) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $student_id, $section_id, $date_enrolled, $status, $letter_grade);

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
<head><title>Add Enrollment</title></head>
<body>
<h2>Add Enrollment</h2>
<form method="POST">
    <label>Student ID</label><br>
    <input type="number" name="student_id" required><br><br>

    <label>Section ID</label><br>
    <input type="number" name="section_id" required><br><br>

    <label>Date Enrolled</label><br>
    <input type="date" name="date_enrolled" required><br><br>

    <label>Status</label><br>
    <input type="text" name="status" required><br><br>

    <label>Letter Grade</label><br>
    <input type="text" name="letter_grade"><br><br>

    <button type="submit">Save</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>