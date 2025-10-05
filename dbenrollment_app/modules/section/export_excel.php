<?php
include_once '../../config/database.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=sections.csv");

$out = fopen("php://output", "w");
fputcsv($out, ["ID","Section Code","Course ID","Term ID","Instructor ID","Day Pattern","Start Time","End Time","Room ID","Max Capacity"]);

$res = $conn->query("SELECT * FROM tblsection ORDER BY section_id ASC");
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [$r['section_id'], $r['section_code'], $r['course_id'], $r['term_id'], $r['instructor_id'], $r['day_pattern'], $r['start_time'], $r['end_time'], $r['room_id'], $r['max_capacity']]);
}
fclose($out);
exit;