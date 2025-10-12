<!-- Edit Modal -->
<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['student_id'])) {
        throw new Exception("Missing student ID");
    }

    $stmt = $conn->prepare("SELECT * FROM tblstudent WHERE student_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $_GET['student_id']);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                "success" => true,
                "data" => $row
            ]);
        } else {
            throw new Exception("Student not found");
        }
    } else {
        throw new Exception("Failed to fetch student data");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>