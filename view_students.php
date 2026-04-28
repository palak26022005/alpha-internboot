<?php
session_start();
require_once 'db.php';
$conn = get_db_connection();

if (!isset($_SESSION['trainer_id'])) {
    header("Location: trainer_login.php");
    exit;
}

if (!isset($_GET['batch_id'])) {
    die("Batch ID not provided.");
}

$batch_id = (int)$_GET['batch_id'];

$batchStmt = $conn->prepare("SELECT b.batch_name, c.name as course_name 
                             FROM batches b 
                             JOIN courses c ON b.course_id = c.id 
                             WHERE b.id = ?");
$batchStmt->execute([$batch_id]);
$batch = $batchStmt->fetch(PDO::FETCH_ASSOC);

if (!$batch) {
    die("Batch not found.");
}

$stmt = $conn->prepare("SELECT id, first_name, last_name, email, mobile, internship_start_date, certificate_access 
                        FROM students 
                        WHERE batch_id = ? 
                        ORDER BY first_name");
$stmt->execute([$batch_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Students – <?= htmlspecialchars($batch['batch_name']) ?></title>
  <link rel="icon" href="https://internboot.com/img/logos/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --sidebar-w: 240px;
      --blue:      #1a56db;
      --blue-dark: #1e429f;
      --blue-mid:  #2563eb;
      --blue-light:#dbeafe;
      --bg:        #f1f5f9;
      --white:     #ffffff;
      --border:    #e2e8f0;
      --text:      #1e293b;
      --muted:     #64748b;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      font-size: 14px;
      min-height: 100vh;
    }

    /* ── Sidebar ── */
    .sidebar {
      position: fixed;
      top: 0; left: 0; bottom: 0;
      width: var(--sidebar-w);
      background: linear-gradient(180deg, #1e3a8a 0%, #1a56db 100%);
      display: flex;
      flex-direction: column;
      z-index: 200;
      overflow-y: auto;
      transition: transform 0.3s ease;
    }

    .sidebar-brand {
      padding: 24px 20px 18px;
      border-bottom: 1px solid rgba(255,255,255,0.12);
    }
    .sidebar-brand .site-label {
      font-size: 10px;
      font-weight: 600;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.55);
      margin-bottom: 3px;
    }
    .sidebar-brand h2 {
      font-size: 16px;
      font-weight: 700;
      color: #fff;
    }

    .sidebar-nav {
      padding: 14px 12px;
      flex: 1;
    }
    .sidebar-nav .nav-label {
      font-size: 10px;
      font-weight: 600;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.45);
      padding: 0 8px;
      margin-bottom: 6px;
    }
    .sidebar-nav ul { list-style: none; }
    .sidebar-nav ul li a {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 9px 10px;
      border-radius: 8px;
      text-decoration: none;
      color: rgba(255,255,255,0.75);
      font-size: 13.5px;
      transition: background 0.15s, color 0.15s;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .sidebar-nav ul li a:hover {
      background: rgba(255,255,255,0.15);
      color: #fff;
    }
    .sidebar-nav ul li a.active {
      background: rgba(255,255,255,0.2);
      color: #fff;
      font-weight: 500;
    }

    .sidebar-footer {
      padding: 14px 12px;
      border-top: 1px solid rgba(255,255,255,0.12);
    }
    .sidebar-footer a {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      padding: 9px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.25);
      color: rgba(255,255,255,0.75);
      font-size: 13px;
      text-decoration: none;
      transition: background 0.15s, color 0.15s;
    }
    .sidebar-footer a:hover {
      background: rgba(255,255,255,0.12);
      color: #fff;
    }

    /* ── Mobile top nav ── */
    .mobile-topnav {
      display: none;
      position: sticky;
      top: 0;
      z-index: 100;
      background: linear-gradient(90deg, #1e3a8a, #1a56db);
      padding: 12px 16px;
      align-items: center;
      justify-content: space-between;
    }
    .mobile-topnav .brand {
      font-size: 15px;
      font-weight: 700;
      color: #fff;
    }
    .hamburger {
      background: none;
      border: none;
      cursor: pointer;
      color: #fff;
      font-size: 20px;
      padding: 4px;
    }

    /* Overlay */
    .overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.45);
      z-index: 150;
    }
    .overlay.active { display: block; }

    /* ── Main ── */
    .main {
      margin-left: var(--sidebar-w);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* ── Topbar ── */
    .topbar {
      background: var(--white);
      border-bottom: 1px solid var(--border);
      padding: 14px 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .topbar .breadcrumb {
      font-size: 13px;
      color: var(--muted);
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .topbar .breadcrumb a {
      color: var(--blue);
      text-decoration: none;
    }
    .topbar .breadcrumb a:hover { text-decoration: underline; }
    .topbar .breadcrumb span { color: var(--muted); }

    /* ── Page content ── */
    .content { padding: 28px; flex: 1; }

    /* ── Page header ── */
    .page-header {
      display: flex;
      align-items: center;
      gap: 16px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 20px 24px;
      margin-bottom: 24px;
    }
    .header-icon {
      width: 48px; height: 48px;
      border-radius: 12px;
      background: var(--blue-light);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .header-icon i { font-size: 20px; color: var(--blue); }
    .header-text h1 {
      font-size: 18px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 3px;
    }
    .header-text p {
      font-size: 13px;
      color: var(--muted);
    }
    .header-text p strong { color: var(--text); font-weight: 500; }

    /* ── Stats row ── */
    .stats-row {
      display: flex;
      gap: 14px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }
    .stat-box {
      flex: 1;
      min-width: 130px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 16px 20px;
      display: flex;
      flex-direction: column;
      gap: 4px;
    }
    .stat-box .num {
      font-size: 26px;
      font-weight: 700;
      color: var(--blue);
      line-height: 1;
    }
    .stat-box .lbl {
      font-size: 11px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    /* ── Table card ── */
    .table-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      margin-bottom: 24px;
    }
    .table-card-head {
      padding: 14px 20px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .table-card-head h3 {
      font-size: 13px;
      font-weight: 600;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: .06em;
    }
    .badge {
      background: var(--blue-light);
      color: var(--blue);
      font-size: 12px;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 20px;
    }

    .table-responsive { overflow-x: auto; }

    .students-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 560px;
    }
    .students-table thead tr {
      background: #f8fafc;
    }
    .students-table th {
      padding: 11px 16px;
      text-align: left;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: var(--muted);
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }
    .students-table th i { margin-right: 5px; color: var(--blue); font-size: 11px; }
    .students-table td {
      padding: 12px 16px;
      font-size: 13.5px;
      color: var(--text);
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
    }
    .students-table tbody tr:last-child td { border-bottom: none; }
    .students-table tbody tr:hover { background: #f8fafc; }

    /* Student name cell */
    .student-name {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .student-avatar {
      width: 32px; height: 32px;
      border-radius: 50%;
      background: var(--blue-light);
      color: var(--blue);
      font-size: 12px;
      font-weight: 600;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .student-name strong { font-weight: 600; font-size: 13.5px; }

    /* Date pill */
    .date-pill {
      display: inline-block;
      background: #f0fdf4;
      color: #166534;
      font-size: 12px;
      font-weight: 500;
      padding: 3px 10px;
      border-radius: 20px;
    }
    .date-pill.na {
      background: #f8fafc;
      color: var(--muted);
    }

    /* ── Empty state ── */
    .empty-state {
      text-align: center;
      padding: 56px 20px;
      color: var(--muted);
    }
    .empty-state i { font-size: 40px; margin-bottom: 14px; opacity: .4; }
    .empty-state h3 { font-size: 16px; font-weight: 600; margin-bottom: 6px; color: var(--text); }
    .empty-state p { font-size: 13px; }

    /* ── Back button ── */
    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 9px 18px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--text);
      font-size: 13.5px;
      font-weight: 500;
      text-decoration: none;
      cursor: pointer;
      transition: background 0.15s, border-color 0.15s;
    }
    .btn-back:hover {
      background: var(--bg);
      border-color: #cbd5e1;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .mobile-topnav { display: flex; }
      .main { margin-left: 0; }
      .topbar { display: none; }
      .content { padding: 16px; }
      .page-header { flex-direction: column; align-items: flex-start; gap: 12px; padding: 16px; }
      .stats-row { gap: 10px; }
      .stat-box { min-width: 100px; }
    }
  </style>
</head>
<body>

<!-- Sidebar overlay (mobile) -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <img src="alphalogo.jpeg" alt="logo" style="width:80px;margin-bottom:4px;height:80px;margin:auto;border-radius: 10px;">
    <div class="site-label">InternBoot</div>
    <h2>Trainer Panel</h2>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Courses</div>
    <ul>
      <?php foreach ($courses as $c): ?>
        <li>
          <a href="view_batches.php?course_id=<?= (int)$c['id'] ?>">
            <i class="fas fa-book-open" style="font-size:12px;opacity:.7;"></i>
            <?= htmlspecialchars($c['name']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <div class="sidebar-footer">
    <a href="trainer_logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
  </div>
</div>

<!-- Mobile top nav -->
<div class="mobile-topnav">
  <span class="brand">Trainer Panel</span>
  <button class="hamburger" onclick="openSidebar()"><i class="fas fa-bars"></i></button>
</div>

<!-- Main -->
<div class="main">

  <!-- Desktop topbar -->
  <div class="topbar">
    <div class="breadcrumb">
      <a href="trainer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
      <span>›</span>
      <a href="view_batches.php?course_id=">Batches</a>
      <span>›</span>
      <span><?= htmlspecialchars($batch['batch_name']) ?></span>
    </div>
    <a href="javascript:history.back()" class="btn-back">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>

  <div class="content">

    <!-- Page header -->
    <div class="page-header">
      <div class="header-icon">
        <i class="fas fa-users"></i>
      </div>
      <div class="header-text">
        <h1><?= htmlspecialchars($batch['batch_name']) ?></h1>
        <p>
          <strong>Course:</strong> <?= htmlspecialchars($batch['course_name']) ?>
          &nbsp;•&nbsp;
          <strong>Students:</strong> <?= count($students) ?> enrolled
        </p>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-box">
        <span class="num"><?= count($students) ?></span>
        <span class="lbl">Total Students</span>
      </div>
      <div class="stat-box">
        <span class="num"><?= count(array_filter($students, fn($s) => !empty($s['internship_start_date']))) ?></span>
        <span class="lbl">Started Internship</span>
      </div>
      <!-- <div class="stat-box">
        <span class="num"><?= count(array_filter($students, fn($s) => $s['certificate_access'])) ?></span>
        <span class="lbl">Certificates Given</span>
      </div> -->
    </div>

    <!-- Table -->
    <div class="table-card">
      <div class="table-card-head">
        <h3>Student List</h3>
        <span class="badge"><?= count($students) ?> students</span>
      </div>

      <?php if (!empty($students)): ?>
        <div class="table-responsive">
          <table class="students-table">
            <thead>
              <tr>
                <th><i class="fas fa-user"></i> Student Name</th>
                <th><i class="fas fa-envelope"></i> Email</th>
                <th><i class="fas fa-phone"></i> Mobile</th>
                <th><i class="fas fa-calendar-check"></i> Enrolled On</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($students as $s): ?>
                <tr>
                  <td>
                    <div class="student-name">
                      <div class="student-avatar">
                        <?= strtoupper(substr($s['first_name'], 0, 1)) ?>
                      </div>
                      <strong><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></strong>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($s['email']) ?></td>
                  <td><?= htmlspecialchars($s['mobile']) ?></td>
                  <td>
                    <?php if (!empty($s['internship_start_date'])): ?>
                      <span class="date-pill"><?= date('d M Y', strtotime($s['internship_start_date'])) ?></span>
                    <?php else: ?>
                      <span class="date-pill na">N/A</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-user-slash"></i>
          <h3>No Students Enrolled Yet</h3>
          <p>This batch currently has no students.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Back button -->
    <a href="javascript:history.back()" class="btn-back">
      <i class="fas fa-arrow-left"></i> Back to Batches
    </a>

  </div>
</div>

<script>
  function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('overlay').classList.add('active');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('active');
  }
</script>

</body>
</html>