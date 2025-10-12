<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['instructor_id'])) {
        throw new Exception("Missing instructor ID");
    }

    $stmt = $conn->prepare("SELECT * FROM tblinstructor WHERE instructor_id = ? AND is_deleted = 0");
    $stmt->bind_param("i", $_GET['instructor_id']);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                "success" => true,
                "data" => $row
            ]);
        } else {
            throw new Exception("Instructor not found");
        }
    } else {
        throw new Exception("Failed to fetch instructor data");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
