<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF('L','mm','A4'); // Landscape for more space
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Enrollment Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(200,200,200);

// Headers
$pdf->Cell(20,8,'ID',1,0,'C',true);
$pdf->Cell(25,8,'Student ID',1,0,'C',true);
$pdf->Cell(25,8,'Section ID',1,0,'C',true);
$pdf->Cell(35,8,'Date Enrolled',1,0,'C',true);
$pdf->Cell(40,8,'Status',1,0,'C',true);
$pdf->Cell(30,8,'Letter Grade',1,1,'C',true);

$pdf->SetFont('Arial','',9);

// Data rows
$res = $conn->query("SELECT * FROM tblenrollment ORDER BY enrollment_id ASC");
while ($row = $res->fetch_assoc()) {
    $pdf->Cell(20,7,$row['enrollment_id'],1,0,'C');
    $pdf->Cell(25,7,$row['student_id'],1);
    $pdf->Cell(25,7,$row['section_id'],1);
    $pdf->Cell(35,7,$row['date_enrolled'],1);
    $pdf->Cell(40,7,$row['status'],1);
    $pdf->Cell(30,7,$row['letter_grade'],1,1);
}

$pdf->Output('I','enrollments.pdf');
exit;