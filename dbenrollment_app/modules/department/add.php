<?php
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dept_code = $_POST['dept_code'];
    $dept_name = $_POST['dept_name'];

    $sql = "INSERT INTO tbldepartment (dept_code, dept_name) VALUES ('$dept_code', '$dept_name')";
    if ($conn->query($sql)) {
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
    <title>Add Department</title>
</head>
<body>
    <h2>Add Department</h2>
    <form method="POST">
        <label>Department Code:</label><br>
        <input type="text" name="dept_code" required><br><br>

        <label>Department Name:</label><br>
        <input type="text" name="dept_name" required><br><br>

        <button type="submit">Save</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>