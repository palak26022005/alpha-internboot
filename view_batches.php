<?php
session_start();
require_once 'db.php';
$conn = get_db_connection();

if (!isset($_SESSION['trainer_id'])) {
    header("Location: trainer_login.php");
    exit;
}

$courseId = $_GET['course_id'] ?? 0;
if (!$courseId) {
    die("Invalid course selected.");
}

$courseStmt = $conn->prepare("SELECT name FROM courses WHERE id = ?");
$courseStmt->execute([$courseId]);
$course = $courseStmt->fetch(PDO::FETCH_ASSOC);
$courseName = $course['name'] ?? 'Course';

$stmt = $conn->prepare("SELECT * FROM batches WHERE course_id = ? ORDER BY start_date DESC");
$stmt->execute([$courseId]);
$batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Batches – <?= htmlspecialchars($courseName) ?></title>
  <link rel="icon" href="https://internboot.com/img/logos/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --sidebar-w: 240px;
      --blue:       #1a56db;
      --blue-dark:  #1e3a8a;
      --blue-light: #dbeafe;
      --bg:         #f1f5f9;
      --white:      #ffffff;
      --border:     #e2e8f0;
      --text:       #1e293b;
      --muted:      #64748b;
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
      color: rgba(255,255,255,0.5);
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
    .sidebar-nav ul li a:hover,
    .sidebar-nav ul li a.active {
      background: rgba(255,255,255,0.18);
      color: #fff;
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
    .mobile-topnav .brand { font-size: 15px; font-weight: 700; color: #fff; }
    .hamburger {
      background: none; border: none; cursor: pointer;
      color: #fff; font-size: 20px; padding: 4px;
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
    .breadcrumb {
      font-size: 13px;
      color: var(--muted);
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .breadcrumb a { color: var(--blue); text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 8px 16px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 8px;
      color: var(--text);
      font-size: 13px;
      font-weight: 500;
      text-decoration: none;
      cursor: pointer;
      transition: background 0.15s, border-color 0.15s;
    }
    .btn-back:hover { background: var(--bg); border-color: #cbd5e1; }

    /* ── Content ── */
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
    .header-text h1 span { color: var(--blue); }
    .header-text p { font-size: 13px; color: var(--muted); }

    /* ── Stats ── */
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

    /* ── Section label ── */
    .section-label {
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .07em;
      color: var(--muted);
      margin-bottom: 14px;
    }

    /* ── Batches grid ── */
    .batches-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .batch-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
      transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
    }
    .batch-card:hover {
      border-color: var(--blue);
      box-shadow: 0 4px 20px rgba(26,86,219,0.10);
      transform: translateY(-2px);
    }

    .batch-header {
      padding: 18px 20px 14px;
      border-bottom: 1px solid var(--border);
    }
    .batch-header h3 {
      font-size: 15px;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 6px;
    }
    .batch-date {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      color: var(--muted);
      background: #f8fafc;
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 3px 10px;
    }
    .batch-date i { color: var(--blue); font-size: 11px; }

    .batch-body {
      padding: 14px 20px;
    }
    .batch-meta {
      display: flex;
      gap: 12px;
      margin-bottom: 14px;
      flex-wrap: wrap;
    }
    .meta-item {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: var(--muted);
    }
    .meta-item i { color: var(--blue); font-size: 11px; }

    .btn-view {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      width: 100%;
      justify-content: center;
      padding: 9px 16px;
      background: var(--blue);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 13.5px;
      font-weight: 500;
      text-decoration: none;
      cursor: pointer;
      transition: background 0.15s;
    }
    .btn-view:hover { background: var(--blue-dark); }
    .btn-view i { font-size: 13px; }

    /* ── Empty state ── */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 12px;
      color: var(--muted);
    }
    .empty-state i { font-size: 40px; margin-bottom: 14px; opacity: .35; display: block; }
    .empty-state h3 { font-size: 16px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
    .empty-state p { font-size: 13px; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .mobile-topnav { display: flex; }
      .main { margin-left: 0; }
      .topbar { display: none; }
      .content { padding: 16px; }
      .page-header { flex-direction: column; align-items: flex-start; gap: 12px; padding: 16px; }
      .batches-grid { grid-template-columns: 1fr; }
      .stats-row { gap: 10px; }
    }
  </style>
</head>
<body>

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
          <a href="view_batches.php?course_id=<?= (int)$c['id'] ?>"
             class="<?= $c['id'] == $courseId ? 'active' : '' ?>">
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

  <div class="topbar">
    <div class="breadcrumb">
      <a href="trainer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
      <span>›</span>
      <span><?= htmlspecialchars($courseName) ?></span>
    </div>
    <a href="trainer_dashboard.php" class="btn-back">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>

  <div class="content">

    <!-- Page header -->
    <div class="page-header">
      <div class="header-icon">
        <i class="fas fa-layer-group"></i>
      </div>
      <div class="header-text">
        <h1><?= htmlspecialchars($courseName) ?> <span>Batches</span></h1>
        <p>Select a batch to manage students or upload study materials.</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-box">
        <span class="num"><?= count($batches) ?></span>
        <span class="lbl">Total Batches</span>
      </div>
      <div class="stat-box">
        <span class="num">
          <?= count(array_filter($batches, fn($b) => strtotime($b['start_date']) <= time())) ?>
        </span>
        <span class="lbl">Started</span>
      </div>
      <div class="stat-box">
        <span class="num">
          <?= count(array_filter($batches, fn($b) => strtotime($b['start_date']) > time())) ?>
        </span>
        <span class="lbl">Upcoming</span>
      </div>
    </div>

    <!-- Batches -->
    <?php if (!empty($batches)): ?>
      <div class="section-label">All Batches</div>
      <div class="batches-grid">
        <?php foreach ($batches as $b): ?>
          <div class="batch-card">
            <div class="batch-header">
              <h3><?= htmlspecialchars($b['batch_name']) ?></h3>
              <span class="batch-date">
                <i class="fas fa-calendar-alt"></i>
                <?= date('d M Y', strtotime($b['start_date'])) ?>
              </span>
            </div>
            <div class="batch-body">
              <a href="view_students.php?batch_id=<?= (int)$b['id'] ?>" class="btn-view">
                <i class="fas fa-users"></i> View Students
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>No Batches Yet</h3>
        <p>This course doesn't have any batches assigned.</p>
      </div>
    <?php endif; ?>

    <a href="trainer_dashboard.php" class="btn-back">
      <i class="fas fa-arrow-left"></i> Back to Dashboard
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