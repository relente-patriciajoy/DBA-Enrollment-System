<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

try {
    if (empty($_POST['dept_id']) || empty($_POST['dept_code']) || empty($_POST['dept_name'])) {
        throw new Exception("Department ID, code, and name are required");
    }

    $dept_id = intval($_POST['dept_id']);
    $dept_code = trim($_POST['dept_code']);
    $dept_name = trim($_POST['dept_name']);

    // Check if department code already exists for another department
    $checkStmt = $conn->prepare("SELECT dept_id FROM tbldepartment WHERE dept_code = ? AND dept_id != ? AND is_deleted = 0");
    $checkStmt->bind_param("si", $dept_code, $dept_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        throw new Exception("Department code already exists for another department");
    }
    $checkStmt->close();

    // Update department
    $stmt = $conn->prepare("UPDATE tbldepartment SET dept_code = ?, dept_name = ? WHERE dept_id = ? AND is_deleted = 0");
    $stmt->bind_param("ssi", $dept_code, $dept_name, $dept_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Department updated successfully"
            ]);
        } else {
            // Check if department exists
            $verifyStmt = $conn->prepare("SELECT dept_id FROM tbldepartment WHERE dept_id = ? AND is_deleted = 0");
            $verifyStmt->bind_param("i", $dept_id);
            $verifyStmt->execute();
            $verifyResult = $verifyStmt->get_result();

            if ($verifyResult->num_rows > 0) {
                // Department exists but no changes were made
                echo json_encode([
                    "success" => true,
                    "message" => "No changes made"
                ]);
            } else {
                throw new Exception("Department not found");
            }
            $verifyStmt->close();
        }
    } else {
        throw new Exception("Failed to update department: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in update_department.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>