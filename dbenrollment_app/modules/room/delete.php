<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');

include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // The JavaScript must send 'room_id'
    $id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;

    if ($id > 0) {
        // Soft delete consistent with other modules
        $stmt = $conn->prepare("UPDATE tblroom SET is_deleted = 1 WHERE room_id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            ob_clean();
            echo json_encode(['success' => true, 'message' => 'Room deleted successfully']);
        } else {
            ob_clean();
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    }
}