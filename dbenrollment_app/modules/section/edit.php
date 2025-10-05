<?php
include_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch existing data
$res = $conn->query("SELECT * FROM tblsection WHERE section_id = $id");
$row = $res->fetch_assoc();
if (!$row) {
    echo "Record not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_code = $_POST['section_code'];
    $course_id = (int)$_POST['course_id'];
    $term_id = (int)$_POST['term_id'];
    $instructor_id = (int)$_POST['instructor_id'];
    $day_pattern = $_POST['day_pattern'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room_id = (int)$_POST['room_id'];
    $max_capacity = (int)$_POST['max_capacity'];

    $stmt = $conn->prepare("UPDATE tblsection
        SET section_code=?, course_id=?, term_id=?, instructor_id=?, day_pattern=?, start_time=?, end_time=?, room_id=?, max_capacity=?
        WHERE section_id=?");
    $stmt->bind_param("siiisssiii", $section_code, $course_id, $term_id, $instructor_id, $day_pattern, $start_time, $end_time, $room_id, $max_capacity, $id);

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
<head><title>Edit Section</title></head>
<body>
<h2>Edit Section</h2>
<form method="POST">
    <label>Section Code</label><br>
    <input type="text" name="section_code" value="<?php echo $row['section_code']; ?>" required><br><br>

    <label>Course ID</label><br>
    <input type="number" name="course_id" value="<?php echo $row['course_id']; ?>" required><br><br>

    <label>Term ID</label><br>
    <input type="number" name="term_id" value="<?php echo $row['term_id']; ?>" required><br><br>

    <label>Instructor ID</label><br>
    <input type="number" name="instructor_id" value="<?php echo $row['instructor_id']; ?>" required><br><br>

    <label>Day Pattern</label><br>
    <input type="text" name="day_pattern" value="<?php echo $row['day_pattern']; ?>" required><br><br>

    <label>Start Time</label><br>
    <input type="time" name="start_time" value="<?php echo $row['start_time']; ?>" required><br><br>

    <label>End Time</label><br>
    <input type="time" name="end_time" value="<?php echo $row['end_time']; ?>" required><br><br>

    <label>Room ID</label><br>
    <input type="number" name="room_id" value="<?php echo $row['room_id']; ?>" required><br><br>

    <label>Max Capacity</label><br>
    <input type="number" name="max_capacity" value="<?php echo $row['max_capacity']; ?>" required><br><br>

    <button type="submit">Update</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>