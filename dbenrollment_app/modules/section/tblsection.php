<?php
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM tblsection
        WHERE section_code LIKE '%$search%'
        ORDER BY section_id ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['section_id']}</td>
                <td>{$row['section_code']}</td>
                <td>{$row['course_id']}</td>
                <td>{$row['term_id']}</td>
                <td>{$row['instructor_id']}</td>
                <td>{$row['day_pattern']}</td>
                <td>{$row['start_time']}</td>
                <td>{$row['end_time']}</td>
                <td>{$row['room_id']}</td>
                <td>{$row['max_capacity']}</td>
                <td>
                    <a href='edit.php?id={$row['section_id']}'>Edit</a> |
                    <a href='delete.php?id={$row['section_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='11'>No records found.</td></tr>";
}
?>