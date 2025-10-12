<?php
session_start();
header('Content-Type: application/json');
include_once '../../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Validate required fields
    if (empty($_POST['student_no']) || empty($_POST['last_name']) || 
        empty($_POST['first_name']) || empty($_POST['email'])) {
        throw new Exception("Please fill in all required fields.");
    }

    // Sanitize inputs
    $student_no = trim($_POST['student_no']);
    $last_name = trim($_POST['last_name']);
    $first_name = trim($_POST['first_name']);
    $email = trim($_POST['email']);
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $birthdate = !empty($_POST['birthdate']) ? $_POST['birthdate'] : null;
    $year_level = isset($_POST['year_level']) ? trim($_POST['year_level']) : '';
    $program_id = isset($_POST['program_id']) ? intval($_POST['program_id']) : 0;

    $sql = "INSERT INTO tblstudent 
            (student_no, last_name, first_name, email, gender, birthdate, year_level, program_id, is_deleted)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssssi", 
        $student_no,
        $last_name,
        $first_name,
        $email,
        $gender,
        $birthdate,
        $year_level,
        $program_id
    );

    if ($stmt->execute()) {
        $_SESSION['new_student_id'] = $conn->insert_id;
        $response = [
            "success" => true,
            "message" => "Student added successfully",
            "student_id" => $conn->insert_id
        ];
        echo json_encode($response);
        exit; // Add exit after successful response
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in add_ajax.php: " . $e->getMessage());
    $response = [
        "success" => false,
        "error" => $e->getMessage()
    ];
    echo json_encode($response);
    exit; // Add exit after error response
}
?>
