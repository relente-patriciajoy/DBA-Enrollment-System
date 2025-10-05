<?php
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM tblenrollment
        WHERE student_id LIKE '%$search%'
        OR section_id LIKE '%$search%'
        ORDER BY enrollment_id ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['enrollment_id']}</td>
                <td>{$row['student_id']}</td>
                <td>{$row['section_id']}</td>
                <td>{$row['date_enrolled']}</td>
                <td>{$row['status']}</td>
                <td>{$row['letter_grade']}</td>
                <td>
                    <a href='edit.php?id={$row['enrollment_id']}'>Edit</a> |
                    <a href='delete.php?id={$row['enrollment_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No records found.</td></tr>";
}
?>