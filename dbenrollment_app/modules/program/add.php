<?php
include_once '../../config/database.php';

if ($_POST) {
    $stmt = $conn->prepare("INSERT INTO tblprogram (program_code, program_name, dept_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $_POST['program_code'], $_POST['program_name'], $_POST['dept_id']);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Program</title>
</head>
<body>
<h2>Add Program</h2>
<form method="POST">
    Program Code: <input type="text" name="program_code" required><br>
    Program Name: <input type="text" name="program_name" required><br>
    Department ID: <input type="number" name="dept_id"><br>
    <button type="submit">Save</button>
</form>
</body>
</html>