<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Terms Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

$pdf->Cell(20,8,'ID',1,0,'C',true);
$pdf->Cell(50,8,'Term Code',1,0,'C',true);
$pdf->Cell(40,8,'Start Date',1,0,'C',true);
$pdf->Cell(40,8,'End Date',1,1,'C',true);

$pdf->SetFont('Arial','',10);

$res = $conn->query("SELECT * FROM tblterm ORDER BY term_id ASC");
while ($row = $res->fetch_assoc()) {
    $pdf->Cell(20,7,$row['term_id'],1,0,'C');
    $pdf->Cell(50,7,$row['term_code'],1);
    $pdf->Cell(40,7,$row['start_date'],1);
    $pdf->Cell(40,7,$row['end_date'],1,1);
}

$pdf->Output('I','terms.pdf');
exit;