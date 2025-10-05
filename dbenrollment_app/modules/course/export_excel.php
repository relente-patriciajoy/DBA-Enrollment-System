<?php
include_once '../../config/database.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=courses.csv");

$out = fopen("php://output", "w");
fputcsv($out, ["Course ID","Course Code","Course Title","Lecture Hours","Lab Hours","Units","Dept ID"]);

$res = $conn->query("SELECT * FROM tblcourse ORDER BY course_id ASC");
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [
        $r['course_id'],
        $r['course_code'],
        $r['course_title'],
        $r['lecture_hours'],
        $r['lab_hours'],
        $r['units'],
        $r['dept_id']
    ]);
}
fclose($out);
exit;