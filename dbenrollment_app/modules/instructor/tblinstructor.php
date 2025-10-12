<?php
session_start();
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$where = "is_deleted = 0";
if ($search !== '') {
    $where .= " AND (last_name LIKE '%$search%' OR first_name LIKE '%$search%' OR email LIKE '%$search%')";
}

$sql = "
    SELECT * FROM tblinstructor
    WHERE {$where}
    ORDER BY last_name ASC, first_name ASC
";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['last_name']}</td>
            <td>{$row['first_name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['dept_id']}</td>
            <td class='text-center'>
                <button class='btn btn-sm btn-warning' onclick='editInstructor({$row['instructor_id']})'>Edit</button>
                <button class='btn btn-sm btn-danger' onclick='deleteInstructor({$row['instructor_id']})'>Delete</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No records found.</td></tr>";
}
?>