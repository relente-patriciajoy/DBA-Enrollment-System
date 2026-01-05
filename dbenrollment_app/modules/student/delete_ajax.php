<?php
session_start();

// Set header
header('Content-Type: application/json');

// Then do auth checks
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

// Then database connection
include_once '../../config/database.php';

try {
    if (empty($_POST['id'])) {
        throw new Exception("Missing student ID.");
    }

    $id = $_POST['id'];

    // Soft delete — mark as deleted
    $stmt = $conn->prepare("UPDATE tblstudent SET is_deleted = 1 WHERE student_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        throw new Exception("Failed to delete student.");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>