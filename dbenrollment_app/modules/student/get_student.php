<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['student_id'])) { // Changed from 'id' to 'student_id'
        throw new Exception("Missing student ID");
    }

    $id = intval($_GET['student_id']); // Changed from 'id' to 'student_id'
    $stmt = $conn->prepare("SELECT * FROM tblstudent WHERE student_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $response = [
                "success" => true,
                "data" => $row
            ];
            echo json_encode($response);
            exit;
        } else {
            throw new Exception("Student not found");
        }
    } else {
        throw new Exception("Failed to fetch student data");
    }

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in get_student.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
    exit;
}
?>