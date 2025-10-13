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

$dateGenerated = date("F d, Y");
$filename = "sections_" . date("F-d-Y") . ".pdf";

$pdf = new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// Add logo
$logoPath = __DIR__ . '/../../assets/images/pup_logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 135, 10, 25);
}

$pdf->Ln(25);

// University Name header
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, 'Polytechnic University of the Philippines', 0, 1, 'C');

// Date Generated
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Generated on: ' . $dateGenerated, 0, 1, 'C');
$pdf->Ln(5);

// Report Title
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Section Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(200,200,200);

// Column widths (adjusted for landscape A4 - total ~277mm)
$w = [15, 25, 20, 20, 25, 25, 22, 22, 20, 25];
$headers = ['ID', 'Code', 'Course', 'Term', 'Instructor', 'Day', 'Start', 'End', 'Room', 'Capacity'];

foreach ($headers as $i => $header) {
    $pdf->Cell($w[$i],8,$header,1,0,'C',true);
}
$pdf->Ln();

$pdf->SetFont('Arial','',8);

$sql = "SELECT section_id, section_code, course_id, term_id, instructor_id, day_pattern, start_time, end_time, room_id, max_capacity
        FROM tblsection
        WHERE is_deleted = 0
        ORDER BY section_code ASC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $pdf->Cell($w[0],6,$row['section_id'],1,0,'C');
    $pdf->Cell($w[1],6,$row['section_code'],1);
    $pdf->Cell($w[2],6,$row['course_id'],1,0,'C');
    $pdf->Cell($w[3],6,$row['term_id'],1,0,'C');
    $pdf->Cell($w[4],6,$row['instructor_id'],1,0,'C');
    $pdf->Cell($w[5],6,$row['day_pattern'],1,0,'C');
    $pdf->Cell($w[6],6,$row['start_time'],1,0,'C');
    $pdf->Cell($w[7],6,$row['end_time'],1,0,'C');
    $pdf->Cell($w[8],6,$row['room_id'],1,0,'C');
    $pdf->Cell($w[9],6,$row['max_capacity'],1,0,'C');
    $pdf->Ln();
}

$pdf->Output('D', 'sections_'.date('Y-m-d').'.pdf');
exit;
?>