<?php
include_once '../../config/database.php';

// Load student data
if (isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $sql = "SELECT * FROM tblstudent WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_no = $_POST['student_no'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = !empty($_POST['birthdate']) ? $_POST['birthdate'] : null;
    $year_level = $_POST['year_level'];
    $program_id = $_POST['program_id'];

    $sql = "UPDATE tblstudent 
            SET student_no=?, last_name=?, first_name=?, email=?, gender=?, birthdate=?, year_level=?, program_id=?
            WHERE student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssii", $student_no, $last_name, $first_name, $email, $gender, $birthdate, $year_level, $program_id, $student_id);

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
<head>
    <title>Edit Student</title>
</head>
<body>
<h2>Edit Student</h2>

<form method="POST">
    <label>Student No:</label><br>
    <input type="text" name="student_no" value="<?php echo $student['student_no']; ?>" required><br><br>

    <label>Last Name:</label><br>
    <input type="text" name="last_name" value="<?php echo $student['last_name']; ?>" required><br><br>

    <label>First Name:</label><br>
    <input type="text" name="first_name" value="<?php echo $student['first_name']; ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo $student['email']; ?>" required><br><br>

    <label>Gender:</label><br>
    <select name="gender" required>
        <option value="Male" <?php if($student['gender']=='Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if($student['gender']=='Female') echo 'selected'; ?>>Female</option>
    </select><br><br>

    <label>Birthdate:</label><br>
    <input type="date" name="birthdate" value="<?php echo $student['birthdate']; ?>"><br><br>

    <label>Year Level:</label><br>
    <input type="text" name="year_level" value="<?php echo $student['year_level']; ?>" required><br><br>

    <label>Program:</label><br>
    <select name="program_id" required>
        <?php
        $programs = $conn->query("SELECT program_id, program_name FROM tblprogram");
        while ($row = $programs->fetch_assoc()) {
            $selected = ($row['program_id'] == $student['program_id']) ? "selected" : "";
            echo "<option value='{$row['program_id']}' $selected>{$row['program_name']}</option>";
        }
        ?>
    </select><br><br>

    <button type="submit">Update</button>
    <button type="button" onclick="window.location.href='index.php'">Cancel</button>
</form>
</body>
</html>