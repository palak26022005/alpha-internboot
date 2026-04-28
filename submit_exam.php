<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$answers = $data['answers'];

$conn = get_db_connection();
$score = 0;

foreach ($answers as $qid => $ans) {
    $stmt = $conn->prepare("SELECT correct_option FROM exam_questions WHERE id = ?");
    $stmt->execute([$qid]);
    $correct = $stmt->fetchColumn();
    if ($correct === $ans) {
        $score++;
    }
}

// Save exam attempt
$stmt = $conn->prepare("INSERT INTO exam_results (student_id, score, attempt_date) VALUES (?, ?, NOW())");
$stmt->execute([$_SESSION['student_id'], $score]);

$_SESSION['eligible'] = ($score >= 20);

$response = ["score" => $score, "eligible" => $score >= 20];
echo json_encode($response);
?>
