<?php
// Prevent "Notice: session_start(): Ignoring session_start()"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use include_once to prevent "Fatal error: Cannot redeclare requireRole()"
include_once('../includes/auth_check.php');
include_once('../includes/role_check.php');
requireRole('admin');

// Get search parameter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
if (!empty($search)) {
    $sql = "SELECT * FROM tbldepartment
            WHERE is_deleted = 0
            AND (dept_code LIKE ? OR dept_name LIKE ?)
            ORDER BY dept_name ASC";
    $stmt = $conn->prepare($sql);
    $searchParam = "%{$search}%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
} else {
    $sql = "SELECT * FROM tbldepartment WHERE is_deleted = 0 ORDER BY dept_name ASC";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['dept_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['dept_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['dept_name']) . "</td>";
        echo "<td class='text-center'>";
        echo "<button class='btn btn-warning btn-sm edit-department' data-id='" . $row['dept_id'] . "'>Edit</button> ";
        echo "<button class='btn btn-danger btn-sm delete-department' data-id='" . $row['dept_id'] . "'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center'>No departments found</td></tr>";
}

$stmt->close();
?>