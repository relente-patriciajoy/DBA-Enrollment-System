<?php
include_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$result = $conn->query("SELECT * FROM tblcourse WHERE course_id = $id");
$row = $result->fetch_assoc();

if (!$row) {
    echo "Record not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code   = $_POST['course_code'];
    $course_title  = $_POST['course_title'];
    $units         = (int)$_POST['units'];
    $lecture_hours = (int)$_POST['lecture_hours'];
    $lab_hours     = (int)$_POST['lab_hours'];
    $dept_id       = !empty($_POST['dept_id']) ? (int)$_POST['dept_id'] : null;

    $stmt = $conn->prepare("UPDATE tblcourse SET course_code=?, course_title=?, units=?, lecture_hours=?, lab_hours=?, dept_id=? WHERE course_id=?");
    $stmt->bind_param("ssiiiii", $course_code, $course_title, $units, $lecture_hours, $lab_hours, $dept_id, $id);
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
<head><title>Edit Course</title></head>
<body>
<h2>Edit Course</h2>
<form method="POST">
    <label>Course Code</label><br>
    <input type="text" name="course_code" value="<?php echo htmlspecialchars($row['course_code']); ?>" required><br><br>

    <label>Course Title</label><br>
    <input type="text" name="course_title" value="<?php echo htmlspecialchars($row['course_title']); ?>" required><br><br>

    <label>Lecture Hours</label><br>
    <input type="number" name="lecture_hours" value="<?php echo (int)$row['lecture_hours']; ?>" min="0" required><br><br>

    <label>Lab Hours</label><br>
    <input type="number" name="lab_hours" value="<?php echo (int)$row['lab_hours']; ?>" min="0" required><br><br>

    <label>Units</label><br>
    <input type="number" name="units" value="<?php echo (int)$row['units']; ?>" min="0" required><br><br>

    <label>Department ID</label><br>
    <input type="number" name="dept_id" value="<?php echo htmlspecialchars($row['dept_id']); ?>"><br><br>

    <button type="submit">Update</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>