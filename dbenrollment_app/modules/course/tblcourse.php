<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_esc = $conn->real_escape_string($search);

$sql = "SELECT * FROM tblcourse
        WHERE course_code LIKE '%$search_esc%'
        OR course_title LIKE '%$search_esc%'
        ORDER BY course_id ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['course_id']}</td>
                <td>{$row['course_code']}</td>
                <td>{$row['course_title']}</td>
                <td>{$row['lecture_hours']}</td>
                <td>{$row['lab_hours']}</td>
                <td>{$row['units']}</td>
                <td>{$row['dept_id']}</td>
                <td>
                    <a href='edit.php?id={$row['course_id']}'>Edit</a> |
                    <a href='delete.php?id={$row['course_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No records found.</td></tr>";
}
?>