<?php
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM tblterm
        WHERE term_code LIKE '%$search%'
        ORDER BY term_id ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['term_id']}</td>
                <td>{$row['term_code']}</td>
                <td>{$row['start_date']}</td>
                <td>{$row['end_date']}</td>
                <td>
                    <a href='edit.php?id={$row['term_id']}'>Edit</a> |
                    <a href='delete.php?id={$row['term_id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No records found.</td></tr>";
}
?>