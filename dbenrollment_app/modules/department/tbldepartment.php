<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM tbldepartment
        WHERE dept_code LIKE '%$search%'
        OR dept_name LIKE '%$search%'
        ORDER BY dept_id ASC";

$result = $conn->query($sql);

echo "<table border='1' cellpadding='8'>";
echo "<tr>
        <th>ID</th>
        <th>Department Code</th>
        <th>Department Name</th>
        <th>Actions</th>
      </tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['dept_id']}</td>
                <td>{$row['dept_code']}</td>
                <td>{$row['dept_name']}</td>
                <td>
                    <a href='edit.php?id={$row['dept_id']}'>Edit</a> |
                    <a href='delete.php?id={$row['dept_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No records found.</td></tr>";
}
echo "</table>";
?>