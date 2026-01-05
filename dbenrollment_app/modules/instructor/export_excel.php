<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';

// Headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=instructors.csv");

$output = fopen("php://output", "w");
fputcsv($output, ["Instructor ID", "Last Name", "First Name", "Email", "Department ID"]);

$result = $conn->query("SELECT * FROM tblinstructor ORDER BY instructor_id ASC");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['instructor_id'],
        $row['last_name'],
        $row['first_name'],
        $row['email'],
        $row['dept_id']
    ]);
}
fclose($output);
exit;
?>