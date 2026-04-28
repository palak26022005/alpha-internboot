<?php
require_once 'db.php';

$conn = get_db_connection();

// PayU response values
$status   = $_POST["status"];
$email    = $_POST["email"];

// ✅ Update payment_status as failed
$sql = "UPDATE students SET payment_status='failed' WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->execute([$email]);

echo "<h2>Payment Failed ❌</h2>";
echo "<p>Your payment was not successful. Please try again.</p>";
echo "<p><a href='pay.php'>Retry Payment</a></p>";
?>