<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM tblprogram
        WHERE program_code LIKE '%$search%'
        OR program_name LIKE '%$search%'
        ORDER BY program_id ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['program_code']}</td>
            <td>{$row['program_name']}</td>
            <td>{$row['dept_id']}</td>
            <td>
                <a href='edit.php?id={$row['program_id']}'>Edit</a> |
                <a href='delete.php?id={$row['program_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No records found.</td></tr>";
}
?>