<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF('L','mm','A4'); // landscape
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Program Report',0,1,'C');
$pdf->Ln(4);

// header style
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

$w = [50, 120, 40]; // widths for Program Code, Program Name, Dept ID

$pdf->Cell($w[0],8,'Program Code',1,0,'C',true);
$pdf->Cell($w[1],8,'Program Name',1,0,'C',true);
$pdf->Cell($w[2],8,'Department ID',1,1,'C',true);

// data rows
$pdf->SetFont('Arial','',10);
$sql = "SELECT program_code, program_name, dept_id FROM tblprogram ORDER BY program_id ASC";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    // simple truncation to keep row widths
    $progName = $row['program_name'];
    if (strlen($progName) > 60) $progName = substr($progName,0,57).'...';

    $pdf->Cell($w[0],7,$row['program_code'],1);
    $pdf->Cell($w[1],7,$progName,1);
    $pdf->Cell($w[2],7,$row['dept_id'],1,1,'C');
}

$pdf->Output('I', 'programs.pdf');
exit;