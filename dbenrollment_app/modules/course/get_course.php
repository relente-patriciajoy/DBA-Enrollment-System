<?php
session_start();
header('Content-Type: application/json');

include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRoleAjax('admin');

include_once '../../config/database.php';

try {
    if (empty($_GET['course_id'])) {
        throw new Exception("Missing course ID");
    }

    $id = intval($_GET['course_id']);
    $stmt = $conn->prepare("SELECT course_id, course_code, course_title, units, lecture_hours, lab_hours, dept_id FROM tblcourse WHERE course_id = ? AND is_deleted = 0");
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
            throw new Exception("Course not found");
        }
    } else {
        throw new Exception("Failed to fetch course data");
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    error_log("Error in get_course.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
    exit;
}
?>