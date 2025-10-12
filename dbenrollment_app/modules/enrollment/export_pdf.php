<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF('L','mm','A4'); // Landscape for more space
$pdf->AddPage();
$dateGenerated = date("F d, Y");
$filename = "enrollments_" . date("F-d-Y") . ".pdf";

// Report Title
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Enrollment Report',0,1,'C');
$pdf->Ln(4);

$w = [25, 30, 30, 30, 40, 30]; 
$headers = ['Enroll ID', 'Student ID', 'Section ID', 'Term ID', 'Date Enrolled', 'Status'];

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
foreach ($headers as $i => $header) {
    $pdf->Cell($w[$i],8,$header,1,0,'C',true);
}
$pdf->Ln();

$sql = "SELECT * FROM tblenrollment WHERE is_deleted = 0 ORDER BY enrollment_id ASC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $pdf->Cell($w[0],7,$row['enrollment_id'],1);
    $pdf->Cell($w[1],7,$row['student_id'],1);
    $pdf->Cell($w[2],7,$row['section_id'],1);
    $pdf->Cell($w[3],7,$row['term_id'],1);
    $pdf->Cell($w[4],7,$row['date_enrolled'],1);
    $pdf->Cell($w[5],7,$row['status'],1,1);
}

$pdf->Output('D', $filename);
exit;