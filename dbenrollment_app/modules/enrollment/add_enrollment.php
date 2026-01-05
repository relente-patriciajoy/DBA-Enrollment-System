<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');

requireRoleAjax('admin');

include_once '../../config/database.php';

try {
    // Basic validation
    if (empty($_POST['student_id']) || empty($_POST['section_id']) || empty($_POST['date_enrolled'])) {
        throw new Exception("Student, Section, and Date are required fields.");
    }

    $student_id = intval($_POST['student_id']);
    $section_id = intval($_POST['section_id']);
    $date_enrolled = $_POST['date_enrolled'];
    $status = trim($_POST['status'] ?? 'Regular'); // Default to Regular if empty
    $letter_grade = !empty($_POST['letter_grade']) ? trim($_POST['letter_grade']) : NULL;

    // Check if student is already enrolled in this section
    $checkStmt = $conn->prepare("
        SELECT enrollment_id
        FROM tblenrollment
        WHERE student_id = ? AND section_id = ? AND is_deleted = 0
    ");
    $checkStmt->bind_param("ii", $student_id, $section_id);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        throw new Exception("Student is already enrolled in this section.");
    }
    $checkStmt->close();

    // Insert new enrollment record
    $stmt = $conn->prepare("INSERT INTO tblenrollment (student_id, section_id, date_enrolled, status, letter_grade, is_deleted) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("iisss", $student_id, $section_id, $date_enrolled, $status, $letter_grade);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Enrollment added successfully"
        ]);
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>