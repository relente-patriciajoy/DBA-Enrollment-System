<?php
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $term_code = $_POST['term_code'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("INSERT INTO tblterm (term_code, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $term_code, $start_date, $end_date);

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
<head><title>Add Term</title></head>
<body>
<h2>Add Term</h2>
<form method="POST">
    <label>Term Code</label><br>
    <input type="text" name="term_code" required><br><br>

    <label>Start Date</label><br>
    <input type="date" name="start_date" required><br><br>

    <label>End Date</label><br>
    <input type="date" name="end_date" required><br><br>

    <button type="submit">Save</button>
    <a href="index.php">Cancel</a>
</form>
</body>
</html>