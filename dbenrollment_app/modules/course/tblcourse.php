<?php
// Get search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
if (!empty($search)) {
    $sql = "SELECT c.course_id, c.course_code, c.course_title, c.units, c.lecture_hours, c.lab_hours, c.dept_id
            FROM tblcourse c
            WHERE c.is_deleted = 0
            AND (c.course_code LIKE ? OR c.course_title LIKE ?)
            ORDER BY c.course_title ASC";
    $stmt = $conn->prepare($sql);
    $searchParam = "%{$search}%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
} else {
    $sql = "SELECT c.course_id, c.course_code, c.course_title, c.units, c.lecture_hours, c.lab_hours, c.dept_id
            FROM tblcourse c
            WHERE c.is_deleted = 0
            ORDER BY c.course_title ASC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['course_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['course_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['course_title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['units']) . "</td>";
        echo "<td>" . htmlspecialchars($row['lecture_hours']) . "</td>";
        echo "<td>" . htmlspecialchars($row['lab_hours']) . "</td>";
        echo "<td>" . htmlspecialchars($row['dept_id']) . "</td>";
        echo "<td class='text-center'>";
        echo "<button class='btn btn-warning btn-sm edit-course' data-id='" . $row['course_id'] . "'>Edit</button> ";
        echo "<button class='btn btn-danger btn-sm delete-course' data-id='" . $row['course_id'] . "'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>No courses found</td></tr>";
}

$stmt->close();
?>