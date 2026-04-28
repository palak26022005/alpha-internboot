<?php
require_once 'db.php';
header('Content-Type: application/json');

$conn = get_db_connection();
$stmt = $conn->query("SELECT id, question, option_a, option_b, option_c, option_d 
                      FROM exam_questions ORDER BY RAND() LIMIT 25");
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($questions);
?>
