<?php
ob_start(); // Buffer all output
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');
requireRoleAjax('admin');
include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['enrollment_id']) ? intval($_POST['enrollment_id']) : 0;

    if ($id > 0) {
        // Perform the deletion
        $stmt = $conn->prepare("DELETE FROM tblenrollment WHERE enrollment_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            ob_clean(); // Remove any warnings or extra spaces
            echo json_encode(['success' => true, 'message' => 'Enrollment deleted successfully']);
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        $stmt->close();
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    }
}
$conn->close();