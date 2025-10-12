<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['student_id'])) {
        throw new Exception("Invalid student ID.");
    }

    $id = $_POST['student_id'];
    $student_no = $_POST['student_no'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'] ?? null;
    $year_level = $_POST['year_level'];
    $program_id = $_POST['program_id'];

    $stmt = $conn->prepare("UPDATE tblstudent
                            SET student_no=?, last_name=?, first_name=?, email=?, gender=?, birthdate=?, year_level=?, program_id=?
                            WHERE student_id=?");
    $stmt->bind_param("ssssssssi", $student_no, $last_name, $first_name, $email, $gender, $birthdate, $year_level, $program_id, $id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "student_id" => $id,
            "student_no" => $student_no,
            "last_name" => $last_name,
            "first_name" => $first_name,
            "email" => $email,
            "gender" => $gender,
            "birthdate" => $birthdate,
            "year_level" => $year_level,
            "program_id" => $program_id
        ]);
    } else {
        throw new Exception("Failed to update student.");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>