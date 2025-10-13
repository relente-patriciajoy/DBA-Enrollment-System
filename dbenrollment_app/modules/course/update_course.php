<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['course_id']) || empty($_POST['course_code']) || empty($_POST['course_title'])) {
        throw new Exception("Course ID, code, and title are required");
    }

    $course_id = intval($_POST['course_id']);
    $course_code = trim($_POST['course_code']);
    $course_title = trim($_POST['course_title']);
    $units = floatval($_POST['units']);
    $lecture_hours = intval($_POST['lecture_hours']);
    $lab_hours = intval($_POST['lab_hours']);
    $dept_id = intval($_POST['dept_id']);

    // Check if course code already exists for another course
    $checkStmt = $conn->prepare("SELECT course_id FROM tblcourse WHERE course_code = ? AND course_id != ? AND is_deleted = 0");
    $checkStmt->bind_param("si", $course_code, $course_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        throw new Exception("Course code already exists for another course");
    }
    $checkStmt->close();

    // Update course
    $stmt = $conn->prepare("UPDATE tblcourse SET course_code = ?, course_title = ?, units = ?, lecture_hours = ?, lab_hours = ?, dept_id = ? WHERE course_id = ? AND is_deleted = 0");
    $stmt->bind_param("ssdiiii", $course_code, $course_title, $units, $lecture_hours, $lab_hours, $dept_id, $course_id); // string, double, integer
    
    if ($stmt->execute()) {
      if ($stmt->affected_rows > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Course updated successfully"
        ]);
      } else {
        // Check if course exists
        $verifyStmt = $conn->prepare("SELECT course_id FROM tblcourse WHERE course_id = ? AND is_deleted = 0");
        $verifyStmt->bind_param("i", $course_id);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();

        if ($verifyResult->num_rows > 0) {
          echo json_encode([
              "success" => true,
              "message" => "No changes made"
          ]);
        } else {
          throw new Exception("Course not found");
        }
        $verifyStmt->close();
      }
    } else {
        throw new Exception("Failed to update course: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in update_course.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>