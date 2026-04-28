<?php
session_start();
require_once 'db.php';
$conn = get_db_connection();

if (!isset($_SESSION['trainer_id'])) {
    header("Location: trainer_login.php");
    exit;
}

$courses = $conn->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);

$trainer_name = 'Trainer';
try {
    $stmt = $conn->prepare("SELECT name FROM trainers WHERE id = ?");
    $stmt->execute([$_SESSION['trainer_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) $trainer_name = $row['name'];
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trainer Dashboard</title>
  <link rel="icon" href="https://internboot.com/img/logos/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --sidebar-w:  240px;
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
    .sidebar-brand h2 { font-size: 16px; font-weight: 700; color: #fff; margin: 0; }
    .sidebar-nav { padding: 14px 12px; flex: 1; }
    .sidebar-nav .nav-label {
      font-size: 10px; font-weight: 600; letter-spacing: .1em;
      text-transform: uppercase; color: rgba(255,255,255,0.45);
      padding: 0 8px; margin-bottom: 6px;
    }
    .sidebar-nav ul { list-style: none; }
    .sidebar-nav ul li a {
      display: flex; align-items: center; gap: 8px;
      padding: 9px 10px; border-radius: 8px; text-decoration: none;
      color: rgba(255,255,255,0.75); font-size: 13.5px;
      transition: background 0.15s, color 0.15s;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .sidebar-nav ul li a:hover {
      background: rgba(255,255,255,0.18); color: #fff;
    }
    .sidebar-footer {
      padding: 14px 12px;
      border-top: 1px solid rgba(255,255,255,0.12);
    }
    .sidebar-footer a {
      display: flex; align-items: center; justify-content: center; gap: 6px;
      padding: 9px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.25);
      color: rgba(255,255,255,0.75); font-size: 13px; text-decoration: none;
      transition: background 0.15s, color 0.15s;
    }
    .sidebar-footer a:hover { background: rgba(255,255,255,0.12); color: #fff; }

    /* ── Mobile top nav ── */
    .mobile-topnav {
      display: none; position: sticky; top: 0; z-index: 100;
      background: linear-gradient(90deg, #1e3a8a, #1a56db);
      padding: 12px 16px; align-items: center; justify-content: space-between;
    }
    .mobile-topnav .brand { font-size: 15px; font-weight: 700; color: #fff; }
    .hamburger { background: none; border: none; cursor: pointer; color: #fff; font-size: 20px; padding: 4px; }

    .overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,0.45); z-index: 150;
    }
    .overlay.active { display: block; }

    /* ── Main ── */
    .main { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }

    .topbar {
      background: var(--white); border-bottom: 1px solid var(--border);
      padding: 14px 28px; display: flex; align-items: center; justify-content: space-between;
    }
    .topbar-title { font-size: 15px; font-weight: 600; color: var(--text); }
    .topbar-user { display: flex; align-items: center; gap: 9px; font-size: 13px; color: var(--muted); }
    .avatar {
      width: 32px; height: 32px; border-radius: 50%;
      background: var(--blue-light); color: var(--blue);
      font-size: 13px; font-weight: 600;
      display: flex; align-items: center; justify-content: center;
    }

    .content { padding: 28px; flex: 1; }

    /* ── Welcome banner ── */
    .welcome-banner {
      background: linear-gradient(135deg, #1e3a8a 0%, #1a56db 100%);
      border-radius: 14px; padding: 28px 32px; margin-bottom: 24px;
      display: flex; align-items: center; justify-content: space-between;
      gap: 16px; flex-wrap: wrap;
    }
    .welcome-banner .text h2 { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 5px; }
    .welcome-banner .text p { font-size: 13.5px; color: rgba(255,255,255,0.7); }
    .welcome-banner .icon-wrap {
      width: 56px; height: 56px; border-radius: 14px;
      background: rgba(255,255,255,0.15);
      display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .welcome-banner .icon-wrap i { font-size: 24px; color: rgba(255,255,255,0.9); }

    /* ── Stats ── */
    .stats-row { display: flex; gap: 14px; margin-bottom: 24px; flex-wrap: wrap; }
    .stat-box {
      flex: 1; min-width: 130px; background: var(--white);
      border: 1px solid var(--border); border-radius: 10px;
      padding: 16px 20px; display: flex; flex-direction: column; gap: 4px;
    }
    .stat-box .num { font-size: 26px; font-weight: 700; color: var(--blue); line-height: 1; }
    .stat-box .lbl { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; }

    .section-label {
      font-size: 12px; font-weight: 600; text-transform: uppercase;
      letter-spacing: .07em; color: var(--muted); margin-bottom: 14px;
    }

    /* ── Course grid ── */
    .course-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 14px; margin-bottom: 24px;
    }
    .course-card {
      background: var(--white); border: 1px solid var(--border);
      border-radius: 12px; padding: 20px; text-decoration: none;
      color: var(--text); display: flex; flex-direction: column; gap: 12px;
      transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
    }
    .course-card:hover {
      border-color: var(--blue);
      box-shadow: 0 4px 20px rgba(26,86,219,0.10);
      transform: translateY(-2px);
    }
    .course-card-icon {
      width: 40px; height: 40px; border-radius: 10px;
      background: var(--blue-light);
      display: flex; align-items: center; justify-content: center;
    }
    .course-card-icon i { font-size: 16px; color: var(--blue); }
    .course-card h3 { font-size: 14px; font-weight: 600; color: var(--text); line-height: 1.4; }
    .course-card .go {
      font-size: 12px; color: var(--blue); font-weight: 500;
      margin-top: auto; display: flex; align-items: center; gap: 5px;
    }

    /* ── How it works ── */
    .how-list {
      list-style: none; background: var(--white);
      border: 1px solid var(--border); border-radius: 12px;
      overflow: hidden; counter-reset: how;
    }
    .how-list li {
      display: flex; align-items: flex-start; gap: 12px;
      padding: 12px 18px; font-size: 13.5px; color: var(--text);
      border-bottom: 1px solid #f1f5f9; counter-increment: how;
    }
    .how-list li:last-child { border-bottom: none; }
    .how-list li::before {
      content: counter(how); width: 22px; height: 22px; border-radius: 50%;
      background: var(--blue-light); color: var(--blue);
      font-size: 11px; font-weight: 600;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; margin-top: 1px;
    }

    /* ── Empty ── */
    .empty-state {
      text-align: center; padding: 48px 20px;
      background: var(--white); border: 1px solid var(--border);
      border-radius: 12px; color: var(--muted);
    }
    .empty-state i { font-size: 36px; opacity: .35; display: block; margin-bottom: 12px; }
    .empty-state h3 { font-size: 15px; font-weight: 600; color: var(--text); margin-bottom: 5px; }
    .empty-state p { font-size: 13px; }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
      .mobile-topnav { display: flex; }
      .main { margin-left: 0; }
      .topbar { display: none; }
      .content { padding: 16px; }
      .welcome-banner { padding: 20px; }
      .welcome-banner .icon-wrap { display: none; }
      .course-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
      .stats-row { gap: 10px; }
    }
    @media (max-width: 420px) {
      .course-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

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

<div class="mobile-topnav">
  
  <span class="brand">Trainer Panel</span>
  <button class="hamburger" onclick="openSidebar()"><i class="fas fa-bars"></i></button>
</div>

<div class="main">

  <div class="topbar">
    <span class="topbar-title">Dashboard</span>
    <div class="topbar-user">
      <div class="avatar"><?= strtoupper(substr($trainer_name, 0, 1)) ?></div>
      <?= htmlspecialchars($trainer_name) ?>
    </div>
  </div>

  <div class="content">

    <div class="welcome-banner">
      <div class="text">
        <h2>Welcome back, <?= htmlspecialchars($trainer_name) ?> 👋</h2>
        <!-- <p>Select a course to manage batches and upload study material.</p> -->
      </div>
      <div class="icon-wrap">
        <i class="fas fa-chalkboard-teacher"></i>
      </div>
    </div>

    <div class="stats-row">
      <div class="stat-box">
        <span class="num"><?= count($courses) ?></span>
        <span class="lbl">Total Courses</span>
      </div>
      <!-- <div class="stat-box">
        <span class="num">—</span>
        <span class="lbl">Active Batches</span>
      </div>
      <div class="stat-box">
        <span class="num">—</span>
        <span class="lbl">Study Materials</span>
      </div> -->
    </div>

    <?php if (!empty($courses)): ?>
      <div class="section-label">Your Courses</div>
      <div class="course-grid">
        <?php
          $icons = ['fa-code','fa-database','fa-paint-brush','fa-chart-bar','fa-flask','fa-laptop-code','fa-brain','fa-globe','fa-cogs','fa-project-diagram'];
          foreach ($courses as $i => $c):
            $icon = $icons[$i % count($icons)];
        ?>
          <a class="course-card" href="view_batches.php?course_id=<?= (int)$c['id'] ?>">
            <div class="course-card-icon">
              <i class="fas <?= $icon ?>"></i>
            </div>
            <h3><?= htmlspecialchars($c['name']) ?></h3>
            <span class="go">View Batches <i class="fas fa-arrow-right" style="font-size:11px;"></i></span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>No Courses Assigned</h3>
        <p>No courses have been assigned to you yet.</p>
      </div>
    <?php endif; ?>

    <div class="section-label">How It Works</div>
    <ul class="how-list">
      <li>Click a course above or from the sidebar to open its batches.</li>
      <li>Inside a course, select a batch to see enrolled students.</li>
      <!-- <li>Use the material upload section to add PDFs, videos, or links.</li>
      <li>Students will see uploaded material in their dashboard instantly.</li> -->
    </ul>

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