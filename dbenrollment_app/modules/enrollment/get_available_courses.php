<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['student_id'])) {
        throw new Exception("Missing student ID");
    }

    $student_id = intval($_GET['student_id']);
    
    // Get student's program, year level
    $studentStmt = $conn->prepare("SELECT program_id, year_level FROM tblstudent WHERE student_id = ? AND is_deleted = 0");
    $studentStmt->bind_param("i", $student_id);
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();
    
    if ($studentResult->num_rows === 0) {
        throw new Exception("Student not found");
    }
    
    $student = $studentResult->fetch_assoc();
    $program_id = $student['program_id'];
    $year_level = $student['year_level'];
    
    // Convert year level to number
    if (strpos($year_level, '1st') !== false) {
        $year_number = 1;
    } elseif (strpos($year_level, '2nd') !== false) {
        $year_number = 2;
    } elseif (strpos($year_level, '3rd') !== false) {
        $year_number = 3;
    } elseif (strpos($year_level, '4th') !== false) {
        $year_number = 4;
    } else {
        $year_number = 1;
    }

    // Get courses AND sections for this year level
    $sql = "SELECT DISTINCT
                c.course_id,
                c.course_code,
                c.course_title,
                c.units,
                c.year_level as course_year_level,
                s.section_id,
                s.section_code,
                s.day_pattern,
                s.start_time,
                s.end_time,
                s.room_id,
                s.year_level as section_year_level,
                (SELECT GROUP_CONCAT(prereq.course_code SEPARATOR ', ')
                 FROM tblcourse_prerequisite cp
                 INNER JOIN tblcourse prereq ON cp.prerequisite_course_id = prereq.course_id
                 WHERE cp.course_id = c.course_id AND cp.is_deleted = 0
                ) as prerequisites,
                (SELECT COUNT(*)
                 FROM tblcourse_prerequisite cp
                 INNER JOIN tblsection sect ON sect.course_id = cp.prerequisite_course_id
                 INNER JOIN tblenrollment enr ON enr.section_id = sect.section_id
                 WHERE cp.course_id = c.course_id
                 AND cp.is_deleted = 0
                 AND enr.student_id = ?
                 AND enr.letter_grade IN ('A', 'B', 'C')
                 AND enr.is_deleted = 0
                ) as completed_prereqs,
                (SELECT COUNT(*)
                 FROM tblcourse_prerequisite cp
                 WHERE cp.course_id = c.course_id AND cp.is_deleted = 0
                ) as total_prereqs
            FROM tblcourse c
            INNER JOIN tblsection s ON c.course_id = s.course_id
            LEFT JOIN tblenrollment e ON s.section_id = e.section_id AND e.student_id = ? AND e.is_deleted = 0
            WHERE c.is_deleted = 0 
            AND s.is_deleted = 0
            AND s.year_level = ?
            AND e.enrollment_id IS NULL
            ORDER BY c.course_code ASC, s.section_code ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $student_id, $year_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        // Check if prerequisites are met
        $prereqs_met = ($row['total_prereqs'] == 0) || ($row['completed_prereqs'] >= $row['total_prereqs']);

        $row['can_enroll'] = $prereqs_met;
        $row['prereq_message'] = $prereqs_met ? '' : 'Missing prerequisites: ' . ($row['prerequisites'] ?? 'Unknown');

        $courses[] = $row;
    }
    
    echo json_encode([
        "success" => true,
        "courses" => $courses,
        "student" => [
            "program_id" => $program_id,
            "year_level" => $year_level,
            "year_number" => $year_number
        ]
    ]);
    
    $stmt->close();
    $studentStmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Error in get_available_courses.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>