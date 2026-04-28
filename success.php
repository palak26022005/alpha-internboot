<?php
// Database connection (localhost test DB)
 $host = "localhost";  // Hostinger MySQL host (often 'localhost' or given in panel)
$dbname = "u293157276_alpha";   // full DB name from Hostinger
$username = "u293157276_alpha";  // full MySQL username
$password = "2025#Human";         // password you set

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PayU response values
$status     = $_POST["status"];      // Payment status
$txnid      = $_POST["txnid"];       // Order ID (transaction ID)
$mihpayid   = $_POST["mihpayid"];    // PayU Payment ID
$email      = $_POST["email"];       // Student email (used to identify record)
$amount     = $_POST["amount"];      // Payment amount

// ✅ Check payment status
if ($status == "success") {
    // Update existing student record with payment details
    $sql = "UPDATE students 
            SET txnid=?, mihpayid=?, payment_status='success', amount=? 
            WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $txnid, $mihpayid, $amount, $email);

    if ($stmt->execute()) {
        // Show success message with OK button
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <title>Payment Success</title>
          <link rel='stylesheet' href='success.css'>
        </head>
        <body>
          <div class='success-box'>
            <h2>✅ Payment Successful!</h2>
            <p>Your registration is now confirmed.</p>
            <p><strong>Order ID:</strong> $txnid</p>
            <p><strong>Payment ID:</strong> $mihpayid</p>
            <p>You can now login to your account.</p>
            <form action='student_login.html' method='get'>
              <button type='submit' class='success-btn'>OK</button>
            </form>
          </div>
        </body>
        </html>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    // Payment failed → update status as failed
    $sql = "UPDATE students 
            SET payment_status='failed' 
            WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
      <meta charset='UTF-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1.0'>
      <title>Payment Failed</title>
      <link rel='stylesheet' href='success.css'>
    </head>
    <body>
      <div class='fail-box'>
        <h2>❌ Payment Failed</h2>
        <p>Your payment was not successful. Please try again.</p>
        <form action='pay.php' method='get'>
          <button type='submit' class='fail-btn'>Retry Payment</button>
        </form>
      </div>
    </body>
    </html>";
}

$conn->close();
?>