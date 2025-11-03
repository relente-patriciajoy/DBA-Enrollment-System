<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['section_code']) || empty($_POST['course_id']) || empty($_POST['term_id'])) {
        throw new Exception("Section code, course ID, and term ID are required");
    }

    $section_code = trim($_POST['section_code']);
    $course_id = intval($_POST['course_id']);
    $term_id = intval($_POST['term_id']);
    $instructor_id = intval($_POST['instructor_id']);
    $day_pattern = trim($_POST['day_pattern']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room_id = intval($_POST['room_id']);
    $max_capacity = intval($_POST['max_capacity']);
    $year_level = intval($_POST['year_level']); // Get year level from form

    // Check if section code already exists for THIS COURSE and YEAR LEVEL
    $checkStmt = $conn->prepare("SELECT section_id FROM tblsection
                                 WHERE section_code = ?
                                 AND course_id = ?
                                 AND year_level = ?
                                 AND is_deleted = 0");
    $checkStmt->bind_param("sii", $section_code, $course_id, $year_level);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        throw new Exception("Section code '$section_code' already exists for this course and year level");
    }
    $checkStmt->close();

    // Insert new section with year_level
    $stmt = $conn->prepare("INSERT INTO tblsection (section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity, year_level, is_deleted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("siiissssii", $section_code, $course_id, $term_id, $instructor_id, $day_pattern, $start_time, $end_time, $room_id, $max_capacity, $year_level);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Section added successfully",
            "section_id" => $conn->insert_id
        ]);
    } else {
        throw new Exception("Failed to add section: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in add_section.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>