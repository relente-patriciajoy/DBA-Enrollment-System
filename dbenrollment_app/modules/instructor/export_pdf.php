<?php
session_start();
include('../includes/auth_check.php');
include('../includes/role_check.php');
requireRole('admin');

// modules/instructor/export_pdf.php (FPDF)
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
$filename = "instructors_" . date("F-d-Y") . ".pdf";

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
$pdf->Cell(0,8,'Instructor Report',0,1,'C');
$pdf->Ln(4);

$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

$w = [15, 40, 40, 100, 20];
$headers = ['ID', 'Last Name', 'First Name', 'Email', 'Dept ID'];

foreach ($headers as $i => $header) {
    $pdf->Cell($w[$i],8,$header,1,0,'C',true);
}
$pdf->Ln();

$pdf->SetFont('Arial','',10);

$sql = "SELECT * FROM tblinstructor WHERE is_deleted = 0 ORDER BY last_name ASC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $last = (strlen($row['last_name'])>30) ? substr($row['last_name'],0,27).'...' : $row['last_name'];
    $first = (strlen($row['first_name'])>30) ? substr($row['first_name'],0,27).'...' : $row['first_name'];
    $email = (strlen($row['email'])>45) ? substr($row['email'],0,42).'...' : $row['email'];
    
    $pdf->Cell($w[0],7,$row['instructor_id'],1);
    $pdf->Cell($w[1],7,$last,1);
    $pdf->Cell($w[2],7,$first,1);
    $pdf->Cell($w[3],7,$email,1);
    $pdf->Cell($w[4],7,$row['dept_id'],1,1);
}

$pdf->Output('D', $filename);
exit;