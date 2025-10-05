<?php
include_once '../../config/database.php';

$id = $_GET['id'];

// Fetch record
$sql = "SELECT * FROM tblinstructor WHERE instructor_id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $dept_id = $_POST['dept_id'];

    $sql = "UPDATE tblinstructor
            SET first_name='$first_name', last_name='$last_name', email='$email', dept_id='$dept_id'
            WHERE instructor_id=$id";

    if ($conn->query($sql) === TRUE) {
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
    <title>Edit Instructor</title>
</head>
<body>
    <h2>Edit Instructor</h2>
    <form method="POST">
        <label>First Name:</label><br>
        <input type="text" name="first_name" value="<?php echo $row['first_name']; ?>" required><br><br>

        <label>Last Name:</label><br>
        <input type="text" name="last_name" value="<?php echo $row['last_name']; ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo $row['email']; ?>" required><br><br>

        <label>Department ID:</label><br>
        <input type="number" name="dept_id" value="<?php echo $row['dept_id']; ?>" required><br><br>

        <button type="submit">Update</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>