<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['enrollment_id'])) {
        throw new Exception("Enrollment ID is required");
    }

    $enrollment_id = intval($_GET['enrollment_id']);
    
    $sql = "SELECT
                e.enrollment_id,
                e.student_id,
                e.section_id,
                DATE_FORMAT(e.date_enrolled, '%Y-%m-%d') as date_enrolled,
                e.status,
                e.letter_grade,
                CONCAT(s.first_name, ' ', s.last_name) as student_name,
                c.course_code,
                sec.section_code,
                c.course_title
            FROM tblenrollment e
            INNER JOIN tblstudent s ON e.student_id = s.student_id
            INNER JOIN tblsection sec ON e.section_id = sec.section_id
            INNER JOIN tblcourse c ON sec.course_id = c.course_id
            WHERE e.enrollment_id = ?
            AND e.is_deleted = 0";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Enrollment not found");
    }

    $enrollment = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "enrollment" => $enrollment
    ]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error in get_enrollment.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>