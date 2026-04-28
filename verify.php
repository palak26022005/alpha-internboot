<?php
require_once 'db.php'; // apna DB connection file include karo
$conn = get_db_connection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Internboot | Certificate Verification</title>
    <link rel="icon" href="https://internboot.com/img/logos/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #004aad, #00aaff);
      color: #fff;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    header { text-align: center; padding: 20px; }
    header h1 { font-size: 1.8rem; font-weight: 700; margin: 5px 0; }
    .container {
      background: rgba(255,255,255,0.1);
      margin: 20px auto;
      padding: 30px 25px;
      border-radius: 12px;
      max-width: 420px;
      width: 90%;
      text-align: center;
      backdrop-filter: blur(5px);
    }
    input, button {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border-radius: 8px;
      border: none;
      outline: none;
      font-size: 1rem;
    }
    button {
      background: #00c2ff;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
    }
    button:hover { background: #0099cc; }
    .result {
      margin-top: 25px;
      padding: 15px;
      border-radius: 8px;
      background: rgba(255,255,255,0.15);
      text-align: left;
    }
    .error { color: #ff6666; font-weight: bold; text-align: center; }
    footer { text-align: center; padding: 15px; font-size: 0.9rem; margin-top: auto; }
  </style>
</head>
<body>
<header>
  <h1>Internboot Verification Certificate</h1>
  <p>Launch Your Career with the Perfect Internship</p>
</header>

<div class="container">
  <h2>Certificate Verification</h2>
  <form method="GET">
    <input type="text" name="id" placeholder="Enter Certificate ID" required />
    <button type="submit">Verify</button>
  </form>

  <div class="result">
    <?php
    if (isset($_GET['id'])) {
        $id = trim($_GET['id']);
        $stmt = $conn->prepare("SELECT first_name, last_name, internship_category, internship_tenure, certificate_date 
                                FROM students WHERE certificate_id=?");
        $stmt->execute([$id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            echo "<h3>✅ Internboot Verified Candidate</h3>";
            echo "<p><strong>Name:</strong> " . htmlspecialchars($student['first_name'] . " " . $student['last_name']) . "</p>";
            echo "<p><strong>Domain:</strong> " . htmlspecialchars($student['internship_category']) . "</p>";
            echo "<p><strong>Tenure:</strong> " . htmlspecialchars($student['internship_tenure']) . "</p>";
            echo "<p><strong>Issue Date:</strong> " . htmlspecialchars($student['certificate_date']) . "</p>";
        } else {
            echo "<p class='error'>❌ Certificate ID \"$id\" not found.</p>";
        }
    } else {
        echo "Enter a Certificate ID above to verify.";
    }
    ?>
  </div>
</div>

<footer>© Internboot @2026</footer>
</body>
</html>
