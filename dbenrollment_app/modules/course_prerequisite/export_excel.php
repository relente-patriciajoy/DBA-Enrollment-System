<?php
include_once '../../config/database.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=course_prerequisites.csv");

$out = fopen("php://output", "w");
fputcsv($out, ["Prereq ID","Course ID","Prerequisite Course ID"]);

$res = $conn->query("SELECT * FROM tblcourse_prerequisite ORDER BY prereq_id ASC");
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [$r['prereq_id'], $r['course_id'], $r['prerequisite_course_id']]);
}
fclose($out);
exit;