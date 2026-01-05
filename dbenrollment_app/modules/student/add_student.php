<!-- Not functional -->
<?php
session_start();

// Then do auth checks
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

// Then database connection
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (
        empty($_POST['student_no']) ||
        empty($_POST['last_name']) ||
        empty($_POST['first_name']) ||
        empty($_POST['email'])
    ) {
        echo "error:missing_fields";
        exit;
    }

    $sql = "INSERT INTO tblstudent 
        (student_no, last_name, first_name, email, gender, birthdate, year_level, program_id, is_deleted)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssis",
        $_POST['student_no'],
        $_POST['last_name'],
        $_POST['first_name'],
        $_POST['email'],
        $_POST['gender'],
        $_POST['birthdate'],
        $_POST['year_level'],
        $_POST['program_id']
    );
    if ($stmt->execute()) {
        $new_id = $conn->insert_id;
        $_SESSION['new_student_id'] = $new_id;
        echo "success";
    } else {
        echo "error:db";
    }
    $stmt->close();
}
?>
