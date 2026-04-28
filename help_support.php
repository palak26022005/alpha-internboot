<?php
session_start();
require_once 'db.php';
$conn = get_db_connection();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$studentId = $_SESSION['student_id'];

// Student info fetch
$stmt = $conn->prepare("SELECT first_name, last_name, email, internship_category AS domain, internship_tenure AS tenure 
                        FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $query = $_POST['query'] ?? '';

    if ($query) {
        $stmt = $conn->prepare("INSERT INTO help_support (student_id, name, email, domain, tenure, query) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $studentId,
            $student['first_name'].' '.$student['last_name'],
            $student['email'],
            $student['domain'],
            $student['tenure'],
            $query
        ]);
        $message = "✅ Your query has been submitted successfully!";
    } else {
        $message = "⚠️ Please enter your query.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Help & Support</title>

  <!-- Fonts + Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --bg-1:#07122a;
      --bg-2:#0b2a66;
      --card:#ffffff;
      --muted:#6b7280;
      --text:#0f172a;
      --line:#e5e7eb;
      --primary:#103a8a;     /* dark blue */
      --primary-2:#1d4ed8;   /* bright blue */
      --soft:#f4f7ff;
      --success:#16a34a;
      --danger:#ef4444;
      --shadow: 0 18px 50px rgba(2, 8, 23, .12);
      --shadow-2: 0 14px 40px rgba(2, 8, 23, .18);
      --radius: 16px;
    }

    *{ box-sizing:border-box; }
    body{
      margin:0;
      font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color:var(--text);
      background:
        radial-gradient(1200px 600px at 10% 5%, rgba(29,78,216,.18), transparent 55%),
        radial-gradient(900px 500px at 90% 15%, rgba(16,58,138,.20), transparent 50%),
        linear-gradient(135deg, #f7f9ff 0%, #ffffff 55%, #f3f7ff 100%);
      padding: 42px 18px;
    }

    .page{
      max-width: 1120px;
      margin: 0 auto;
    }

    .topbar{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
      margin-bottom: 18px;
    }

    .brand{
      display:flex;
      align-items:center;
      gap:12px;
    }

    .brand .logo{
      width:44px;
      height:44px;
      border-radius: 14px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
      box-shadow: 0 14px 30px rgba(29,78,216,.25);
      display:grid;
      place-items:center;
      color:#fff;
      font-size:18px;
    }

    .brand h1{
      font-size: 18px;
      margin:0;
      line-height:1.15;
      letter-spacing: -0.02em;
    }

    .brand p{
      margin:4px 0 0;
      font-size: 13px;
      color: var(--muted);
    }

    .wrapper{
      display:grid;
      grid-template-columns: 1.8fr 1fr;
      gap: 18px;
      align-items: stretch;
    }

    .card{
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow:hidden;
      background: var(--card);
      border: 1px solid rgba(2, 8, 23, .06);
    }

    /* Left card (form) */
    .form-head{
      padding: 18px 20px;
      background: linear-gradient(135deg, rgba(16,58,138,.06), rgba(29,78,216,.06));
      border-bottom: 1px solid rgba(2, 8, 23, .06);
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    }

    .form-head .title{
      display:flex;
      align-items:center;
      gap:10px;
      font-weight: 800;
      color: #0b1b3a;
      letter-spacing: -0.02em;
    }

    .form-head .title i{
      color: var(--primary-2);
    }

    .chip{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 8px 10px;
      border-radius: 999px;
      background: #ffffff;
      border: 1px solid rgba(2,8,23,.08);
      font-size: 12.5px;
      color: #0b1b3a;
    }

    .chip i{ color: var(--primary-2); }

    .form-body{
      padding: 18px 20px 20px;
    }

    .msg{
      border-radius: 14px;
      padding: 12px 14px;
      margin-bottom: 14px;
      font-weight: 600;
      font-size: 13.5px;
      border: 1px solid transparent;
      background: var(--soft);
    }
    .msg.success{
      color: #0f3d1f;
      border-color: rgba(22,163,74,.25);
      background: rgba(22,163,74,.08);
    }
    .msg.error{
      color: #4a1313;
      border-color: rgba(239,68,68,.25);
      background: rgba(239,68,68,.08);
    }

    .grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    label{
      display:block;
      font-size: 13px;
      font-weight: 700;
      color:#0b1b3a;
      margin: 10px 0 6px;
    }

    .field{
      width:100%;
      padding: 11px 12px;
      border-radius: 12px;
      border: 1px solid rgba(2,8,23,.14);
      background: #fff;
      outline: none;
      font-size: 14px;
      transition: box-shadow .2s, border-color .2s, transform .08s;
    }

    .field:focus{
      border-color: rgba(29,78,216,.65);
      box-shadow: 0 0 0 4px rgba(29,78,216,.14);
    }

    .field[readonly]{
      background: #f8fafc;
      color: #1f2937;
    }

    textarea.field{
      min-height: 130px;
      resize: vertical;
      line-height: 1.4;
    }

    .actions{
      display:flex;
      align-items:center;
      justify-content:flex-start;
      gap: 10px;
      margin-top: 16px;
      flex-wrap: wrap;
    }

    .btn{
      border: 0;
      cursor:pointer;
      border-radius: 12px;
      padding: 11px 14px;
      font-weight: 800;
      letter-spacing: -0.01em;
      font-size: 14px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap: 10px;
      transition: transform .15s ease, box-shadow .2s ease, opacity .2s ease, background .2s;
      user-select:none;
      text-decoration:none;
    }

    .btn:active{ transform: translateY(1px); }

    .btn-primary{
      color:#fff;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
      box-shadow: 0 14px 30px rgba(29,78,216,.25);
    }
    .btn-primary:hover{
      box-shadow: 0 18px 40px rgba(29,78,216,.30);
      transform: translateY(-1px);
    }

    .btn-outline{
      background:#fff;
      color: var(--primary);
      border: 1px solid rgba(16,58,138,.25);
    }
    .btn-outline:hover{
      background: rgba(16,58,138,.06);
      transform: translateY(-1px);
    }

    /* Right card (contact) */
    .support{
      background: linear-gradient(135deg, var(--bg-1) 0%, var(--bg-2) 70%);
      color:#eaf0ff;
      box-shadow: var(--shadow-2);
      border: 1px solid rgba(255,255,255,.10);
      position: relative;
      overflow:hidden;
    }

    .support::before{
      content:"";
      position:absolute;
      inset:-2px;
      background:
        radial-gradient(520px 220px at 20% 10%, rgba(255,255,255,.16), transparent 60%),
        radial-gradient(420px 220px at 85% 35%, rgba(29,78,216,.40), transparent 55%);
      pointer-events:none;
    }

    .support-inner{
      position:relative;
      padding: 20px;
      height: 100%;
      display:flex;
      flex-direction:column;
      justify-content:space-between;
      gap: 18px;
    }

    .support h3{
      margin:0 0 8px;
      font-size: 18px;
      letter-spacing: -0.02em;
    }

    .support p{
      margin:0;
      color: rgba(234,240,255,.86);
      font-size: 13.5px;
      line-height: 1.5;
    }

    .contact-list{
      margin-top: 12px;
      display:flex;
      flex-direction:column;
      gap: 10px;
    }

    .contact-item{
      display:flex;
      gap: 10px;
      align-items:flex-start;
      padding: 12px 12px;
      border-radius: 14px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.10);
      backdrop-filter: blur(8px);
    }

    .contact-item i{
      margin-top: 1px;
      color: #cfe0ff;
      width: 18px;
      text-align:center;
    }

    .contact-item strong{
      display:block;
      color:#ffffff;
      font-size: 13px;
      margin-bottom: 2px;
    }

    .contact-item a{
      color: #eaf0ff;
      text-decoration:none;
      font-weight: 600;
    }
    .contact-item a:hover{ text-decoration: underline; }

    .support-footer{
      font-size: 12.5px;
      color: rgba(234,240,255,.75);
      border-top: 1px solid rgba(255,255,255,.12);
      padding-top: 12px;
    }

    /* Responsive */
    @media (max-width: 920px){
      .wrapper{ grid-template-columns: 1fr; }
      body{ padding: 26px 14px; }
      .grid{ grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
  <div class="page">

    <div class="topbar">
      <div class="brand">
        <div class="logo"><i class="fa-solid fa-headset"></i></div>
        <div>
          <h1>Help & Support</h1>
          <p>Submit your query and our team will get back to you.</p>
        </div>
      </div>

      <span class="chip"><i class="fa-solid fa-shield-heart"></i> Support Desk</span>
    </div>

    <div class="wrapper">

      <!-- Left: Form -->
      <div class="card">
        <div class="form-head">
          <div class="title">
            <i class="fa-solid fa-message"></i>
            <span>Raise a Support Ticket</span>
          </div>
          <div class="chip"><i class="fa-solid fa-clock"></i> Usually replies within 24–48 hrs</div>
        </div>

        <div class="form-body">
          <?php if ($message): ?>
            <div class="msg <?= (strpos($message,'⚠️')!==false) ? 'error' : 'success' ?>">
              <?= htmlspecialchars($message) ?>
            </div>
          <?php endif; ?>

          <form method="POST">
            <div class="grid">
              <div>
                <label>Name</label>
                <input class="field" type="text" value="<?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?>" readonly>
              </div>
              <div>
                <label>Email</label>
                <input class="field" type="email" value="<?= htmlspecialchars($student['email']) ?>" readonly>
              </div>
              <div>
                <label>Domain</label>
                <input class="field" type="text" value="<?= htmlspecialchars($student['domain']) ?>" readonly>
              </div>
              <div>
                <label>Tenure</label>
                <input class="field" type="text" value="<?= htmlspecialchars($student['tenure']) ?>" readonly>
              </div>
            </div>

            <label style="margin-top:14px;">Your Query</label>
            <textarea class="field" name="query" placeholder="Write your issue/question in detail..." required></textarea>

            <div class="actions">
              <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-paper-plane"></i> Submit Query
              </button>

              <button type="button" class="btn btn-outline" onclick="window.location.href='student_dashboard.php'">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Right: Contact Info -->
      <div class="card support">
        <div class="support-inner">
          <div>
            <h3>Need quicker help?</h3>
            <p>Contact us directly via call or email. Please include your name, domain and a brief description of the issue.</p>

            <div class="contact-list">
              <div class="contact-item">
                <i class="fa-solid fa-phone"></i>
                <div>
                  <strong>Contact No</strong>
                  <a href="tel:+919557019604">+91 95570 19604</a>
                </div>
              </div>

              <div class="contact-item">
                <i class="fa-solid fa-envelope"></i>
                <div>
                  <strong>Email</strong>
                  <a href="mailto:info@internboot.com">info@internboot.com</a>
                </div>
              </div>
            </div>
          </div>

          <div class="support-footer">
            Available Mon–Sat • 10:00 AM – 6:00 PM (IST)
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>