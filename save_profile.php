<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$conn = get_db_connection();
$studentId = $_SESSION['student_id'] ?? 0;

if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Fetch current student data for file fallback
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit;
}

// ✅ Internship fields removed from update list
$fields = [
    'first_name','last_name','username','email','mobile','whatsapp',
    'employment_status','extra_skills','aadhar_number',
    'linkedin_id','instagram_id'
];

$updates = [];
$values = [];

foreach ($fields as $f) {
    $updates[] = "$f=?";
    $values[] = $_POST[$f] ?? '';
}

// CV Upload
$cvFile = $student['cv'] ?? '';
if (!empty($_FILES['cv']['name'])) {
    $targetDir = "uploads/cv/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $fileName = time() . "_" . basename($_FILES['cv']['name']);
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES['cv']['tmp_name'], $targetFile)) {
        $cvFile = $targetFile;
    }
}

// Profile Pic Upload
$picFile = $student['profile_pic'] ?? '';
if (!empty($_FILES['profile_pic']['name'])) {
    $targetDir = "uploads/profile_pics/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
        $picFile = $targetFile;
    }
}

$updates[] = "cv=?";
$values[] = $cvFile;
$updates[] = "profile_pic=?";
$values[] = $picFile;

$values[] = $studentId;

try {
    $sql = "UPDATE students SET " . implode(",", $updates) . " WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($values);

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>