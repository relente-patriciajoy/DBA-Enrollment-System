<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Term Report',0,1,'C');
$pdf->Ln(4);

$w = [30, 50, 50, 50]; 
$headers = ['Term ID', 'Term Name', 'Start Date', 'End Date'];

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
foreach ($headers as $i => $header) {
    $pdf->Cell($w[$i],8,$header,1,0,'C',true);
}
$pdf->Ln();

$sql = "SELECT * FROM tblterm WHERE is_deleted = 0 ORDER BY term_id ASC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $pdf->Cell($w[0],7,$row['term_id'],1);
    $pdf->Cell($w[1],7,$row['term_name'],1);
    $pdf->Cell($w[2],7,$row['start_date'],1);
    $pdf->Cell($w[3],7,$row['end_date'],1,1);
}

$pdf->Output('D', 'terms_' . date('F-d-Y') . '.pdf');
exit;
?>