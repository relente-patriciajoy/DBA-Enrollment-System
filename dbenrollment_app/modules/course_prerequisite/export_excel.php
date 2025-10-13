<?php
include_once '../../config/database.php';

$dateGenerated = date("F d, Y");
$filename = "course_prerequisites_" . date("Y-m-d") . ".xls";

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
echo '<div class="subtitle">Course Prerequisites Report</div>';
echo '</div>';
echo '<br><br>';

// Table
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>Prereq ID</th>';
echo '<th>Course Code</th>';
echo '<th>Course Title</th>';
echo '<th>Prerequisite Course Code</th>';
echo '<th>Prerequisite Course Title</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$sql = "SELECT
            cp.prereq_id,
            c1.course_code as course_code,
            c1.course_title as course_title,
            c2.course_code as prereq_course_code,
            c2.course_title as prereq_course_title
        FROM tblcourse_prerequisite cp
        INNER JOIN tblcourse c1 ON cp.course_id = c1.course_id
        INNER JOIN tblcourse c2 ON cp.prerequisite_course_id = c2.course_id
        WHERE cp.is_deleted = 0
        AND c1.is_deleted = 0
        AND c2.is_deleted = 0
        ORDER BY c1.course_code ASC";
$result = $conn->query($sql);

$totalRecords = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['prereq_id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['course_code']) . '</td>';
        echo '<td>' . htmlspecialchars($row['course_title']) . '</td>';
        echo '<td>' . htmlspecialchars($row['prereq_course_code']) . '</td>';
        echo '<td>' . htmlspecialchars($row['prereq_course_title']) . '</td>';
        echo '</tr>';
        $totalRecords++;
    }
} else {
    echo '<tr>';
    echo '<td colspan="5" style="text-align: center;">No records found</td>';
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