<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Missing id');
    }

    $id = intval($_GET['id']);

    if ($id <= 0) {
        throw new Exception('Invalid id');
    }

    $stmt = $conn->prepare("
        SELECT program_id, program_code, program_name, dept_id
        FROM tblprogram
        WHERE program_id = ? AND is_deleted = 0
    ");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        throw new Exception('Program not found');
    }

    $row = $res->fetch_assoc();

    echo json_encode([
        'success' => true,
        'data' => $row
    ], JSON_PRETTY_PRINT);  // Optional: Pretty print for debugging

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();  // Safe close in finally block
}
?>
