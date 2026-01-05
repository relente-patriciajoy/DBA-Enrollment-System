<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

try {
    // Basic validation
    if (empty($_POST['program_id'])) throw new Exception("Missing program_id.");
    if (!isset($_POST['program_code']) || !isset($_POST['program_name']) || !isset($_POST['dept_id'])) {
        throw new Exception("Missing fields.");
    }

    $id = intval($_POST['program_id']);
    $program_code = trim($_POST['program_code']);
    $program_name = trim($_POST['program_name']);
    $dept_id = intval($_POST['dept_id']);

    // Check if program exists and not deleted (if soft delete)
    $checkStmt = $conn->prepare("SELECT program_id FROM tblprogram WHERE program_id = ? " . (isset($is_deleted_column) ? "AND is_deleted = 0" : ""));
    if (!$checkStmt) throw new Exception("Check prepare failed: " . $conn->error);
    $checkStmt->bind_param("i", $id);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows == 0) throw new Exception("Program not found or deleted.");

    // Prepare update
    $stmt = $conn->prepare("UPDATE tblprogram SET program_code = ?, program_name = ?, dept_id = ? WHERE program_id = ?");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

    $stmt->bind_param("ssii", $program_code, $program_name, $dept_id, $id);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    // Fetch updated row (including dept_name)
    $sql = "SELECT p.program_id, p.program_code, p.program_name, p.dept_id, COALESCE(d.dept_name, '') AS dept_name
            FROM tblprogram p LEFT JOIN tbldepartment d ON p.dept_id = d.dept_id
            WHERE p.program_id = ? LIMIT 1";
    $s2 = $conn->prepare($sql);
    $s2->bind_param("i", $id);
    $s2->execute();
    $res = $s2->get_result();
    $data = $res->fetch_assoc();

    echo json_encode(['success' => true, 'data' => $data]);

    $stmt->close();
    $s2->close();
    $checkStmt->close();
    $conn->close();  // Add this
    exit;
} catch (Exception $e) {
    error_log("program/update.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
?>