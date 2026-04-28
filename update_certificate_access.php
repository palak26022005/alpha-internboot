<?php
session_start();
require_once 'db.php';
$conn = get_db_connection();

if (!isset($_SESSION['trainer_id'])) {
    header("Location: trainer_login.php");
    exit;
}

$student_id = $_POST['student_id'];
$batch_id   = $_POST['batch_id'];
$access     = isset($_POST['certificate_access']) ? 1 : 0;

$stmt = $conn->prepare("UPDATE students SET certificate_access=? WHERE id=?");
$stmt->execute([$access, $student_id]);

header("Location: view_students.php?batch_id=" . $batch_id);
exit;
?>
