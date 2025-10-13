<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['course_code']) || empty($_POST['course_title'])) {
        throw new Exception("Course code and title are required");
    }

    $course_code = trim($_POST['course_code']);
    $course_title = trim($_POST['course_title']);
    $units = floatval($_POST['units']);
    $lecture_hours = intval($_POST['lecture_hours']);
    $lab_hours = intval($_POST['lab_hours']);
    $dept_id = intval($_POST['dept_id']);

    // Check if course code already exists
    $checkStmt = $conn->prepare("SELECT course_id FROM tblcourse WHERE course_code = ? AND is_deleted = 0");
    $checkStmt->bind_param("s", $course_code);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        throw new Exception("Course code already exists");
    }
    $checkStmt->close();

    // Insert new course
    $stmt = $conn->prepare("INSERT INTO tblcourse (course_code, course_title, units, lecture_hours, lab_hours, dept_id, is_deleted) VALUES (?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("ssdiii", $course_code, $course_title, $units, $lecture_hours, $lab_hours, $dept_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Course added successfully",
            "course_id" => $conn->insert_id
        ]);
    } else {
        throw new Exception("Failed to add course: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in add_course.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>