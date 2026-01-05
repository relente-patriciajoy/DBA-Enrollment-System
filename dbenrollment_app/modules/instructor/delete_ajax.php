<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

try {
    if (empty($_POST['id'])) {
        throw new Exception("Missing instructor ID.");
    }

    // Soft delete - mark as deleted
    $stmt = $conn->prepare("UPDATE tblinstructor SET is_deleted = 1 WHERE instructor_id = ?");
    $stmt->bind_param("i", $_POST['id']);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Instructor deleted successfully"
        ]);
    } else {
        throw new Exception("Failed to delete instructor.");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
