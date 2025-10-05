<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM tblstudent
        WHERE student_no LIKE '%$search%'
        OR last_name LIKE '%$search%'
        OR first_name LIKE '%$search%'
        ORDER BY student_id ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['student_no']}</td>
            <td>{$row['last_name']}</td>
            <td>{$row['first_name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['gender']}</td>
            <td>{$row['birthdate']}</td>
            <td>{$row['year_level']}</td>
            <td>{$row['program_id']}</td>
            <td>
                <a href='edit.php?id={$row['student_id']}'>Edit</a> |
                <a href='delete.php?id={$row['student_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='9'>No records found.</td></tr>";
}
?>