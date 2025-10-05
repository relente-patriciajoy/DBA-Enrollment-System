<?php
include_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = $conn->query("SELECT * FROM tblenrollment WHERE enrollment_id=$id");
$row = $res->fetch_assoc();
if (!$row) { echo "Record not found."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $section_id = (int)$_POST['section_id'];
    $date_enrolled = $_POST['date_enrolled'];
    $status = $_POST['status'];
    $letter_grade = $_POST['letter_grade'];

    $stmt = $conn->prepare("UPDATE tblenrollment SET student_id=?, section_id=?, date_enrolled=?, status=?, letter_grade=? WHERE enrollment_id=?");
    $stmt->bind_param("iisssi", $student_id, $section_id, $date_enrolled, $status, $letter_grade, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Enrollment</title></head>
<body>
<h2>Edit Enrollment</h2>
<form method="POST">
    <label>Student ID</label><br>
    <input type="number" name="student_id" value="<?php echo $row['student_id']; ?>" required><br><br>

    <label>Section ID</label><br>
    <input type="number" name="section_id" value="<?php echo $row['section_id']; ?>" required><br><br>

    <label>Date Enrolled</label><br>
    <input type="date" name="date_enrolled" value="<?php echo $row['date_enrolled']; ?>" required><br><br>

    <label>Status</label><br>
    <input type="text" name="status" value="<?php echo $row['status']; ?>" required><br><br>

    <label>Letter Grade</label><br>
    <input type="text" name="letter_grade" value="<?php echo $row['letter_grade']; ?>"><br><br>

    <button type="submit">Update</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>