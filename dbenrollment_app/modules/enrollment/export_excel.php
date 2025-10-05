<?php
include_once '../../config/database.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=enrollments.csv");

$out = fopen("php://output", "w");
fputcsv($out, ["Enrollment ID","Student ID","Section ID","Date Enrolled","Status","Letter Grade"]);

$res = $conn->query("SELECT * FROM tblenrollment ORDER BY enrollment_id ASC");
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [$r['enrollment_id'], $r['student_id'], $r['section_id'], $r['date_enrolled'], $r['status'], $r['letter_grade']]);
}
fclose($out);
exit;