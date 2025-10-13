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
$filename = "rooms_" . date("F-d-Y") . ".pdf";

$pdf = new PDF('P','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// Add logo
$logoPath = __DIR__ . '/../../assets/images/pup_logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 92, 10, 25);
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
$pdf->Cell(0,8,'Room Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

// Column widths
$w = [25, 60, 65, 40];
$headers = ['Room ID', 'Room Code', 'Building', 'Capacity'];

foreach ($headers as $i => $header) {
    $pdf->Cell($w[$i],8,$header,1,0,'C',true);
}
$pdf->Ln();

$pdf->SetFont('Arial','',9);

$sql = "SELECT room_id, room_code, building, capacity
        FROM tblroom
        WHERE is_deleted = 0
        ORDER BY room_code ASC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $pdf->Cell($w[0],7,$row['room_id'],1,0,'C');
    $pdf->Cell($w[1],7,$row['room_code'],1);
    $pdf->Cell($w[2],7,$row['building'],1);
    $pdf->Cell($w[3],7,$row['capacity'],1,0,'C');
    $pdf->Ln();
}

$pdf->Output('D', 'rooms_'.date('Y-m-d').'.pdf');
exit;
?>