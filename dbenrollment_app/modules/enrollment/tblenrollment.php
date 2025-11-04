<?php
// Get search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
if (!empty($search)) {
    $sql = "SELECT enrollment_id, student_id, section_id, date_enrolled, status, letter_grade
            FROM tblenrollment
            WHERE is_deleted = 0
            AND (student_id LIKE ? OR section_id LIKE ?)
            ORDER BY date_enrolled DESC";
    $stmt = $conn->prepare($sql);
    $searchParam = "%{$search}%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
} else {
    $sql = "SELECT enrollment_id, student_id, section_id, date_enrolled, status, letter_grade
            FROM tblenrollment
            WHERE is_deleted = 0
            ORDER BY date_enrolled DESC";
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