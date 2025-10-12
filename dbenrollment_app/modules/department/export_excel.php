<?php
include_once '../../config/database.php';

$dateGenerated = date("F d, Y");
$filename = "departments_" . date("Y-m-d") . ".xls";

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
echo '<div class="subtitle">Department Report</div>';
echo '</div>';
echo '<br><br>';

// Table
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>Dept ID</th>';
echo '<th>Dept Code</th>';
echo '<th>Department Name</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$sql = "SELECT dept_id, dept_code, dept_name FROM tbldepartment WHERE is_deleted = 0 ORDER BY dept_name ASC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($row['dept_id']) . '</td>';
    echo '<td>' . htmlspecialchars($row['dept_code']) . '</td>';
    echo '<td>' . htmlspecialchars($row['dept_name']) . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

// Footer with page info
echo '<br><br>';
echo '<div style="text-align: center; font-size: 10px; color: gray;">';
echo 'Total Records: ' . $result->num_rows;
echo '</div>';

echo '</body>';
echo '</html>';

$conn->close();
exit;
?>