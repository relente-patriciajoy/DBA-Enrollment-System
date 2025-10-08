<?php
include_once '../../config/database.php';

header("Content-Type: application/vnd.ms-excel"); // Set the content type to Excel
header("Content-Disposition: attachment; filename=rooms.csv"); // Set the filename for the download

$out = fopen("php://output", "w"); // Open the output stream
fputcsv($out, ["ID","Room Code","Building","Capacity"]);

$res = $conn->query("SELECT * FROM tblroom ORDER BY room_id ASC");
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [$r['room_id'], $r['room_code'], $r['building'], $r['capacity']]);
}
fclose($out);
exit;