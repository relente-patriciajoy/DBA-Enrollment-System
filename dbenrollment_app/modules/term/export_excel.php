<?php
include_once '../../config/database.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=terms.csv");

$out = fopen("php://output", "w");
fputcsv($out, ["ID","Term Code","Start Date","End Date"]);

$res = $conn->query("SELECT * FROM tblterm ORDER BY term_id ASC");
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [$r['term_id'], $r['term_code'], $r['start_date'], $r['end_date']]);
}
fclose($out);
exit;