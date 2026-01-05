<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';

$id = $_GET['id'];
$sql = "SELECT * FROM tbldepartment WHERE dept_id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dept_code = $_POST['dept_code'];
    $dept_name = $_POST['dept_name'];

    $update = "UPDATE tbldepartment SET dept_code='$dept_code', dept_name='$dept_name' WHERE dept_id=$id";
    if ($conn->query($update)) {
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
    <title>Edit Department</title>
</head>
<body>
    <h2>Edit Department</h2>
    <form method="POST">
        <label>Department Code:</label><br>
        <input type="text" name="dept_code" value="<?php echo $row['dept_code']; ?>" required><br><br>

        <label>Department Name:</label><br>
        <input type="text" name="dept_name" value="<?php echo $row['dept_name']; ?>" required><br><br>

        <button type="submit">Update</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>