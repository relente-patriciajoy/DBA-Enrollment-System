<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$res = $conn->query("SELECT * FROM tblcourse_prerequisite WHERE prereq_id = $id");
$row = $res->fetch_assoc();
if (!$row) { echo "Record not found."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = (int)$_POST['course_id'];
    $prerequisite_course_id = (int)$_POST['prerequisite_course_id'];

    $stmt = $conn->prepare("UPDATE tblcourse_prerequisite SET course_id=?, prerequisite_course_id=? WHERE prereq_id=?");
    $stmt->bind_param("iii", $course_id, $prerequisite_course_id, $id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else { echo "Error: " . $conn->error; }
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Prerequisite</title></head>
<body>
<h2>Edit Prerequisite</h2>
<form method="POST">
    <label>Course ID</label><br>
    <input type="number" name="course_id" value="<?php echo $row['course_id']; ?>" required><br><br>

    <label>Prerequisite Course ID</label><br>
    <input type="number" name="prerequisite_course_id" value="<?php echo $row['prerequisite_course_id']; ?>" required><br><br>

    <button type="submit">Update</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>