<?php
session_start();
require_once 'db.php';

// Check eligibility
if (!isset($_SESSION['eligible']) || $_SESSION['eligible'] !== true) {
    die("You are not eligible to download LOI. Please pass the exam first.");
}

$studentId = $_SESSION['student_id'] ?? 0;

$conn = get_db_connection();
$stmt = $conn->prepare("SELECT first_name, last_name, internship_category, internship_start_date, internship_tenure 
                        FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

$studentName = $student['first_name'] . " " . $student['last_name'];
$internshipCategory = $student['internship_category'];
$internshipStart = $student['internship_start_date'];
$internshipTenure = $student['internship_tenure'];
$currentDate = date("Y-m-d");

// Include FPDF
require('fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();

// Page 1 background
$pdf->Image('uploads/loi1.jpg', 0, 0, 210, 297); // A4 size background
$pdf->SetFont('Arial','',13);
$pdf->SetTextColor(0,0,0);

// Overwrite details
$pdf->SetXY(35, 41.5);
$pdf->Cell(0,10," $currentDate",0,1);

$pdf->SetXY(43, 60);
$pdf->Cell(0,10," $internshipCategory ",0,1);

$pdf->SetXY(23, 51);
$pdf->SetFont('Arial','B',13); // 'B' = Bold, 12 = font size
$pdf->Cell(0,10," $studentName,",0,1);// Student name belew date and above internship category

$pdf->SetXY(45, 101.6);
$pdf->Cell(0,10," $internshipStart",0,1);

$pdf->SetXY(98, 101.3);
$pdf->SetFont('Arial','B',13); // 'B' = Bold, 12 = font size
$pdf->Cell(0,10," $internshipTenure",0,1);

$pdf->SetXY(35, 79);
$pdf->SetFont('Arial','B',13); // 'B' = Bold, 12 = font size
$pdf->Cell(0,10,"$studentName,",0,1); //with dear 

$pdf->SetXY(137, 88.2);
$pdf->SetFont('Arial','B',13); // 'B' = Bold, 12 = font size
$pdf->Cell(0,10," $internshipCategory ",0,1); // with  in the paragraph near intern 


// // Signature
// $pdf->SetXY(30, 230);
// $pdf->Cell(0,10,"Program Manager",0,1);
// $pdf->Cell(0,10,"Internboot",0,1);

// Page 2 background
$pdf->AddPage();
$pdf->Image('uploads/loi2.jpg', 0, 0, 210, 297);

// // (Optional) overwrite same details on page 2 if needed
// $pdf->SetFont('Arial','',12);
// $pdf->SetTextColor(0,0,0);
// $pdf->SetXY(30, 40);
// $pdf->Cell(0,10," $currentDate",0,1);
// $pdf->SetXY(30, 55);
// $pdf->Cell(0,10,"$internshipCategory Internship",0,1);
// $pdf->SetXY(30, 70);
// $pdf->Cell(0,10," $studentName,",0,1);

// Output PDF
$pdf->Output('D','LOI.pdf'); // Force download
?>
