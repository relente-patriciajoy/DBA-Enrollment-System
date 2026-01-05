<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Use your actual file names
include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');

// Ensure requireRole exists in your role_check.php
requireRole('admin');

include_once '../../config/database.php';

try {
    if (empty($_GET['enrollment_id'])) {
        throw new Exception("Enrollment ID is required");
    }

    $enrollment_id = intval($_GET['enrollment_id']);
    
    $sql = "SELECT e.*,
            CONCAT(s.first_name, ' ', s.last_name) as student_name,
            c.course_code, sec.section_code
            FROM tblenrollment e
            INNER JOIN tblstudent s ON e.student_id = s.student_id
            INNER JOIN tblsection sec ON e.section_id = sec.section_id
            INNER JOIN tblcourse c ON sec.course_id = c.course_id
            WHERE e.enrollment_id = ? AND e.is_deleted = 0";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Enrollment not found");
    }

    echo json_encode(["success" => true, "enrollment" => $result->fetch_assoc()]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>