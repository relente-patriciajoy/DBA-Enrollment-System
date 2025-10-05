<?php
// export_pdf.php using FPDF
require_once __DIR__ . '/../../libraries/fpdf/fpdf.php';
require __DIR__ . '/../../config/database.php';

$pdf = new FPDF('L','mm','A4'); // landscape
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,'Student List',0,1,'C');
$pdf->Ln(4);

// table header
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(220,220,220);

// Column widths (adjust as needed)
$w = [
    'student_no' => 40,
    'last_name'  => 35,
    'first_name' => 35,
    'email'      => 80,
    'gender'     => 18,
    'birthdate'  => 25,
    'year_level' => 25,
    'program_id' => 20
];

// header
$pdf->Cell($w['student_no'],7,'Student No',1,0,'C',1);
$pdf->Cell($w['last_name'],7,'Last Name',1,0,'C',1);
$pdf->Cell($w['first_name'],7,'First Name',1,0,'C',1);
$pdf->Cell($w['email'],7,'Email',1,0,'C',1);
$pdf->Cell($w['gender'],7,'Gender',1,0,'C',1);
$pdf->Cell($w['birthdate'],7,'Birthdate',1,0,'C',1);
$pdf->Cell($w['year_level'],7,'Year Level',1,0,'C',1);
$pdf->Cell($w['program_id'],7,'Program ID',1,0,'C',1);
$pdf->Ln();

// rows
$pdf->SetFont('Arial','',9);
$sql = "SELECT student_no, last_name, first_name, email, gender, birthdate, year_level, program_id
        FROM tblstudent ORDER BY student_id ASC";
$res = $conn->query($sql);

while ($row = $res->fetch_assoc()) {
    $pdf->Cell($w['student_no'],6, $row['student_no'],1);
    $pdf->Cell($w['last_name'],6, $row['last_name'],1);
    $pdf->Cell($w['first_name'],6, $row['first_name'],1);
    // MultiCell for long email: workaround by truncating to fit width
    $email = $row['email'];
    if (strlen($email) > 40) $email = substr($email,0,40).'...';
    $pdf->Cell($w['email'],6, $email,1);
    $pdf->Cell($w['gender'],6, $row['gender'],1,0,'C');
    $pdf->Cell($w['birthdate'],6, $row['birthdate'] ? $row['birthdate'] : '',1,0,'C');
    $pdf->Cell($w['year_level'],6, $row['year_level'],1,0,'C');
    $pdf->Cell($w['program_id'],6, $row['program_id'],1,0,'C');
    $pdf->Ln();
}

$pdf->Output('I', 'students.pdf');
exit;
