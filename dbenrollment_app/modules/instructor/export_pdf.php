<?php
// modules/instructor/export_pdf.php (FPDF)
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require_once __DIR__ . '/../../config/database.php';

$pdf = new FPDF('L','mm','A4'); // landscape
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Instructor Report',0,1,'C');
$pdf->Ln(4);

// header style
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);

$w = [15, 40, 40, 100, 20]; // widths: ID, Last Name, First Name, Email, Dept ID (adjust as needed)

$pdf->Cell($w[0],8,'ID',1,0,'C',true);
$pdf->Cell($w[1],8,'Last Name',1,0,'C',true);
$pdf->Cell($w[2],8,'First Name',1,0,'C',true);
$pdf->Cell($w[3],8,'Email',1,0,'C',true);
$pdf->Cell($w[4],8,'Dept ID',1,1,'C',true);

$pdf->SetFont('Arial','',10);

// data rows
$sql = "SELECT instructor_id, last_name, first_name, email, dept_id FROM tblinstructor ORDER BY instructor_id ASC";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    // truncate long email/name to avoid cell overflow
    $last = (strlen($row['last_name'])>30) ? substr($row['last_name'],0,27).'...' : $row['last_name'];
    $first = (strlen($row['first_name'])>30) ? substr($row['first_name'],0,27).'...' : $row['first_name'];
    $email = (strlen($row['email'])>45) ? substr($row['email'],0,42).'...' : $row['email'];

    $pdf->Cell($w[0],7,$row['instructor_id'],1);
    $pdf->Cell($w[1],7,$last,1);
    $pdf->Cell($w[2],7,$first,1);
    $pdf->Cell($w[3],7,$email,1);
    $pdf->Cell($w[4],7,$row['dept_id'],1,1,'C');

}

$pdf->Output('I','instructors.pdf');
exit;