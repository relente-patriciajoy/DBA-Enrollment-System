<?php
// Ensure database connection exists
if (!isset($conn)) {
    include_once '../../config/database.php';
}

// 1. Handle Search Filter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// 2. Build the Query
$sql = "SELECT
            e.enrollment_id,
            e.student_id,
            e.section_id,
            e.date_enrolled,
            e.status,
            e.letter_grade,
            s.student_no,
            CONCAT(s.first_name, ' ', s.last_name) as student_name,
            c.course_code,
            sec.section_code
        FROM tblenrollment e
        JOIN tblstudent s ON e.student_id = s.student_id
        JOIN tblsection sec ON e.section_id = sec.section_id
        JOIN tblcourse c ON sec.course_id = c.course_id
        WHERE e.is_deleted = 0";

if (!empty($search)) {
    $sql .= " AND (s.student_no LIKE '%$search%'
               OR s.last_name LIKE '%$search%'
               OR s.first_name LIKE '%$search%'
               OR e.enrollment_id = '$search')";
}

$sql .= " ORDER BY e.enrollment_id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Determine status badge color
        $statusBadge = 'bg-secondary';
        if ($row['status'] == 'Regular') $statusBadge = 'bg-success';
        if ($row['status'] == 'Irregular') $statusBadge = 'bg-warning text-dark';
        if ($row['status'] == 'Dropped') $statusBadge = 'bg-danger';

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['enrollment_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['student_no']) . " — " . htmlspecialchars($row['student_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['course_code']) . " (" . htmlspecialchars($row['section_code']) . ")</td>";
        echo "<td>" . date('M d, Y', strtotime($row['date_enrolled'])) . "</td>";
        echo "<td><span class='badge $statusBadge'>" . htmlspecialchars($row['status']) . "</span></td>";
        echo "<td>" . ($row['letter_grade'] ? htmlspecialchars($row['letter_grade']) : '<span class="text-muted">N/A</span>') . "</td>";

        // ACTION BUTTONS
        echo "<td>
                <div class='btn-group' role='group'>
                    <button type='button'
                            class='btn btn-sm btn-outline-primary btn-edit-enrollment'
                            data-bs-toggle='modal'
                            data-bs-target='#enrollmentEditModal'
                            data-enrollment-id='{$row['enrollment_id']}'
                            title='Edit Enrollment'>
                        Edit
                    </button>
                    <button type='button'
                            class='btn btn-sm btn-outline-danger btn-delete-enrollment'
                            data-enrollment-id='{$row['enrollment_id']}'
                            title='Delete Enrollment'>
                        Delete
                    </button>
                </div>
            </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No enrollments found.</td></tr>";
}
?>