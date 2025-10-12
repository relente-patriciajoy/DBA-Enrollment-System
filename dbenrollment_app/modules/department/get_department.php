<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['dept_id'])) {
        throw new Exception("Missing department ID");
    }

    $id = intval($_GET['dept_id']);
    $stmt = $conn->prepare("SELECT dept_id, dept_code, dept_name FROM tbldepartment WHERE dept_id = ? AND is_deleted = 0");
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
            throw new Exception("Department not found");
        }
    } else {
        throw new Exception("Failed to fetch department data");
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in get_department.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
    exit;
}
?>