<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM tblinstructor
        WHERE first_name LIKE '%$search%'
        OR last_name LIKE '%$search%'
        OR email LIKE '%$search%'
        ORDER BY instructor_id ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['instructor_id']}</td>
            <td>{$row['last_name']}, {$row['first_name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['dept_id']}</td>
            <td>
                <a href='edit.php?id={$row['instructor_id']}'>Edit</a> |
                <a href='delete.php?id={$row['instructor_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No records found.</td></tr>";
}
?>