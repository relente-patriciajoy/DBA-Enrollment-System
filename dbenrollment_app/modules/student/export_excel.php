<?php
require_once __DIR__ . '/../../config/database.php';

// File name with real-time date (Format A: October-11-Y)
$dateGenerated = date("F-d-Y");
$filename = "students_" . $dateGenerated . ".csv"; // students_October-11-2025.csv

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Open output stream
$output = fopen('php://output', 'w');

// ✅ Header Section (Template-like)
fputcsv($output, ['Polytechnic University of the Philippines']);
fputcsv($output, ['Generated on: ' . date("F d, Y")]); // no time
fputcsv($output, []); // Empty row

// Column headers
fputcsv($output, [
    'Student No',
    'Last Name',
    'First Name',
    'Email',
    'Gender',
    'Birthdate',
    'Year Level',
    'Program ID'
]);

// Fetch student records
$sql = "SELECT * FROM tblstudent WHERE is_deleted = 0 ORDER BY last_name ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['student_no'],
            $row['last_name'],
            $row['first_name'],
            $row['email'],
            $row['gender'],
            $row['birthdate'],
            $row['year_level'],
            $row['program_id']
        ]);
    }
} else {
    fputcsv($output, ['No records found']);
}

// Close output
fclose($output);
exit;
?>
