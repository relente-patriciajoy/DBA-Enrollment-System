<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF('L','mm','A4'); // Landscape
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Section Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(200,200,200);

// Headers
$headers = ["ID","Code","Course","Term","Instructor","Day","Start","End","Room","Capacity"];
$widths = [10,25,20,20,25,20,20,20,20,20];

foreach ($headers as $i=>$col) {
    $pdf->Cell($widths[$i],8,$col,1,0,'C',true);
}
$pdf->Ln();

$pdf->SetFont('Arial','',8);

// Rows
$res = $conn->query("SELECT * FROM tblsection ORDER BY section_id ASC");
while ($row = $res->fetch_assoc()) {
    $pdf->Cell($widths[0],7,$row['section_id'],1);
    $pdf->Cell($widths[1],7,$row['section_code'],1);
    $pdf->Cell($widths[2],7,$row['course_id'],1);
    $pdf->Cell($widths[3],7,$row['term_id'],1);
    $pdf->Cell($widths[4],7,$row['instructor_id'],1);
    $pdf->Cell($widths[5],7,$row['day_pattern'],1);
    $pdf->Cell($widths[6],7,$row['start_time'],1);
    $pdf->Cell($widths[7],7,$row['end_time'],1);
    $pdf->Cell($widths[8],7,$row['room_id'],1);
    $pdf->Cell($widths[9],7,$row['max_capacity'],1);
    $pdf->Ln();
}

$pdf->Output('I','sections.pdf');
exit;