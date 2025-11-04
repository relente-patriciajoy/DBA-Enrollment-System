<?php
header('Content-Type: application/json');
include_once '../../config/database.php';

try {
    if (empty($_GET['student_id'])) {
        throw new Exception("Missing student ID");
    }

    $student_id = intval($_GET['student_id']);

    // Get student info
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

    // Get the current active term
    $termStmt = $conn->prepare("SELECT term_id, term_code FROM tblterm WHERE is_active = 1 AND is_deleted = 0 LIMIT 1");
    $termStmt->execute();
    $termResult = $termStmt->get_result();

    if ($termResult->num_rows === 0) {
        throw new Exception("No active term found. Please contact administrator.");
    }

    $currentTerm = $termResult->fetch_assoc();
    $current_term_id = $currentTerm['term_id'];
    $term_code = $currentTerm['term_code'];

    // Get all terms ordered properly
    $allTermsStmt = $conn->prepare("
        SELECT DISTINCT term_id, term_code
        FROM tblterm
        WHERE is_deleted = 0
        ORDER BY start_date ASC
    ");
    $allTermsStmt->execute();
    $allTermsResult = $allTermsStmt->get_result();

    $all_terms = [];
    while ($term_row = $allTermsResult->fetch_assoc()) {
        $all_terms[] = $term_row;
    }

    // **NEW: Get detailed enrollment status for each term**
    $enrolledTermsStmt = $conn->prepare("
        SELECT DISTINCT
            t.term_id,
            t.term_code,
            COUNT(DISTINCT e.enrollment_id) as enrolled_courses,
            COUNT(DISTINCT CASE WHEN e.letter_grade IN ('A', 'B', 'C') THEN e.enrollment_id END) as passed_courses
        FROM tblterm t
        LEFT JOIN tblsection s ON t.term_id = s.term_id AND s.year_level = ? AND s.is_deleted = 0
        LEFT JOIN tblenrollment e ON s.section_id = e.section_id AND e.student_id = ? AND e.is_deleted = 0
        WHERE t.is_deleted = 0
        GROUP BY t.term_id, t.term_code
        ORDER BY t.start_date ASC
    ");
    $enrolledTermsStmt->bind_param("ii", $year_number, $student_id);
    $enrolledTermsStmt->execute();
    $enrolledTermsResult = $enrolledTermsStmt->get_result();

    $term_progress = [];
    $enrolled_term_ids = [];

    while ($enrolled_term = $enrolledTermsResult->fetch_assoc()) {
        $term_progress[] = [
            'term_id' => $enrolled_term['term_id'],
            'term_code' => $enrolled_term['term_code'],
            'enrolled_courses' => $enrolled_term['enrolled_courses'],
            'passed_courses' => $enrolled_term['passed_courses'],
            'is_completed' => ($enrolled_term['enrolled_courses'] > 0) // Has enrollments
        ];

        if ($enrolled_term['enrolled_courses'] > 0) {
            $enrolled_term_ids[] = $enrolled_term['term_id'];
        }
    }

    // Determine the NEXT term the student should enroll in
    $allowed_term_id = null;
    $allowed_term_code = null;

    foreach ($all_terms as $term) {
        if (!in_array($term['term_id'], $enrolled_term_ids)) {
            $allowed_term_id = $term['term_id'];
            $allowed_term_code = $term['term_code'];
            break;
        }
    }

    // If student has completed all terms for their year level
    if ($allowed_term_id === null) {
        echo json_encode([
            "success" => true,
            "courses" => [],
            "student" => [
                "program_id" => $program_id,
                "year_level" => $year_level,
                "year_number" => $year_number
            ],
            "current_term" => [
                "term_id" => $current_term_id,
                "term_code" => $term_code
            ],
            "allowed_term" => null,
            "term_progress" => $term_progress,
            "message" => "You have completed all terms for " . $year_level . ". Please contact the registrar to proceed to the next year level."
        ]);
        exit;
    }

    // Check if the active term matches the term the student should enroll in
    $term_mismatch = false;
    $term_mismatch_message = "";

    if ($current_term_id != $allowed_term_id) {
        $term_mismatch = true;
        $term_mismatch_message = "You must complete {$allowed_term_code} before enrolling in {$term_code}. The current active term ({$term_code}) does not match your enrollment sequence.";
    }

    // Get courses ONLY for the term the student should be enrolled in
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
                s.term_id,
                s.year_level as section_year_level,
                t.term_code,
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
            INNER JOIN tblterm t ON s.term_id = t.term_id
            LEFT JOIN tblenrollment e ON s.section_id = e.section_id AND e.student_id = ? AND e.is_deleted = 0
            WHERE c.is_deleted = 0
            AND s.is_deleted = 0
            AND s.year_level = ?
            AND s.term_id = ?
            AND e.enrollment_id IS NULL
            ORDER BY c.course_code ASC, s.section_code ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $student_id, $student_id, $year_number, $allowed_term_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $prereqs_met = ($row['total_prereqs'] == 0) || ($row['completed_prereqs'] >= $row['total_prereqs']);

        $row['can_enroll'] = $prereqs_met && !$term_mismatch;
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
        ],
        "current_term" => [
            "term_id" => $current_term_id,
            "term_code" => $term_code
        ],
        "allowed_term" => [
            "term_id" => $allowed_term_id,
            "term_code" => $allowed_term_code
        ],
        "term_mismatch" => $term_mismatch,
        "term_mismatch_message" => $term_mismatch_message,
        "term_progress" => $term_progress
    ]);

    $stmt->close();
    $studentStmt->close();
    $termStmt->close();
    $allTermsStmt->close();
    $enrolledTermsStmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Error in get_available_courses.php: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>