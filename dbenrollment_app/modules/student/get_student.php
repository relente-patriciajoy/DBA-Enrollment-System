<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("Missing student ID.");
    }

    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM tblstudent WHERE student_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Student not found or has been deleted.");
    }

    $student = $result->fetch_assoc();
    echo json_encode($student);

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>