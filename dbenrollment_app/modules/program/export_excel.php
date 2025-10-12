<?php
require_once __DIR__ . '/../../config/database.php';

$dateGenerated = date("F-d-Y");
$filename = "programs_" . $dateGenerated . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

fputcsv($output, ['Polytechnic University of the Philippines']);
fputcsv($output, ['Generated on: ' . date("F d, Y")]);
fputcsv($output, []);
fputcsv($output, ['Program Code','Program Name','Department']);

$sql = "SELECT p.program_code, p.program_name, d.dept_name FROM tblprogram p LEFT JOIN tbldepartment d ON p.dept_id=d.dept_id WHERE p.is_deleted=0 ORDER BY p.program_name ASC";
$res = $conn->query($sql);
if ($res->num_rows>0) {
  while($r=$res->fetch_assoc()){
    fputcsv($output, [$r['program_code'],$r['program_name'],$r['dept_name']]);
  }
} else {
  fputcsv($output, ['No records found']);
}
fclose($output);
exit;