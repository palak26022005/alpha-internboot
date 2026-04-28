<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';
require_once __DIR__ . '/fpdf.php';

$conn = get_db_connection();

// ✅ Security check
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit;
}

$student_id = $_SESSION['student_id'];

// ✅ Fetch student record
$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student record not found.");
}

// ✅ Certificate details
$name   = $student['first_name'] . " " . $student['last_name'];
$domain = $student['internship_category'];

// ✅ Tenure formatting
$rawTenure = strtolower(trim($student['internship_tenure'])); 
if (strpos($rawTenure, 'day') !== false || is_numeric($rawTenure)) {
    $tenure = preg_replace('/[^0-9]/', '', $rawTenure) . " days";
} else {
    $tenure = ucfirst($rawTenure); 
}

$date   = !empty($student['certificate_date']) ? $student['certificate_date'] : date("Y-m-d");

// ✅ If certificate_date not set, update it once
if (empty($student['certificate_date'])) {
    $stmt = $conn->prepare("UPDATE students SET certificate_date=? WHERE id=?");
    $stmt->execute([$date, $student_id]);
}

// ✅ Certificate ID logic
if (empty($student['certificate_id'])) {
    $stmtMax = $conn->query("SELECT certificate_id FROM students WHERE certificate_id IS NOT NULL ORDER BY certificate_id DESC LIMIT 1");
    $last = $stmtMax->fetch(PDO::FETCH_ASSOC);

    $year = date("Y");
    $nextNumber = 1;

    if ($last) {
        preg_match('/CERTIINT'.$year.'(\d+)/', $last['certificate_id'], $matches);
        if (!empty($matches[1])) {
            $nextNumber = intval($matches[1]) + 1;
        }
    }

    $certificate_id = "CERTIINT" . $year . str_pad($nextNumber, 3, "0", STR_PAD_LEFT);

    $stmt = $conn->prepare("UPDATE students SET certificate_id=? WHERE id=?");
    $stmt->execute([$certificate_id, $student_id]);
} else {
    $certificate_id = $student['certificate_id'];
}

// ✅ Generate PDF in Landscape mode
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape orientation
$pdf->AddPage();

$certificatePath = __DIR__ . "/uploads/certificate.jpg";

// Fit full landscape page (297x210 mm)
$pdf->Image($certificatePath, 0, 0, 297, 210);

// ✅ Fonts and overlay text
$pdf->SetFont('Arial', 'B', 14);

$pdf->SetXY(100, 90);
$pdf->SetFont('Arial', 'B', 18); 
$pdf->Cell(0, 10, "$name", 0, 1);

$pdf->SetXY(124.5, 122);
$pdf->Cell(0, 10, "$domain", 0, 1);

$pdf->SetXY(121, 133.4);
$pdf->SetFont('Arial', 'B', 18); 
$pdf->Cell(0, 10, "$tenure", 0, 1);

$pdf->SetXY(27, 52);
$pdf->SetFont('Arial', 'B', 18); 
$pdf->SetTextColor(255, 255, 255); // ✅ White color (R,G,B)
$pdf->Cell(0, 10, "$date", 0, 1);


$pdf->SetXY(109, 177.1);
$pdf->SetFont('Arial', 'B', 18); 
$pdf->SetTextColor(0, 0, 0); // Black
$pdf->Cell(0, 10, "$certificate_id", 0, 1);

// ✅ Output PDF
$pdf->Output('D', 'certificate.pdf');
?>
