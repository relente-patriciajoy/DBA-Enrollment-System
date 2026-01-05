<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code   = $_POST['course_code'];
    $course_title  = $_POST['course_title'];
    $units         = (int)$_POST['units'];
    $lecture_hours = (int)$_POST['lecture_hours'];
    $lab_hours     = (int)$_POST['lab_hours'];
    $dept_id       = !empty($_POST['dept_id']) ? (int)$_POST['dept_id'] : null;

    $stmt = $conn->prepare("INSERT INTO tblcourse (course_code, course_title, units, lecture_hours, lab_hours, dept_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiii", $course_code, $course_title, $units, $lecture_hours, $lab_hours, $dept_id);
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
<head><title>Add Course</title></head>
<body>
<h2>Add Course</h2>
<form method="POST">
    <label>Course Code</label><br>
    <input type="text" name="course_code" required><br><br>

    <label>Course Title</label><br>
    <input type="text" name="course_title" required><br><br>

    <label>Lecture Hours</label><br>
    <input type="number" name="lecture_hours" value="0" min="0" required><br><br>

    <label>Lab Hours</label><br>
    <input type="number" name="lab_hours" value="0" min="0" required><br><br>

    <label>Units</label><br>
    <input type="number" name="units" value="0" min="0" required><br><br>

    <label>Department ID</label><br>
    <input type="number" name="dept_id"><br><br>

    <button type="submit">Save</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>