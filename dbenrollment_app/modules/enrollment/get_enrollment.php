<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['enrollment_id'])) {
        throw new Exception("Missing enrollment ID");
    }

    $id = intval($_GET['enrollment_id']);
    $stmt = $conn->prepare("SELECT enrollment_id, student_id, section_id, date_enrolled, status, letter_grade FROM tblenrollment WHERE enrollment_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                "success" => true,
                "data" => $row
            ]);
            exit;
        } else {
            throw new Exception("Enrollment not found");
        }
    } else {
        throw new Exception("Failed to fetch enrollment data");
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in get_enrollment.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
    exit;
}
?>