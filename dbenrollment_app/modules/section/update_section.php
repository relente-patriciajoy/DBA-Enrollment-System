<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['section_id']) || empty($_POST['section_code']) || empty($_POST['course_id']) || empty($_POST['term_id'])) {
        throw new Exception("Section ID, section code, course ID, and term ID are required");
    }

    $section_id = intval($_POST['section_id']);
    $section_code = trim($_POST['section_code']);
    $course_id = intval($_POST['course_id']);
    $term_id = intval($_POST['term_id']);
    $instructor_id = intval($_POST['instructor_id']);
    $day_pattern = trim($_POST['day_pattern']);
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room_id = intval($_POST['room_id']);
    $max_capacity = intval($_POST['max_capacity']);

    // Check if section code already exists for another section
    $checkStmt = $conn->prepare("SELECT section_id FROM tblsection WHERE section_code = ? AND section_id != ? AND is_deleted = 0");
    $checkStmt->bind_param("si", $section_code, $section_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        throw new Exception("Section code already exists for another section");
    }
    $checkStmt->close();

    // Update section
    $stmt = $conn->prepare("UPDATE tblsection SET section_code = ?, course_id = ?, term_id = ?, instructor_id = ?, day_pattern = ?, start_time = ?, end_time = ?, room_id = ?, max_capacity = ? WHERE section_id = ? AND is_deleted = 0");
    $stmt->bind_param("siiissssii", $section_code, $course_id, $term_id, $instructor_id, $day_pattern, $start_time, $end_time, $room_id, $max_capacity, $section_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Section updated successfully"
            ]);
        } else {
            // Check if section exists
            $verifyStmt = $conn->prepare("SELECT section_id FROM tblsection WHERE section_id = ? AND is_deleted = 0");
            $verifyStmt->bind_param("i", $section_id);
            $verifyStmt->execute();
            $verifyResult = $verifyStmt->get_result();
            
            if ($verifyResult->num_rows > 0) {
                echo json_encode([
                    "success" => true,
                    "message" => "No changes made"
                ]);
            } else {
                throw new Exception("Section not found");
            }
            $verifyStmt->close();
        }
    } else {
        throw new Exception("Failed to update section: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in update_section.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>