<?php
include_once '../../config/database.php';

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

    $stmt = $conn->prepare("INSERT INTO tblsection
        (section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiisssii", $section_code, $course_id, $term_id, $instructor_id, $day_pattern, $start_time, $end_time, $room_id, $max_capacity);

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
<head><title>Add Section</title></head>
<body>
<h2>Add Section</h2>
<form method="POST">
    <label>Section Code</label><br>
    <input type="text" name="section_code" required><br><br>

    <label>Course ID</label><br>
    <input type="number" name="course_id" required><br><br>

    <label>Term ID</label><br>
    <input type="number" name="term_id" required><br><br>

    <label>Instructor ID</label><br>
    <input type="number" name="instructor_id" required><br><br>

    <label>Day Pattern</label><br>
    <input type="text" name="day_pattern" required><br><br>

    <label>Start Time</label><br>
    <input type="time" name="start_time" required><br><br>

    <label>End Time</label><br>
    <input type="time" name="end_time" required><br><br>

    <label>Room ID</label><br>
    <input type="number" name="room_id" required><br><br>

    <label>Max Capacity</label><br>
    <input type="number" name="max_capacity" required><br><br>

    <button type="submit">Save</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>