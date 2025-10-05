<?php
include_once '../../config/database.php';

// Headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=departments.csv");

$output = fopen("php://output", "w");
fputcsv($output, ["Dept ID", "Dept Code", "Dept Name"]);

$result = $conn->query("SELECT * FROM tbldepartment ORDER BY dept_id ASC");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['dept_id'], $row['dept_code'], $row['dept_name']]);
}
fclose($output);
exit;
?>