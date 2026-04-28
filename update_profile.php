<?php
session_start();
require_once 'db.php';

$conn = get_db_connection();
$studentId = $_SESSION['student_id'] ?? 0;

$data = json_decode(file_get_contents("php://input"), true);

if ($studentId && $data) {
    $sql = "UPDATE students SET 
        first_name=?, last_name=?, email=?, mobile=?, dob=?, gender=?, 
        address=?, city=?, state=? 
        WHERE id=?";
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['phone'],
        $data['dob'],
        $data['gender'],
        $data['address'],
        $data['city'],
        $data['state'],
        $studentId
    ]);

    echo json_encode(["success" => $success]);
} else {
    echo json_encode(["success" => false]);
}
?>
