<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';

// Generate current date
$dateGenerated = date("F d, Y"); // e.g., October 11, 2025

// File name with date
$filename = "students_" . date("F-d-Y") . ".pdf"; // e.g., students_October-11-2025.pdf

$pdf = new FPDF('L', 'mm', 'A4'); // Landscape
$pdf->AddPage();

// Small centered logo
$logoPath = __DIR__ . '/../../assets/images/pup_logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 135, 10, 25); // standard centered small logo size
}

// Spacing under logo
$pdf->Ln(25);

// University Name header
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'Polytechnic University of the Philippines', 0, 1, 'C');

// Date below header
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Generated on: ' . $dateGenerated, 0, 1, 'C');
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 200, 200);

// Updated column widths
$w = [35, 30, 38, 80, 20, 30, 25, 20];
$headers = ['Student No', 'Last Name', 'First Name', 'Email', 'Gender', 'Birthdate', 'Year Level', 'Prog ID'];

foreach ($headers as $i => $col) {
    $pdf->Cell($w[$i], 8, $col, 1, 0, 'C', true);
}
$pdf->Ln();

// Table Data
$pdf->SetFont('Arial', '', 10);
$sql = "SELECT * FROM tblstudent WHERE is_deleted = 0 ORDER BY last_name ASC";
$res = $conn->query($sql);

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $pdf->Cell($w[0], 7, $row['student_no'], 1);
        $pdf->Cell($w[1], 7, $row['last_name'], 1);
        $pdf->Cell($w[2], 7, $row['first_name'], 1);
        $pdf->Cell($w[3], 7, $row['email'], 1);
        $pdf->Cell($w[4], 7, $row['gender'], 1);
        $pdf->Cell($w[5], 7, $row['birthdate'], 1);
        $pdf->Cell($w[6], 7, $row['year_level'], 1);
        $pdf->Cell($w[7], 7, $row['program_id'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 7, 'No data available', 1, 1, 'C');
}

// Output with dynamic filename
$pdf->Output('D', $filename);
exit;
?>
