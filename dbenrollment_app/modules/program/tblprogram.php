<?php
// Prevent "Notice: session_start(): Ignoring session_start()"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use include_once to prevent "Fatal error: Cannot redeclare requireRole()"
include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');
requireRole('admin');

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$where = "p.is_deleted = 0";
if ($search !== '') {
    $where .= " AND (p.program_code LIKE '%$search%' OR p.program_name LIKE '%$search%')";
}

$sql = "SELECT p.program_id, p.program_code, p.program_name, p.dept_id, d.dept_name
        FROM tblprogram p
        LEFT JOIN tbldepartment d ON p.dept_id = d.dept_id
        WHERE $where
        ORDER BY p.program_name ASC, p.program_code ASC
        LIMIT 500";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><?= $row['program_code']; ?></td>
            <td><?= $row['program_name']; ?></td>
            <td><?= $row['dept_name']; ?></td>
            <td class="text-center">
                <button class="btn btn-sm btn-warning edit-program" data-id="<?= $row['program_id']; ?>">Edit</button>
                <button class="btn btn-sm btn-danger delete-program" data-id="<?= $row['program_id']; ?>">Delete</button>
            </td>
        </tr>
        <?php
    }
} else {
    echo "<tr><td colspan='4' class='text-center'>No records found.</td></tr>";
}

?>