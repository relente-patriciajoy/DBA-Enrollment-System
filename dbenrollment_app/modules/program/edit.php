<?php
include_once '../../config/database.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM tblprogram WHERE program_id=$id");
$program = $result->fetch_assoc();

if ($_POST) {
    $stmt = $conn->prepare("UPDATE tblprogram SET program_code=?, program_name=?, dept_id=? WHERE program_id=?");
    $stmt->bind_param("ssii", $_POST['program_code'], $_POST['program_name'], $_POST['dept_id'], $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Program</title>
</head>
<body>
<h2>Edit Program</h2>
<form method="POST">
    Program Code: <input type="text" name="program_code" value="<?php echo $program['program_code']; ?>"><br>
    Program Name: <input type="text" name="program_name" value="<?php echo $program['program_name']; ?>"><br>
    Department ID: <input type="number" name="dept_id" value="<?php echo $program['dept_id']; ?>"><br>
    <button type="submit">Update</button>
</form>
</body>
</html>