<?php
include_once '../../config/database.php';

// Headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=programs.csv");

$output = fopen("php://output", "w");
fputcsv($output, ["Program Code", "Program Name", "Department ID"]);

$result = $conn->query("SELECT * FROM tblprogram ORDER BY program_id ASC");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['program_code'], $row['program_name'], $row['dept_id']]);
}
fclose($output);
exit;
?>