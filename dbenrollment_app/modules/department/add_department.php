<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

try {
    if (empty($_POST['dept_code']) || empty($_POST['dept_name'])) {
        throw new Exception("Department code and name are required");
    }

    $dept_code = trim($_POST['dept_code']);
    $dept_name = trim($_POST['dept_name']);

    // Check if department code already exists
    $checkStmt = $conn->prepare("SELECT dept_id FROM tbldepartment WHERE dept_code = ? AND is_deleted = 0");
    $checkStmt->bind_param("s", $dept_code);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        throw new Exception("Department code already exists");
    }
    $checkStmt->close();

    // Insert new department
    $stmt = $conn->prepare("INSERT INTO tbldepartment (dept_code, dept_name, is_deleted) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $dept_code, $dept_name);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Department added successfully",
            "dept_id" => $conn->insert_id
        ]);
    } else {
        throw new Exception("Failed to add department: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in add_department.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>