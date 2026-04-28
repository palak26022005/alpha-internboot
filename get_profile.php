<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

$studentId = $_SESSION['student_id'] ?? 0;

$conn = get_db_connection();
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($student);
?>
