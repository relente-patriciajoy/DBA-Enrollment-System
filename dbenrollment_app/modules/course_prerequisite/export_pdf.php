<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Course Prerequisites Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

$pdf->Cell(30,8,'Prereq ID',1,0,'C',true);
$pdf->Cell(40,8,'Course ID',1,0,'C',true);
$pdf->Cell(60,8,'Prerequisite Course ID',1,1,'C',true);

$pdf->SetFont('Arial','',10);

$res = $conn->query("SELECT * FROM tblcourse_prerequisite ORDER BY prereq_id ASC");
while ($row = $res->fetch_assoc()) {
    $pdf->Cell(30,7,$row['prereq_id'],1,0,'C');
    $pdf->Cell(40,7,$row['course_id'],1,0,'C');
    $pdf->Cell(60,7,$row['prerequisite_course_id'],1,1,'C');
}

$pdf->Output('I','course_prereqs.pdf');
exit;