<?php
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

class PDF extends FPDF {
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' of {nb}', 0, 0, 'C');
    }
}

$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

$dateGenerated = date("F d, Y");
$filename = "sections_" . date("F-d-Y") . ".pdf";

// Report Title
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Section Report',0,1,'C');
$pdf->Ln(4);

$w = [25, 40, 40, 40, 40, 40];
$headers = ['Section ID', 'Section Name', 'Course ID', 'Instructor ID', 'Room ID', 'Term ID'];

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
foreach ($headers as $i => $header) {
    $pdf->Cell($w[$i],8,$header,1,0,'C',true);
}
$pdf->Ln();

$sql = "SELECT * FROM tblsection WHERE is_deleted = 0 ORDER BY section_id ASC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $pdf->Cell($w[0],7,$row['section_id'],1);
    $pdf->Cell($w[1],7,$row['section_name'],1);
    $pdf->Cell($w[2],7,$row['course_id'],1);
    $pdf->Cell($w[3],7,$row['instructor_id'],1);
    $pdf->Cell($w[4],7,$row['room_id'],1);
    $pdf->Cell($w[5],7,$row['term_id'],1,1);
}

$pdf->Output('D', $filename);
exit;