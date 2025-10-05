<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_esc = $conn->real_escape_string($search);

$sql = "SELECT * FROM tblcourse_prerequisite
        WHERE course_id LIKE '%$search_esc%'
        OR prerequisite_course_id LIKE '%$search_esc%'
        ORDER BY prereq_id ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['prereq_id']}</td>
                <td>{$row['course_id']}</td>
                <td>{$row['prerequisite_course_id']}</td>
                <td>
                    <a href='edit.php?id={$row['prereq_id']}'>Edit</a> |
                    <a href='delete.php?id={$row['prereq_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No records found.</td></tr>";
}
?>