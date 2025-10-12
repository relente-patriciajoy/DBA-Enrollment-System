<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
  // Validate required fields
  if (empty($_POST['student_no']) || empty($_POST['last_name']) || empty($_POST['first_name']) || empty($_POST['email'])) {
      throw new Exception("Please fill in all required fields.");
  }

  $student_no = $_POST['student_no'];
  $last_name = $_POST['last_name'];
  $first_name = $_POST['first_name'];
  $email = $_POST['email'];
  $gender = $_POST['gender'] ?? '';
  $birthdate = $_POST['birthdate'] ?? null;
  $year_level = $_POST['year_level'] ?? '';
  $program_id = $_POST['program_id'] ?? '';

  $stmt = $conn->prepare("INSERT INTO tblstudent (student_no, last_name, first_name, email, gender, birthdate, year_level, program_id, is_deleted)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
  $stmt->bind_param("ssssssss", $student_no, $last_name, $first_name, $email, $gender, $birthdate, $year_level, $program_id);

  if ($stmt->execute()) {
      session_start();
      $_SESSION['new_student_id'] = $conn->insert_id;
      echo json_encode([
          "success" => true,
          "student_id" => $conn->insert_id,
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
      throw new Exception("Failed to add student. Please try again.");
  }

  $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
