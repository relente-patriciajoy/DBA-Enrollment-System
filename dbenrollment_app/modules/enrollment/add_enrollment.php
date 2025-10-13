<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['student_id']) || empty($_POST['section_id']) || empty($_POST['date_enrolled']) || empty($_POST['status'])) {
        throw new Exception("Student ID, Section ID, Date Enrolled, and Status are required");
    }

    $student_id = intval($_POST['student_id']);
    $section_id = intval($_POST['section_id']);
    $date_enrolled = $_POST['date_enrolled'];
    $status = trim($_POST['status']);
    $letter_grade = isset($_POST['letter_grade']) ? trim($_POST['letter_grade']) : NULL;

    // Insert new enrollment record (the newest record date will be on top and the oldest at the bottom)
    $stmt = $conn->prepare("INSERT INTO tblenrollment (student_id, section_id, date_enrolled, status, letter_grade, is_deleted) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("iisss", $student_id, $section_id, $date_enrolled, $status, $letter_grade);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Enrollment added successfully",
            "enrollment_id" => $conn->insert_id
        ]);
    } else {
        throw new Exception("Failed to add enrollment: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in add_enrollment.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>