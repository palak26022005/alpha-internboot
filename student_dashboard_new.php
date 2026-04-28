<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
    <link rel="icon" href="https://internboot.com/img/logos/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #1e40af;
      --primary-light: #3b82f6;
      --primary-dark: #1e3a8a;
      --primary-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
      --accent: #60a5fa;
      --bg: #f0f4ff;
      --card-bg: #ffffff;
      --text-dark: #1e293b;
      --text-medium: #475569;
      --text-light: #94a3b8;
      --border: #e2e8f0;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
      --shadow-md: 0 4px 20px rgba(30,64,175,0.1);
      --shadow-lg: 0 10px 40px rgba(30,64,175,0.15);
      --shadow-xl: 0 20px 60px rgba(30,64,175,0.2);
      --radius: 16px;
      --radius-sm: 10px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text-dark);
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ═══════════════════════════════════════
       SIDEBAR
    ═══════════════════════════════════════ */
    .sidebar {
      width: 280px;
      min-height: 100vh;
      background: var(--primary-gradient);
      padding: 0;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      transition: var(--transition);
      box-shadow: 4px 0 30px rgba(30, 64, 175, 0.3);
    }

    .sidebar-header {
      padding: 32px 24px 28px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .sidebar-logo .logo-icon {
      width: 48px;
      height: 48px;
      background: rgba(255,255,255,0.2);
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      color: #fff;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-logo h2 {
      color: #fff;
      font-size: 20px;
      font-weight: 700;
      letter-spacing: -0.3px;
    }

    .sidebar-logo span {
      color: rgba(255,255,255,0.6);
      font-size: 12px;
      font-weight: 400;
      display: block;
      margin-top: 2px;
    }

    .sidebar-nav {
      padding: 20px 16px;
      flex: 1;
    }

    .nav-label {
      color: rgba(255,255,255,0.4);
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      padding: 0 12px;
      margin-bottom: 12px;
      margin-top: 8px;
    }

    .sidebar-nav ul {
      list-style: none;
    }

    .sidebar-nav ul li {
      margin-bottom: 4px;
    }

    .sidebar-nav ul li a {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 13px 16px;
      color: rgba(255,255,255,0.7);
      text-decoration: none;
      font-size: 14.5px;
      font-weight: 500;
      border-radius: 12px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .sidebar-nav ul li a::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(255,255,255,0.1);
      opacity: 0;
      transition: var(--transition);
      border-radius: 12px;
    }

    .sidebar-nav ul li a:hover::before {
      opacity: 1;
    }

    .sidebar-nav ul li a:hover {
      color: #fff;
      transform: translateX(4px);
    }

    .sidebar-nav ul li a.active {
      background: rgba(255,255,255,0.18);
      color: #fff;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .sidebar-nav ul li a.active::after {
      content: '';
      position: absolute;
      right: 14px;
      width: 6px;
      height: 6px;
      background: #60a5fa;
      border-radius: 50%;
      box-shadow: 0 0 10px #60a5fa;
    }

    .sidebar-nav ul li a i {
      font-size: 18px;
      width: 22px;
      text-align: center;
      flex-shrink: 0;
    }

    .sidebar-footer {
      padding: 20px 16px;
      border-top: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-footer .user-card {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px;
      background: rgba(255,255,255,0.1);
      border-radius: 12px;
      backdrop-filter: blur(10px);
    }

    .sidebar-footer .user-avatar {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #60a5fa, #a78bfa);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      font-size: 16px;
    }

    .sidebar-footer .user-info h4 {
      color: #fff;
      font-size: 13px;
      font-weight: 600;
    }

    .sidebar-footer .user-info p {
      color: rgba(255,255,255,0.5);
      font-size: 11px;
      margin-top: 1px;
    }

    .logout-btn {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 13px 16px;
      color: rgba(255,255,255,0.6);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      border-radius: 12px;
      transition: var(--transition);
      margin-top: 10px;
      border: none;
      background: none;
      cursor: pointer;
      width: 100%;
    }

    .logout-btn:hover {
      background: rgba(239,68,68,0.2);
      color: #fca5a5;
    }

    /* ═══════════════════════════════════════
       MAIN CONTENT
    ═══════════════════════════════════════ */
    .main-content {
      margin-left: 280px;
      flex: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* TOP BAR */
    .topbar {
      background: var(--card-bg);
      padding: 20px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 100;
      backdrop-filter: blur(20px);
      background: rgba(255,255,255,0.9);
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 22px;
      color: var(--text-dark);
      cursor: pointer;
      padding: 8px;
      border-radius: 8px;
      transition: var(--transition);
    }

    .mobile-toggle:hover {
      background: var(--bg);
    }

    .topbar h1 {
      font-size: 22px;
      font-weight: 700;
      color: var(--text-dark);
      letter-spacing: -0.5px;
    }

    .topbar h1 span {
      color: var(--primary-light);
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .topbar-btn {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      border: 1px solid var(--border);
      background: var(--card-bg);
      color: var(--text-medium);
      font-size: 17px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
      position: relative;
    }

    .topbar-btn:hover {
      background: var(--bg);
      color: var(--primary);
      border-color: var(--primary-light);
    }

    .topbar-btn .badge {
      position: absolute;
      top: -2px;
      right: -2px;
      width: 18px;
      height: 18px;
      background: #ef4444;
      color: #fff;
      font-size: 10px;
      font-weight: 700;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid #fff;
    }

    /* PAGE CONTENT */
    .page-content {
      padding: 32px 40px;
      flex: 1;
    }

    /* CONTENT SECTIONS */
    .content-section {
      display: none;
      animation: fadeInUp 0.5s ease;
    }

    .content-section.active {
      display: block;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* WELCOME BANNER */
    .welcome-banner {
      background: var(--primary-gradient);
      border-radius: var(--radius);
      padding: 40px;
      color: #fff;
      position: relative;
      overflow: hidden;
      margin-bottom: 32px;
    }

    .welcome-banner::before {
      content: '';
      position: absolute;
      right: -60px;
      top: -60px;
      width: 250px;
      height: 250px;
      background: rgba(255,255,255,0.08);
      border-radius: 50%;
    }

    .welcome-banner::after {
      content: '';
      position: absolute;
      right: 80px;
      bottom: -40px;
      width: 150px;
      height: 150px;
      background: rgba(255,255,255,0.05);
      border-radius: 50%;
    }

    .welcome-banner h2 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 8px;
      position: relative;
      z-index: 1;
    }

    .welcome-banner p {
      font-size: 15px;
      opacity: 0.85;
      position: relative;
      z-index: 1;
      max-width: 500px;
      line-height: 1.6;
    }

    .welcome-banner .welcome-icon {
      position: absolute;
      right: 40px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 80px;
      opacity: 0.15;
      z-index: 0;
    }

    /* STATS GRID */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 32px;
    }

    .stat-card {
      background: var(--card-bg);
      border-radius: var(--radius);
      padding: 28px;
      border: 1px solid var(--border);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
      border-color: transparent;
    }

    .stat-card .stat-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      margin-bottom: 18px;
    }

    .stat-card .stat-icon.blue {
      background: rgba(59,130,246,0.1);
      color: var(--primary-light);
    }

    .stat-card .stat-icon.green {
      background: rgba(16,185,129,0.1);
      color: #10b981;
    }

    .stat-card .stat-icon.purple {
      background: rgba(139,92,246,0.1);
      color: #8b5cf6;
    }

    .stat-card .stat-icon.orange {
      background: rgba(245,158,11,0.1);
      color: #f59e0b;
    }

    .stat-card h3 {
      font-size: 28px;
      font-weight: 800;
      color: var(--text-dark);
      margin-bottom: 4px;
    }

    .stat-card p {
      font-size: 13px;
      color: var(--text-light);
      font-weight: 500;
    }

    /* CARDS */
    .card {
      background: var(--card-bg);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      padding: 32px;
      margin-bottom: 24px;
      transition: var(--transition);
    }

    .card:hover {
      box-shadow: var(--shadow-md);
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--border);
    }

    .card-header h2 {
      font-size: 20px;
      font-weight: 700;
      color: var(--text-dark);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card-header h2 i {
      color: var(--primary-light);
    }

    /* PROFILE SECTION */
    .profile-grid {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 32px;
    }

    .profile-left {
      text-align: center;
    }

    .profile-avatar-large {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      background: var(--primary-gradient);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      color: #fff;
      font-weight: 700;
      margin: 0 auto 20px;
      box-shadow: 0 8px 30px rgba(59,130,246,0.3);
      border: 4px solid #fff;
      position: relative;
    }

    .profile-avatar-large::after {
      content: '';
      position: absolute;
      bottom: 8px;
      right: 8px;
      width: 20px;
      height: 20px;
      background: #10b981;
      border-radius: 50%;
      border: 3px solid #fff;
    }

    .profile-name {
      font-size: 22px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 4px;
    }

    .profile-role {
      font-size: 14px;
      color: var(--text-light);
      margin-bottom: 20px;
    }

    .profile-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 16px;
      background: rgba(16,185,129,0.1);
      color: #10b981;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
    }

    .profile-details {
      display: grid;
      gap: 0;
    }

    .detail-row {
      display: flex;
      align-items: center;
      padding: 16px 0;
      border-bottom: 1px solid var(--border);
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-row .detail-icon {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      background: rgba(59,130,246,0.08);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-light);
      font-size: 16px;
      margin-right: 16px;
      flex-shrink: 0;
    }

    .detail-row .detail-label {
      font-size: 12px;
      color: var(--text-light);
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .detail-row .detail-value {
      font-size: 15px;
      color: var(--text-dark);
      font-weight: 600;
      margin-top: 2px;
    }

    /* EDIT PROFILE FORM */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .form-group {
      margin-bottom: 4px;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-medium);
      margin-bottom: 8px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      font-size: 14px;
      font-family: 'Inter', sans-serif;
      color: var(--text-dark);
      transition: var(--transition);
      background: #f8fafc;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--primary-light);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 100px;
    }

    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    /* BUTTONS */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 28px;
      border: none;
      border-radius: var(--radius-sm);
      font-size: 14px;
      font-weight: 600;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }

    .btn-primary {
      background: var(--primary-gradient);
      color: #fff;
      box-shadow: 0 4px 15px rgba(30,64,175,0.3);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(30,64,175,0.4);
    }

    .btn-secondary {
      background: var(--bg);
      color: var(--text-medium);
      border: 1px solid var(--border);
    }

    .btn-secondary:hover {
      background: #e2e8f0;
    }

    .btn-success {
      background: linear-gradient(135deg, #059669, #10b981);
      color: #fff;
      box-shadow: 0 4px 15px rgba(16,185,129,0.3);
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(16,185,129,0.4);
    }

    /* LOI SECTION */
    .loi-container {
      text-align: center;
      padding: 40px 20px;
    }

    .loi-icon {
      width: 100px;
      height: 100px;
      background: rgba(59,130,246,0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      color: var(--primary-light);
      margin: 0 auto 24px;
    }

    .loi-container h3 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .loi-container p {
      color: var(--text-light);
      font-size: 15px;
      max-width: 450px;
      margin: 0 auto 16px;
      line-height: 1.6;
    }

    .exam-instructions {
      text-align: left;
      background: #f0f4ff;
      border-radius: var(--radius-sm);
      padding: 24px;
      margin: 24px 0;
    }

    .exam-instructions h4 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 14px;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .exam-instructions ul {
      list-style: none;
      padding: 0;
    }

    .exam-instructions ul li {
      padding: 8px 0;
      font-size: 14px;
      color: var(--text-medium);
      display: flex;
      align-items: flex-start;
      gap: 10px;
    }

    .exam-instructions ul li::before {
      content: '✓';
      color: var(--primary-light);
      font-weight: 700;
      flex-shrink: 0;
      width: 22px;
      height: 22px;
      background: rgba(59,130,246,0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
    }

    .agree-box {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 16px 20px;
      background: #fff;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      margin: 20px 0;
      cursor: pointer;
      transition: var(--transition);
    }

    .agree-box:hover {
      border-color: var(--primary-light);
    }

    .agree-box input[type="checkbox"] {
      width: 20px;
      height: 20px;
      accent-color: var(--primary);
    }

    .agree-box label {
      font-size: 14px;
      color: var(--text-medium);
      cursor: pointer;
    }

    /* EXAM SECTION */
    .exam-container {
      max-width: 800px;
    }

    .exam-timer {
      background: linear-gradient(135deg, #dc2626, #ef4444);
      color: #fff;
      padding: 12px 24px;
      border-radius: var(--radius-sm);
      display: inline-flex;
      align-items: center;
      gap: 10px;
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 24px;
      box-shadow: 0 4px 15px rgba(239,68,68,0.3);
    }

    .question-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 24px;
      margin-bottom: 16px;
      transition: var(--transition);
    }

    .question-card:hover {
      border-color: var(--primary-light);
      box-shadow: var(--shadow-sm);
    }

    .question-card .q-number {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      background: var(--primary-gradient);
      color: #fff;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 700;
      margin-right: 10px;
    }

    .question-card .q-text {
      font-size: 15px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 16px;
    }

    .option-label {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 14px;
      border: 1px solid var(--border);
      border-radius: 8px;
      margin-bottom: 8px;
      cursor: pointer;
      transition: var(--transition);
      font-size: 14px;
      color: var(--text-medium);
    }

    .option-label:hover {
      background: #f0f4ff;
      border-color: var(--primary-light);
    }

    .option-label input[type="radio"] {
      accent-color: var(--primary);
    }

    /* STUDY MATERIAL */
    .material-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }

    .material-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 24px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .material-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
      border-color: transparent;
    }

    .material-card .file-icon {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 16px;
    }

    .material-card .file-icon.pdf {
      background: rgba(239,68,68,0.1);
      color: #ef4444;
    }

    .material-card .file-icon.video {
      background: rgba(139,92,246,0.1);
      color: #8b5cf6;
    }

    .material-card .file-icon.doc {
      background: rgba(59,130,246,0.1);
      color: var(--primary-light);
    }

    .material-card .file-icon.img {
      background: rgba(16,185,129,0.1);
      color: #10b981;
    }

    .material-card h4 {
      font-size: 15px;
      font-weight: 600;
      margin-bottom: 6px;
      color: var(--text-dark);
    }

    .material-card .file-meta {
      font-size: 12px;
      color: var(--text-light);
      margin-bottom: 16px;
    }

    .material-card .download-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      font-weight: 600;
      color: var(--primary-light);
      text-decoration: none;
      transition: var(--transition);
    }

    .material-card .download-link:hover {
      color: var(--primary);
      gap: 10px;
    }

    /* DISCORD CARD */
    .discord-banner {
      background: linear-gradient(135deg, #5865F2, #7289da);
      border-radius: var(--radius);
      padding: 40px;
      color: #fff;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .discord-banner::before {
      content: '';
      position: absolute;
      width: 200px;
      height: 200px;
      background: rgba(255,255,255,0.08);
      border-radius: 50%;
      top: -50px;
      right: -50px;
    }

    .discord-banner i.fab {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.9;
    }

    .discord-banner h3 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .discord-banner p {
      font-size: 15px;
      opacity: 0.85;
      margin-bottom: 24px;
      max-width: 400px;
      margin-left: auto;
      margin-right: auto;
    }

    .btn-discord {
      background: #fff;
      color: #5865F2;
      padding: 14px 32px;
      border-radius: var(--radius-sm);
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: var(--transition);
    }

    .btn-discord:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    /* RESULT SECTION */
    .result-card {
      text-align: center;
      padding: 40px;
    }

    .result-icon {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 44px;
      margin: 0 auto 20px;
    }

    .result-icon.success {
      background: rgba(16,185,129,0.1);
      color: #10b981;
    }

    .result-icon.fail {
      background: rgba(239,68,68,0.1);
      color: #ef4444;
    }

    .score-display {
      font-size: 48px;
      font-weight: 800;
      color: var(--text-dark);
      margin-bottom: 8px;
    }

    .score-display span {
      font-size: 24px;
      color: var(--text-light);
    }

    /* ═══════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════ */
    .sidebar-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 999;
    }

    @media (max-width: 1024px) {
      .profile-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.open {
        transform: translateX(0);
      }

      .sidebar-overlay.active {
        display: block;
      }

      .main-content {
        margin-left: 0;
      }

      .mobile-toggle {
        display: flex;
      }

      .topbar {
        padding: 16px 20px;
      }

      .page-content {
        padding: 20px;
      }

      .welcome-banner {
        padding: 28px;
      }

      .welcome-banner h2 {
        font-size: 22px;
      }

      .welcome-banner .welcome-icon {
        display: none;
      }

      .form-grid {
        grid-template-columns: 1fr;
      }

      .stats-grid {
        grid-template-columns: 1fr 1fr;
      }

      .material-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 480px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
    }

    /* Scroll bar styling */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: var(--bg);
    }

    ::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }
  </style>
</head>
<body>

  <!-- SIDEBAR OVERLAY (MOBILE) -->
  <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

  <!-- ═══════════════════════════════════════
       SIDEBAR
  ═══════════════════════════════════════ -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <div class="logo-icon">
          <i class="fas fa-graduation-cap"></i>
        </div>
        <div>
          <h2>EduPortal</h2>
          <span>Student Dashboard</span>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <p class="nav-label">Main Menu</p>
      <ul>
        <li>
          <a href="#" class="active" data-page="dashboard" onclick="showPage('dashboard', this)">
            <i class="fas fa-th-large"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="#" data-page="profile" onclick="showPage('profile', this)">
            <i class="fas fa-user-circle"></i> My Profile
          </a>
        </li>
        <li>
          <a href="#" data-page="loi" onclick="showPage('loi', this)">
            <i class="fas fa-file-download"></i> Download LOI
          </a>
        </li>
        <li>
          <a href="#" data-page="editProfile" onclick="showPage('editProfile', this)">
            <i class="fas fa-user-edit"></i> Edit Profile
          </a>
        </li>
      </ul>

      <p class="nav-label" style="margin-top: 24px;">Resources</p>
      <ul>
        <li>
          <a href="#" data-page="studyMaterial" onclick="showPage('studyMaterial', this)">
            <i class="fas fa-book-open"></i> Study Material
          </a>
        </li>
        <li>
          <a href="#" data-page="discord" onclick="showPage('discord', this)">
            <i class="fab fa-discord"></i> Join Discord
          </a>
        </li>
      </ul>
    </nav>

    <div class="sidebar-footer">
      <div class="user-card">
        <div class="user-avatar" id="sidebarAvatar">JS</div>
        <div class="user-info">
          <h4 id="sidebarUserName">John Smith</h4>
          <p>Student</p>
        </div>
      </div>
      <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Log Out
      </a>
    </div>
  </aside>

  <!-- ═══════════════════════════════════════
       MAIN CONTENT
  ═══════════════════════════════════════ -->
  <main class="main-content">

    <!-- TOP BAR -->
    <header class="topbar">
      <div class="topbar-left">
        <button class="mobile-toggle" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        <h1>Welcome <span id="topbarGreeting">back!</span></h1>
      </div>
      <div class="topbar-right">
        <button class="topbar-btn" title="Notifications">
          <i class="fas fa-bell"></i>
          <span class="badge">3</span>
        </button>
        <button class="topbar-btn" title="Messages">
          <i class="fas fa-envelope"></i>
        </button>
      </div>
    </header>

    <!-- PAGE CONTENT AREA -->
    <div class="page-content">

      <!-- ===================== DASHBOARD PAGE ===================== -->
      <section class="content-section active" id="page-dashboard">
        <div class="welcome-banner">
          <h2 id="welcomeText">Welcome back, Student! 👋</h2>
          <p>Track your progress, download your LOI, access study materials, and manage your profile all in one place.</p>
          <i class="fas fa-rocket welcome-icon"></i>
        </div>

        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue">
              <i class="fas fa-book"></i>
            </div>
            <h3 id="statMaterials">12</h3>
            <p>Study Materials</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon green">
              <i class="fas fa-check-circle"></i>
            </div>
            <h3>Cleared</h3>
            <p>Exam Status</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon purple">
              <i class="fas fa-file-alt"></i>
            </div>
            <h3>Ready</h3>
            <p>LOI Status</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon orange">
              <i class="fas fa-calendar-check"></i>
            </div>
            <h3 id="statDate">—</h3>
            <p>Joined Date</p>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-clock"></i> Recent Activity</h2>
          </div>
          <div style="color: var(--text-light); font-size: 14px; text-align:center; padding: 32px 0;">
            <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 12px; display:block; opacity:0.3;"></i>
            Your recent activities will appear here
          </div>
        </div>
      </section>

      <!-- ===================== PROFILE PAGE ===================== -->
      <section class="content-section" id="page-profile">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-user-circle"></i> My Profile</h2>
            <button class="btn btn-primary" onclick="showPage('editProfile', document.querySelector('[data-page=editProfile]'))">
              <i class="fas fa-edit"></i> Edit Profile
            </button>
          </div>
          <div class="profile-grid">
            <div class="profile-left">
              <div class="profile-avatar-large" id="profileAvatar">JS</div>
              <div class="profile-name" id="profileFullName">John Smith</div>
              <div class="profile-role">Student</div>
              <div class="profile-badge">
                <i class="fas fa-check-circle"></i> Active
              </div>
            </div>
            <div class="profile-details" id="profileDetails">
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-envelope"></i></div>
                <div>
                  <div class="detail-label">Email Address</div>
                  <div class="detail-value" id="profileEmail">john@example.com</div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-phone"></i></div>
                <div>
                  <div class="detail-label">Phone Number</div>
                  <div class="detail-value" id="profilePhone">+91 9876543210</div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-birthday-cake"></i></div>
                <div>
                  <div class="detail-label">Date of Birth</div>
                  <div class="detail-value" id="profileDob">January 15, 2000</div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                  <div class="detail-label">Address</div>
                  <div class="detail-value" id="profileAddress">123 Main Street, City, State</div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-id-badge"></i></div>
                <div>
                  <div class="detail-label">Student ID</div>
                  <div class="detail-value" id="profileStudentId">STU-2024-001</div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-layer-group"></i></div>
                <div>
                  <div class="detail-label">Batch</div>
                  <div class="detail-value" id="profileBatch">Batch 2024-A</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ===================== LOI PAGE ===================== -->
      <section class="content-section" id="page-loi">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-file-download"></i> Download Letter of Intent (LOI)</h2>
          </div>
          <div class="loi-container" id="loiContent">
            <div class="loi-icon">
              <i class="fas fa-file-signature"></i>
            </div>
            <h3>Download Your LOI</h3>
            <p>Complete the exam to become eligible to download your Letter of Intent. Please read the instructions carefully before starting.</p>

            <div class="exam-instructions">
              <h4><i class="fas fa-info-circle"></i> Exam Instructions</h4>
              <ul>
                <li>You must complete the test within 30 minutes.</li>
                <li>Do not switch tabs or minimize the window during the test.</li>
                <li>You need at least 20 correct answers out of 25 to download your LOI.</li>
                <li>If you score less than 20, you must retake the exam.</li>
                <li>Once eligible, you can download the LOI directly.</li>
              </ul>
            </div>

            <div class="agree-box">
              <input type="checkbox" id="agree">
              <label for="agree">I have read and agree to all the terms and conditions</label>
            </div>

            <button class="btn btn-primary" onclick="startExam()">
              <i class="fas fa-play-circle"></i> Start Exam
            </button>
          </div>
        </div>

        <!-- Exam Questions Container -->
        <div class="card" id="examContainer" style="display:none;">
          <div class="card-header">
            <h2><i class="fas fa-pencil-alt"></i> Exam in Progress</h2>
            <div class="exam-timer" id="timer">
              <i class="fas fa-clock"></i> <span id="timerText">30:00</span>
            </div>
          </div>
          <div class="exam-container" id="examQuestions"></div>
          <div style="text-align:center; margin-top: 24px;">
            <button class="btn btn-success" onclick="submitExam()" style="padding: 14px 40px; font-size: 16px;">
              <i class="fas fa-paper-plane"></i> Submit Exam
            </button>
          </div>
        </div>

        <!-- Result Container -->
        <div class="card" id="resultContainer" style="display:none;">
          <div id="resultContent"></div>
        </div>
      </section>

      <!-- ===================== EDIT PROFILE PAGE ===================== -->
      <section class="content-section" id="page-editProfile">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
          </div>
          <form id="editProfileForm" onsubmit="saveProfile(event)">
            <div class="form-grid">
              <div class="form-group">
                <label for="editFirstName">First Name</label>
                <input type="text" id="editFirstName" placeholder="Enter first name" value="John">
              </div>
              <div class="form-group">
                <label for="editLastName">Last Name</label>
                <input type="text" id="editLastName" placeholder="Enter last name" value="Smith">
              </div>
              <div class="form-group">
                <label for="editEmail">Email Address</label>
                <input type="email" id="editEmail" placeholder="Enter email" value="john@example.com">
              </div>
              <div class="form-group">
                <label for="editPhone">Phone Number</label>
                <input type="tel" id="editPhone" placeholder="Enter phone number" value="+91 9876543210">
              </div>
              <div class="form-group">
                <label for="editDob">Date of Birth</label>
                <input type="date" id="editDob" value="2000-01-15">
              </div>
              <div class="form-group">
                <label for="editGender">Gender</label>
                <select id="editGender">
                  <option value="male" selected>Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="form-group full-width">
                <label for="editAddress">Address</label>
                <textarea id="editAddress" placeholder="Enter your full address">123 Main Street, City, State</textarea>
              </div>
              <div class="form-group">
                <label for="editCity">City</label>
                <input type="text" id="editCity" placeholder="Enter city" value="Mumbai">
              </div>
              <div class="form-group">
                <label for="editState">State</label>
                <input type="text" id="editState" placeholder="Enter state" value="Maharashtra">
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
              </button>
              <button type="button" class="btn btn-secondary" onclick="showPage('profile', document.querySelector('[data-page=profile]'))">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </form>
        </div>
      </section>

      <!-- ===================== STUDY MATERIAL PAGE ===================== -->
      <section class="content-section" id="page-studyMaterial">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-book-open"></i> Study Material</h2>
          </div>

          <div class="material-grid" id="materialGrid">
            <!-- Materials loaded dynamically or from PHP -->

            <!-- Sample Material Cards (replace with PHP loop) -->
            <div class="material-card">
              <div class="file-icon pdf">
                <i class="fas fa-file-pdf"></i>
              </div>
              <h4>Introduction to Programming</h4>
              <div class="file-meta">PDF • 2.4 MB • Uploaded Jan 15, 2024</div>
              <a href="#" class="download-link" target="_blank">
                <i class="fas fa-download"></i> Download
                <i class="fas fa-arrow-right" style="font-size:11px;"></i>
              </a>
            </div>

            <div class="material-card">
              <div class="file-icon video">
                <i class="fas fa-play-circle"></i>
              </div>
              <h4>Data Structures Tutorial</h4>
              <div class="file-meta">VIDEO • 45 min • Uploaded Jan 20, 2024</div>
              <a href="#" class="download-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> Watch
                <i class="fas fa-arrow-right" style="font-size:11px;"></i>
              </a>
            </div>

            <div class="material-card">
              <div class="file-icon doc">
                <i class="fas fa-file-word"></i>
              </div>
              <h4>Assignment Guidelines</h4>
              <div class="file-meta">DOC • 1.1 MB • Uploaded Feb 02, 2024</div>
              <a href="#" class="download-link" target="_blank">
                <i class="fas fa-download"></i> Download
                <i class="fas fa-arrow-right" style="font-size:11px;"></i>
              </a>
            </div>

            <div class="material-card">
              <div class="file-icon pdf">
                <i class="fas fa-file-pdf"></i>
              </div>
              <h4>Web Development Notes</h4>
              <div class="file-meta">PDF • 3.8 MB • Uploaded Feb 10, 2024</div>
              <a href="#" class="download-link" target="_blank">
                <i class="fas fa-download"></i> Download
                <i class="fas fa-arrow-right" style="font-size:11px;"></i>
              </a>
            </div>

            <div class="material-card">
              <div class="file-icon img">
                <i class="fas fa-image"></i>
              </div>
              <h4>System Architecture Diagram</h4>
              <div class="file-meta">PNG • 800 KB • Uploaded Feb 14, 2024</div>
              <a href="#" class="download-link" target="_blank">
                <i class="fas fa-eye"></i> View
                <i class="fas fa-arrow-right" style="font-size:11px;"></i>
              </a>
            </div>

            <div class="material-card">
              <div class="file-icon video">
                <i class="fas fa-play-circle"></i>
              </div>
              <h4>Database Management Lecture</h4>
              <div class="file-meta">VIDEO • 1h 20min • Uploaded Feb 18, 2024</div>
              <a href="#" class="download-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> Watch
                <i class="fas fa-arrow-right" style="font-size:11px;"></i>
              </a>
            </div>
          </div>
        </div>
      </section>

      <!-- ===================== DISCORD PAGE ===================== -->
      <section class="content-section" id="page-discord">
        <div class="discord-banner">
          <i class="fab fa-discord"></i>
          <h3>Join Our Discord Community</h3>
          <p>Stay connected with mentors and peers, ask questions, share your progress, and get help in real-time.</p>
          <a href="https://discord.gg/bAyM64DZvD" target="_blank" class="btn-discord">
            <i class="fab fa-discord"></i> Join Discord Server
          </a>
        </div>

        <div class="card" style="margin-top: 24px;">
          <div class="card-header">
            <h2><i class="fas fa-star"></i> Why Join?</h2>
          </div>
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon blue">
                <i class="fas fa-users"></i>
              </div>
              <h3>500+</h3>
              <p>Active Members</p>
            </div>
            <div class="stat-card">
              <div class="stat-icon green">
                <i class="fas fa-comments"></i>
              </div>
              <h3>24/7</h3>
              <p>Support Available</p>
            </div>
            <div class="stat-card">
              <div class="stat-icon purple">
                <i class="fas fa-chalkboard-teacher"></i>
              </div>
              <h3>Live</h3>
              <p>Mentor Sessions</p>
            </div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <!-- ═══════════════════════════════════════
       JAVASCRIPT
  ═══════════════════════════════════════ -->
  <script>
    // ── PAGE NAVIGATION ──
    function showPage(pageId, el) {
      // Hide all sections
      document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));

      // Show target section
      const target = document.getElementById('page-' + pageId);
      if (target) target.classList.add('active');

      // Update active link
      document.querySelectorAll('.sidebar-nav a').forEach(a => a.classList.remove('active'));
      if (el) el.classList.add('active');

      // Update topbar title
      const titles = {
        dashboard: 'Welcome <span>back!</span>',
        profile: 'My <span>Profile</span>',
        loi: 'Download <span>LOI</span>',
        editProfile: 'Edit <span>Profile</span>',
        studyMaterial: 'Study <span>Material</span>',
        discord: 'Join <span>Discord</span>'
      };
      document.querySelector('.topbar h1').innerHTML = titles[pageId] || 'Dashboard';

      // Close mobile sidebar
      document.getElementById('sidebar').classList.remove('open');
      document.getElementById('sidebarOverlay').classList.remove('active');

      // Load profile data when navigating to profile page
      if (pageId === 'profile' || pageId === 'dashboard') {
        loadProfile();
      }

      return false;
    }

    // ── MOBILE SIDEBAR TOGGLE ──
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('sidebarOverlay').classList.toggle('active');
    }

    // ── LOAD PROFILE DATA ──
    async function loadProfile() {
      try {
        const res = await fetch('get_profile.php');
        const student = await res.json();

        const initials = (student.first_name?.[0] || '') + (student.last_name?.[0] || '');
        const fullName = `${student.first_name} ${student.last_name}`;

        // Update profile page
        document.getElementById('profileAvatar').textContent = initials.toUpperCase();
        document.getElementById('profileFullName').textContent = fullName;
        if (student.email) document.getElementById('profileEmail').textContent = student.email;
        if (student.phone) document.getElementById('profilePhone').textContent = student.phone;
        if (student.dob) document.getElementById('profileDob').textContent = student.dob;
        if (student.address) document.getElementById('profileAddress').textContent = student.address;
        if (student.student_id) document.getElementById('profileStudentId').textContent = student.student_id;
        if (student.batch) document.getElementById('profileBatch').textContent = student.batch;

        // Update sidebar & topbar
        document.getElementById('sidebarAvatar').textContent = initials.toUpperCase();
        document.getElementById('sidebarUserName').textContent = fullName;
        document.getElementById('welcomeText').textContent = `Welcome back, ${student.first_name}! 👋`;

        // Fill edit form
        if (student.first_name) document.getElementById('editFirstName').value = student.first_name;
        if (student.last_name) document.getElementById('editLastName').value = student.last_name;
        if (student.email) document.getElementById('editEmail').value = student.email;
        if (student.phone) document.getElementById('editPhone').value = student.phone;
      } catch (e) {
        console.log('Profile fetch skipped (demo mode)');
      }
    }

    // ── EXAM FUNCTIONALITY ──
    let examTimer;
    let timeLeft = 30 * 60;

    async function startExam() {
      if (!document.getElementById('agree').checked) {
        showToast('Please agree to the terms and conditions before starting.', 'warning');
        return;
      }

      try {
        const res = await fetch('get_exam_questions.php');
        const questions = await res.json();

        let html = '';
        questions.forEach((q, i) => {
          html += `
            <div class="question-card">
              <div class="q-text">
                <span class="q-number">${i + 1}</span>
                ${q.question}
              </div>
              <label class="option-label">
                <input type="radio" name="q${q.id}" value="A"> ${q.option_a}
              </label>
              <label class="option-label">
                <input type="radio" name="q${q.id}" value="B"> ${q.option_b}
              </label>
              <label class="option-label">
                <input type="radio" name="q${q.id}" value="C"> ${q.option_c}
              </label>
              <label class="option-label">
                <input type="radio" name="q${q.id}" value="D"> ${q.option_d}
              </label>
            </div>
          `;
        });

        document.getElementById('examQuestions').innerHTML = html;
        document.getElementById('loiContent').style.display = 'none';
        document.getElementById('examContainer').style.display = 'block';
        document.getElementById('resultContainer').style.display = 'none';

        // Start timer
        timeLeft = 30 * 60;
        clearInterval(examTimer);
        examTimer = setInterval(updateTimer, 1000);
      } catch (e) {
        showToast('Could not load exam questions. Please try again.', 'error');
      }
    }

    function updateTimer() {
      let minutes = Math.floor(timeLeft / 60);
      let seconds = timeLeft % 60;
      document.getElementById('timerText').textContent =
        `${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
      timeLeft--;
      if (timeLeft < 0) {
        clearInterval(examTimer);
        submitExam();
      }
    }

    async function submitExam() {
      clearInterval(examTimer);

      const inputs = document.querySelectorAll('#examQuestions input[type="radio"]:checked');
      let answers = {};
      inputs.forEach(input => {
        const qId = input.name.substring(1);
        answers[qId] = input.value;
      });

      try {
        const res = await fetch('submit_exam.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ answers })
        });
        const result = await res.json();

        document.getElementById('examContainer').style.display = 'none';
        document.getElementById('resultContainer').style.display = 'block';

        if (result.eligible) {
          document.getElementById('resultContent').innerHTML = `
            <div class="result-card">
              <div class="result-icon success">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="score-display">${result.score} <span>/ 25</span></div>
              <h3 style="color: #10b981; margin-bottom: 8px;">Congratulations! 🎉</h3>
              <p style="color: var(--text-light); margin-bottom: 24px;">You have cleared the exam. You can now download your LOI.</p>
              <a href="download_loi.php" class="btn btn-success">
                <i class="fas fa-download"></i> Download LOI
              </a>
            </div>
          `;
        } else {
          document.getElementById('resultContent').innerHTML = `
            <div class="result-card">
              <div class="result-icon fail">
                <i class="fas fa-times-circle"></i>
              </div>
              <div class="score-display">${result.score} <span>/ 25</span></div>
              <h3 style="color: #ef4444; margin-bottom: 8px;">Not Cleared</h3>
              <p style="color: var(--text-light); margin-bottom: 24px;">You need at least 20 correct answers. Please retake the exam.</p>
              <button class="btn btn-primary" onclick="retakeExam()">
                <i class="fas fa-redo"></i> Retake Exam
              </button>
            </div>
          `;
        }
      } catch (e) {
        showToast('Could not submit exam. Please try again.', 'error');
      }
    }

    function retakeExam() {
      document.getElementById('loiContent').style.display = 'block';
      document.getElementById('examContainer').style.display = 'none';
      document.getElementById('resultContainer').style.display = 'none';
      document.getElementById('agree').checked = false;
    }

    // ── SAVE PROFILE ──
    async function saveProfile(e) {
      e.preventDefault();

      const data = {
        first_name: document.getElementById('editFirstName').value,
        last_name: document.getElementById('editLastName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        dob: document.getElementById('editDob').value,
        gender: document.getElementById('editGender').value,
        address: document.getElementById('editAddress').value,
        city: document.getElementById('editCity').value,
        state: document.getElementById('editState').value
      };

      try {
        const res = await fetch('update_profile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        const result = await res.json();

        if (result.success) {
          showToast('Profile updated successfully!', 'success');
          loadProfile();
          showPage('profile', document.querySelector('[data-page=profile]'));
        } else {
          showToast('Failed to update profile.', 'error');
        }
      } catch (e) {
        showToast('Profile saved locally (demo mode).', 'success');
        // Update UI locally for demo
        const fullName = data.first_name + ' ' + data.last_name;
        const initials = (data.first_name?.[0] || '') + (data.last_name?.[0] || '');
        document.getElementById('profileFullName').textContent = fullName;
        document.getElementById('profileAvatar').textContent = initials.toUpperCase();
        document.getElementById('profileEmail').textContent = data.email;
        document.getElementById('profilePhone').textContent = data.phone;
        document.getElementById('profileAddress').textContent = data.address;
        document.getElementById('sidebarUserName').textContent = fullName;
        document.getElementById('sidebarAvatar').textContent = initials.toUpperCase();
        document.getElementById('welcomeText').textContent = `Welcome back, ${data.first_name}! 👋`;
        showPage('profile', document.querySelector('[data-page=profile]'));
      }
    }

    // ── TOAST NOTIFICATION ──
    function showToast(message, type = 'info') {
      const toast = document.createElement('div');
      const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
      };
      const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
      };

      toast.innerHTML = `<i class="${icons[type]}"></i> ${message}`;
      Object.assign(toast.style, {
        position: 'fixed',
        top: '24px',
        right: '24px',
        background: colors[type],
        color: '#fff',
        padding: '14px 24px',
        borderRadius: '12px',
        fontSize: '14px',
        fontWeight: '600',
        fontFamily: 'Inter, sans-serif',
        zIndex: '9999',
        display: 'flex',
        alignItems: 'center',
        gap: '10px',
        boxShadow: '0 8px 30px rgba(0,0,0,0.2)',
        animation: 'fadeInUp 0.4s ease',
        maxWidth: '400px'
      });

      document.body.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }

    // ── SET CURRENT DATE ──
    const now = new Date();
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    document.getElementById('statDate').textContent = `${months[now.getMonth()]} ${now.getFullYear()}`;

    // ── INITIAL LOAD ──
    loadProfile();
  </script>

</body>
</html>