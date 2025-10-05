<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Department Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

// column widths
$w = [20, 60, 120]; // ID, Code, Name

$pdf->Cell($w[0],8,'ID',1,0,'C',true);
$pdf->Cell($w[1],8,'Dept Code',1,0,'C',true);
$pdf->Cell($w[2],8,'Dept Name',1,1,'C',true);

$pdf->SetFont('Arial','',10);

$sql = "SELECT * FROM tbldepartment ORDER BY dept_id ASC";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $pdf->Cell($w[0],7,$row['dept_id'],1,0,'C');
    $pdf->Cell($w[1],7,$row['dept_code'],1);
    $pdf->Cell($w[2],7,$row['dept_name'],1,1);
}

$pdf->Output('I','departments.pdf');
exit;
?>
