<?php
include_once '../../config/database.php';

$student_no = $_POST['student_no'];
$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$birthdate = $_POST['birthdate'];
$year_level = $_POST['year_level'];
$program_id = $_POST['program_id'];

$sql = "INSERT INTO tblstudent (student_no, last_name, first_name, email, gender, birthdate, year_level, program_id)
        VALUES ('$student_no', '$last_name', '$first_name', '$email', '$gender', 
                " . ($birthdate ? "'$birthdate'" : "NULL") . ", '$year_level', '$program_id')";

if ($conn->query($sql)) {
    $id = $conn->insert_id;
    $result = $conn->query("SELECT * FROM tblstudent WHERE student_id = $id");
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => $conn->error]);
}
?>