<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $conn = get_db_connection();

        // ✅ Fetch student record
        $stmt = $conn->prepare("SELECT id, password, payment_status FROM students WHERE email=?");
        $stmt->execute([$email]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            // ✅ Password check
            if ($student['password'] === $password) {
                // ✅ Payment status check
                if ($student['payment_status'] === 'success') {
                    // Both conditions true → redirect to dashboard
                    session_start();
                    $_SESSION['student_id'] = $student['id'];
                    header("Location: student_dashboard.php");
                    exit;
                } else {
                    // Payment not complete
                    echo "<h2>Login Failed ❌</h2>";
                    echo "<p>Payment not completed. Please complete payment first.</p>";
                    echo "<p><a href='pay.php'>Click here to Pay Now</a></p>";
                }
            } else {
                echo "<h2>Login Failed ❌</h2>";
                echo "<p>Invalid password.</p>";
            }
        } else {
            echo "<h2>Login Failed ❌</h2>";
            echo "<p>Email not found.</p>";
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>