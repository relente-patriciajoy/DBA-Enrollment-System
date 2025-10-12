<!-- Not functional -->
<?php
include_once '../../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_no = $_POST['student_no'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = !empty($_POST['birthdate']) ? $_POST['birthdate'] : null;
    $year_level = $_POST['year_level'];
    $program_id = $_POST['program_id'];

    $sql = "INSERT INTO tblstudent (student_no, last_name, first_name, email, gender, birthdate, year_level, program_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $student_no, $last_name, $first_name, $email, $gender, $birthdate, $year_level, $program_id);
    
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
<head>
    <title>Add Student</title>
</head>
<body>

<h2>Add Student</h2>

<form method="POST">
    <label>Student No:</label><br>
    <input type="text" name="student_no" required><br><br>

    <label>Last Name:</label><br>
    <input type="text" name="last_name" required><br><br>

    <label>First Name:</label><br>
    <input type="text" name="first_name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Gender:</label><br>
    <select name="gender" required>
        <option value="">--Select--</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select><br><br>

    <label>Birthdate:</label><br>
    <input type="date" name="birthdate"><br><br>

    <label>Year Level:</label><br>
    <input type="text" name="year_level" required><br><br>

    <label>Program:</label><br>
    <select name="program_id" required>
        <option value="">--Select Program--</option>
        <?php
        $programs = $conn->query("SELECT program_id, program_name FROM tblprogram");
        while ($row = $programs->fetch_assoc()) {
            echo "<option value='{$row['program_id']}'>{$row['program_name']}</option>";
        }
        ?>
    </select><br><br>

    <button type="submit">Save</button>
    <button type="button" onclick="window.location.href='index.php'">Cancel</button>
</form>

</body>
</html>