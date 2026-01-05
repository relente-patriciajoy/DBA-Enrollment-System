<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

include_once '../../config/database.php';

$dateGenerated = date("F d, Y");
$filename = "enrollments_" . date("Y-m-d") . ".xls";

// Headers for Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

// Start output
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<style>';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'th, td { border: 1px solid black; padding: 8px; text-align: left; }';
echo 'th { background-color: #f2f2f2; font-weight: bold; }';
echo '.header { text-align: center; font-weight: bold; margin-bottom: 20px; }';
echo '.title { font-size: 18px; font-weight: bold; }';
echo '.subtitle { font-size: 14px; }';
echo '.date { font-size: 12px; margin-bottom: 10px; }';
echo '</style>';
echo '</head>';
echo '<body>';

// University Header
echo '<div class="header">';
echo '<div class="title">Polytechnic University of the Philippines</div>';
echo '<div class="date">Generated on: ' . $dateGenerated . '</div>';
echo '<div class="subtitle">Enrollment Report</div>';
echo '</div>';
echo '<br><br>';

// Table
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>Enrollment ID</th>';
echo '<th>Student ID</th>';
echo '<th>Section ID</th>';
echo '<th>Date Enrolled</th>';
echo '<th>Status</th>';
echo '<th>Letter Grade</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$sql = "SELECT enrollment_id, student_id, section_id, date_enrolled, status, letter_grade
        FROM tblenrollment
        WHERE is_deleted = 0
        ORDER BY date_enrolled DESC";
$result = $conn->query($sql);

$totalRecords = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['enrollment_id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['student_id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['section_id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['date_enrolled']) . '</td>';
        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
        echo '<td>' . htmlspecialchars($row['letter_grade']) . '</td>';
        echo '</tr>';
        $totalRecords++;
    }
} else {
    echo '<tr>';
    echo '<td colspan="6" style="text-align: center;">No records found</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

// Footer with record count
echo '<br><br>';
echo '<div style="text-align: center; font-size: 10px; color: gray;">';
echo 'Total Records: ' . $totalRecords . ' | Generated on: ' . $dateGenerated;
echo '</div>';

echo '</body>';
echo '</html>';

$conn->close();
exit;
?>