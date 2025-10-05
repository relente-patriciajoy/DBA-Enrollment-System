<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Course Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

// widths: ID, Code, Title, Lec, Lab, Units, Dept
$w = [15, 35, 130, 25, 25, 20, 25];

$pdf->Cell($w[0],8,'ID',1,0,'C',true);
$pdf->Cell($w[1],8,'Code',1,0,'C',true);
$pdf->Cell($w[2],8,'Title',1,0,'C',true);
$pdf->Cell($w[3],8,'Lecture Hrs',1,0,'C',true);
$pdf->Cell($w[4],8,'Lab Hrs',1,0,'C',true);
$pdf->Cell($w[5],8,'Units',1,0,'C',true);
$pdf->Cell($w[6],8,'Dept ID',1,1,'C',true);

$pdf->SetFont('Arial','',10);

$res = $conn->query("SELECT * FROM tblcourse ORDER BY course_id ASC");
while ($row = $res->fetch_assoc()) {
    // convert title encoding for FPDF
    $title = iconv('UTF-8', 'windows-1252', $row['course_title']);

    $pdf->Cell($w[0],7,$row['course_id'],1,0,'C');
    $pdf->Cell($w[1],7,$row['course_code'],1);
    $pdf->Cell($w[2],7,$title,1);
    $pdf->Cell($w[3],7,$row['lecture_hours'],1,0,'C');
    $pdf->Cell($w[4],7,$row['lab_hours'],1,0,'C');
    $pdf->Cell($w[5],7,$row['units'],1,0,'C');
    $pdf->Cell($w[6],7,$row['dept_id'],1,1,'C');
}

$pdf->Output('I','courses.pdf');
exit;