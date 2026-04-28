<?php
session_start();
require_once 'db.php';
$conn = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = $_POST['name'];
    $email  = $_POST['email'];
    $domain = $_POST['domain'];
    $tenure = $_POST['tenure'];
    $query  = $_POST['query'];

    $student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null;

    $stmt = $conn->prepare("INSERT INTO help_support (student_id, name, email, domain, tenure, query) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$student_id, $name, $email, $domain, $tenure, $query]);

    $success = "✅ Your query has been submitted successfully. Our team will contact you soon.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Help & Support | Internboot</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #004aad, #00aaff);
      color: #333;
      display: flex;
      min-height: 100vh;
    }
    .container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }
    .form-box {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 500px;
    }
    .form-box h2 {
      margin-bottom: 20px;
      text-align: center;
      color: #004aad;
    }
    input, textarea {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }
    button {
      width: 100%;
      padding: 12px;
      margin-top: 15px;
      border: none;
      border-radius: 8px;
      background: #004aad;
      color: #fff;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover { background: #003080; }
    .success { color: green; text-align: center; margin-bottom: 15px; }
    .contact-box {
      position: fixed;
      right: 20px;
      top: 100px;
      width: 250px;
      background: #fff;
      border: 1px solid #ccc;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .contact-box h3 {
      margin-top: 0;
      color: #004aad;
    }
    .contact-box p {
      margin: 8px 0;
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="form-box">
    <h2>Help & Support</h2>
    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <input type="text" name="domain" placeholder="Internship Domain" required>
      <input type="text" name="tenure" placeholder="Internship Tenure" required>
      <textarea name="query" placeholder="Write your query/issue here..." rows="5" required></textarea>
      <button type="submit">Submit Query</button>
    </form>
  </div>
</div>

<div class="contact-box">
  <h3>📞 Contact Us</h3>
  <p><strong>Email:</strong> support@internboot.com</p>
  <p><strong>Phone:</strong> +91-9876543210</p>
  <p><strong>WhatsApp:</strong> +91-9876543210</p>
  <p style="font-size: 12px; color: #555;">Hello! If you have any query, you can call or WhatsApp us directly.</p>
</div>

</body>
</html>
