<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = get_db_connection();
    // ✅ batch_id bhi select karo
    $stmt = $conn->prepare("SELECT id, first_name, last_name, password, batch_id FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student && $password === $student['password']) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['first_name'] . " " . $student['last_name'];
        $_SESSION['batch_id'] = $student['batch_id']; // ✅ batch_id session ch set karo

        header("Location: student_dashboard.php");
        exit;
    } else {
        echo "Invalid login credentials!";
    }
}
?>
