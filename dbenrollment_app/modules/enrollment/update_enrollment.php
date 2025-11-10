<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_POST['enrollment_id'])) {
        throw new Exception("Enrollment ID is required");
    }

    $enrollment_id = intval($_POST['enrollment_id']);
    $date_enrolled = $_POST['date_enrolled'];
    $status = trim($_POST['status']);
    $letter_grade = isset($_POST['letter_grade']) && $_POST['letter_grade'] !== '' ? trim($_POST['letter_grade']) : NULL;

    // Update enrollment
    $stmt = $conn->prepare("UPDATE tblenrollment
                           SET date_enrolled = ?, status = ?, letter_grade = ?
                           WHERE enrollment_id = ? AND is_deleted = 0");
    $stmt->bind_param("sssi", $date_enrolled, $status, $letter_grade, $enrollment_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Enrollment updated successfully"
        ]);
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