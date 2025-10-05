<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Rooms Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

$pdf->Cell(20,8,'ID',1,0,'C',true);
$pdf->Cell(40,8,'Room Code',1,0,'C',true);
$pdf->Cell(40,8,'Building',1,0,'C',true);
$pdf->Cell(30,8,'Capacity',1,1,'C',true);

$pdf->SetFont('Arial','',10);

$res = $conn->query("SELECT * FROM tblroom ORDER BY room_id ASC");
while ($row = $res->fetch_assoc()) {
    $pdf->Cell(20,7,$row['room_id'],1,0,'C');
    $pdf->Cell(40,7,$row['room_code'],1);
    $pdf->Cell(40,7,$row['building'],1);
    $pdf->Cell(30,7,$row['capacity'],1,1);
}

$pdf->Output('I','rooms.pdf');
exit;