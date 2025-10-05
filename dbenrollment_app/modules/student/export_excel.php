<?php
// export_excel.php  (CSV) - no external library required
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students.csv');

require __DIR__ . '/../../config/database.php';

// open output stream
$out = fopen('php://output', 'w');

// header row
fputcsv($out, ['Student No','Last Name','First Name','Email','Gender','Birthdate','Year Level','Program ID']);

// fetch rows
$sql = "SELECT student_no, last_name, first_name, email, gender, birthdate, year_level, program_id
        FROM tblstudent ORDER BY student_id ASC";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // ensure birthdate format consistent (NULL -> empty)
        $birth = $row['birthdate'] ? $row['birthdate'] : '';
        fputcsv($out, [
            $row['student_no'],
            $row['last_name'],
            $row['first_name'],
            $row['email'],
            $row['gender'],
            $birth,
            $row['year_level'],
            $row['program_id']
        ]);
    }
}

fclose($out);
exit;
