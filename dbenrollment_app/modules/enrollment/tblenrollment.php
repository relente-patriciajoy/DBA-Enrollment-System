<?php
// Get search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query with JOINs to get student and course info
if (!empty($search)) {
    $sql = "SELECT e.enrollment_id, e.student_id, e.section_id, e.date_enrolled, e.status, e.letter_grade,
                   CONCAT(s.last_name, ', ', s.first_name) as student_name,
                   c.course_code, sec.section_code
            FROM tblenrollment e
            INNER JOIN tblstudent s ON e.student_id = s.student_id
            INNER JOIN tblsection sec ON e.section_id = sec.section_id
            INNER JOIN tblcourse c ON sec.course_id = c.course_id
            WHERE e.is_deleted = 0
            AND (e.student_id LIKE ? OR e.section_id LIKE ? OR s.last_name LIKE ? OR c.course_code LIKE ?)
            ORDER BY e.date_enrolled DESC";
    $stmt = $conn->prepare($sql);
    $searchParam = "%{$search}%";
    $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
} else {
    $sql = "SELECT e.enrollment_id, e.student_id, e.section_id, e.date_enrolled, e.status, e.letter_grade,
                   CONCAT(s.last_name, ', ', s.first_name) as student_name,
                   c.course_code, sec.section_code
            FROM tblenrollment e
            INNER JOIN tblstudent s ON e.student_id = s.student_id
            INNER JOIN tblsection sec ON e.section_id = sec.section_id
            INNER JOIN tblcourse c ON sec.course_id = c.course_id
            WHERE e.is_deleted = 0
            ORDER BY e.date_enrolled DESC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['enrollment_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['section_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['date_enrolled']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['letter_grade'] ?: 'Not yet graded') . "</td>";
        echo "<td class='text-center'>";
        echo "<button class='btn btn-warning btn-sm btn-edit-enrollment' data-enrollment-id='" . $row['enrollment_id'] . "'>Edit</button> ";
        echo "<button class='btn btn-danger btn-sm btn-delete-enrollment' data-enrollment-id='" . $row['enrollment_id'] . "'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No enrollments found</td></tr>";
}

$stmt->close();
?>