<?php
// Get search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
if (!empty($search)) {
    $sql = "SELECT section_id, section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity
            FROM tblsection
            WHERE is_deleted = 0
            AND section_code LIKE ?
            ORDER BY section_code ASC";
    $stmt = $conn->prepare($sql);
    $searchParam = "%{$search}%";
    $stmt->bind_param("s", $searchParam);
} else {
    $sql = "SELECT section_id, section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity
            FROM tblsection
            WHERE is_deleted = 0
            ORDER BY section_code ASC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['section_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['section_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['course_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['term_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['instructor_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['day_pattern']) . "</td>";
        echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['room_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['max_capacity']) . "</td>";
        echo "<td class='text-center'>";
        echo "<button class='btn btn-warning btn-sm edit-section' data-id='" . $row['section_id'] . "'>Edit</button> ";
        echo "<button class='btn btn-danger btn-sm delete-section' data-id='" . $row['section_id'] . "'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='11' class='text-center'>No sections found</td></tr>";
}

$stmt->close();
?>