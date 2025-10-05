<?php
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM tblroom
        WHERE room_code LIKE '%$search%'
        OR building LIKE '%$search%'
        ORDER BY room_id ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['room_id']}</td>
                <td>{$row['room_code']}</td>
                <td>{$row['building']}</td>
                <td>{$row['capacity']}</td>
                <td>
                    <a href='edit.php?id={$row['room_id']}'>Edit</a> |
                    <a href='delete.php?id={$row['room_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No records found.</td></tr>";
}
?>