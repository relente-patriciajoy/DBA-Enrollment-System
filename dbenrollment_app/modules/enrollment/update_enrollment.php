<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['enrollment_id']) || empty($_POST['student_id']) || empty($_POST['section_id']) || empty($_POST['date_enrolled']) || empty($_POST['status'])) {
        throw new Exception("Enrollment ID, Student ID, Section ID, Date Enrolled, and Status are required");
    }

    $enrollment_id = intval($_POST['enrollment_id']);
    $student_id = intval($_POST['student_id']);
    $section_id = intval($_POST['section_id']);
    $date_enrolled = $_POST['date_enrolled'];
    $status = trim($_POST['status']);
    $letter_grade = isset($_POST['letter_grade']) ? trim($_POST['letter_grade']) : NULL;

    // Update enrollment
    $stmt = $conn->prepare("UPDATE tblenrollment SET student_id = ?, section_id = ?, date_enrolled = ?, status = ?, letter_grade = ? WHERE enrollment_id = ? AND is_deleted = 0");
    $stmt->bind_param("iisssi", $student_id, $section_id, $date_enrolled, $status, $letter_grade, $enrollment_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Enrollment updated successfully"
            ]);
        } else {
            // Check if enrollment exists
            $verifyStmt = $conn->prepare("SELECT enrollment_id FROM tblenrollment WHERE enrollment_id = ? AND is_deleted = 0");
            $verifyStmt->bind_param("i", $enrollment_id);
            $verifyStmt->execute();
            $verifyResult = $verifyStmt->get_result();
            
            if ($verifyResult->num_rows > 0) {
                echo json_encode([
                    "success" => true,
                    "message" => "No changes made"
                ]);
            } else {
                throw new Exception("Enrollment not found");
            }
            $verifyStmt->close();
        }
    } else {
        throw new Exception("Failed to update enrollment: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in update_enrollment.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>