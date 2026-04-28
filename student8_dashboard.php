<?php
session_start();
require_once 'db.php';
$conn = get_db_connection();
$studentId = $_SESSION['student_id'] ?? 0;

if (!$studentId) {
  header("Location: login.php");
  exit;
}

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$student) {
  header("Location: login.php");
  exit;
}

$batchId = $student['batch_id'] ?? 0;
$materials = [];
try {
  $stmtMat = $conn->prepare("SELECT * FROM study_material WHERE batch_id = ? ORDER BY uploaded_at DESC");
  $stmtMat->execute([$batchId]);
  $materials = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

$initials = strtoupper(substr($student['first_name'] ?? 'S', 0, 1) . substr($student['last_name'] ?? '', 0, 1));
$fullName = htmlspecialchars(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''));
$hasProfilePic = !empty($student['profile_pic']) && file_exists($student['profile_pic']);
$hasCv = !empty($student['cv']) && file_exists($student['cv']);

$savedMsg = isset($_GET['saved']) ? 'Profile updated successfully!' : '';
$defaultPage = $_GET['page'] ?? 'dashboard';

function getFileIcon($type)
{
  $t = strtolower($type ?? '');
  if (strpos($t, 'pdf') !== false)
    return ['fas fa-file-pdf', 'pdf'];
  if (strpos($t, 'doc') !== false)
    return ['fas fa-file-word', 'doc'];
  if (strpos($t, 'ppt') !== false)
    return ['fas fa-file-powerpoint', 'ppt'];
  if (strpos($t, 'xls') !== false)
    return ['fas fa-file-excel', 'excel'];
  if (strpos($t, 'video') !== false || strpos($t, 'mp4') !== false)
    return ['fas fa-play-circle', 'video'];
  if (strpos($t, 'image') !== false || strpos($t, 'jpg') !== false || strpos($t, 'png') !== false)
    return ['fas fa-image', 'img'];
  return ['fas fa-file', 'doc'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="icon" href="https://internboot.com/img/logos/favicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
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
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
      --shadow-md: 0 4px 20px rgba(30, 64, 175, 0.1);
      --shadow-lg: 0 10px 40px rgba(30, 64, 175, 0.15);
      --radius: 16px;
      --radius-sm: 10px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    html,
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text-dark);
      height: 100vh;
      overflow: hidden;
    }

    body {
      display: flex;
    }

    /* ═══ SIDEBAR ═══ */
    .sidebar {
      width: 280px;
      height: 100vh;
      background: var(--primary-gradient);
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      z-index: 1000;
      display: flex;
      flex-direction: column;
      transition: var(--transition);
      box-shadow: 4px 0 30px rgba(30, 64, 175, 0.3);
      overflow: hidden;
    }

    .sidebar-header {
      padding: 28px 24px 24px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      flex-shrink: 0;
    }

    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .sidebar-logo .logo-icon {
      width: 46px;
      height: 46px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 13px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 21px;
      color: #fff;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-logo h2 {
      color: #fff;
      font-size: 19px;
      font-weight: 700;
      letter-spacing: -0.3px;
    }

    .sidebar-logo span {
      color: rgba(255, 255, 255, 0.6);
      font-size: 11.5px;
      font-weight: 400;
      display: block;
      margin-top: 2px;
    }

    /* SIDEBAR SCROLL AREA */
    .sidebar-scroll {
      flex: 1;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 16px 0;
    }

    .sidebar-scroll::-webkit-scrollbar {
      width: 5px;
    }

    .sidebar-scroll::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.03);
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.18);
      border-radius: 10px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.3);
    }

    .sidebar-nav {
      padding: 0 14px;
    }

    .nav-label {
      color: rgba(255, 255, 255, 0.38);
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.3px;
      padding: 0 12px;
      margin-bottom: 10px;
      margin-top: 18px;
    }

    .nav-label:first-child {
      margin-top: 0;
    }

    .sidebar-nav ul {
      list-style: none;
    }

    .sidebar-nav ul li {
      margin-bottom: 3px;
    }

    .sidebar-nav ul li a {
      display: flex;
      align-items: center;
      gap: 13px;
      padding: 12px 15px;
      color: rgba(255, 255, 255, 0.65);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      border-radius: 11px;
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
      background: rgba(255, 255, 255, 0.1);
      opacity: 0;
      transition: var(--transition);
      border-radius: 11px;
    }

    .sidebar-nav ul li a:hover::before {
      opacity: 1;
    }

    .sidebar-nav ul li a:hover {
      color: #fff;
      transform: translateX(3px);
    }

    .sidebar-nav ul li a.active {
      background: rgba(255, 255, 255, 0.18);
      color: #fff;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .sidebar-nav ul li a.active::after {
      content: '';
      position: absolute;
      right: 13px;
      width: 6px;
      height: 6px;
      background: #60a5fa;
      border-radius: 50%;
      box-shadow: 0 0 10px #60a5fa;
    }

    .sidebar-nav ul li a i {
      font-size: 17px;
      width: 22px;
      text-align: center;
      flex-shrink: 0;
    }

    .sidebar-footer {
      padding: 16px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      flex-shrink: 0;
    }

    .sidebar-footer .user-card {
      display: flex;
      align-items: center;
      gap: 11px;
      padding: 11px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 11px;
      backdrop-filter: blur(10px);
    }

    .sidebar-footer .user-avatar {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      font-size: 15px;
      flex-shrink: 0;
      overflow: hidden;
    }

    .sidebar-footer .user-avatar.gradient-bg {
      background: linear-gradient(135deg, #60a5fa, #a78bfa);
    }

    .sidebar-footer .user-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .sidebar-footer .user-info {
      overflow: hidden;
    }

    .sidebar-footer .user-info h4 {
      color: #fff;
      font-size: 13px;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .sidebar-footer .user-info p {
      color: rgba(255, 255, 255, 0.5);
      font-size: 11px;
      margin-top: 1px;
    }

    .logout-btn {
      display: flex;
      align-items: center;
      gap: 13px;
      padding: 12px 15px;
      color: rgba(255, 255, 255, 0.55);
      text-decoration: none;
      font-size: 13.5px;
      font-weight: 500;
      border-radius: 11px;
      transition: var(--transition);
      margin-top: 8px;
      border: none;
      background: none;
      cursor: pointer;
      width: 100%;
    }

    .logout-btn:hover {
      background: rgba(239, 68, 68, 0.2);
      color: #fca5a5;
    }

    /* ═══ MAIN CONTENT ═══ */
    .main-content {
      margin-left: 280px;
      flex: 1;
      height: 100vh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    /* TOP BAR */
    .topbar {
      padding: 18px 36px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--border);
      flex-shrink: 0;
      background: rgba(255, 255, 255, 0.92);
      backdrop-filter: blur(20px);
      z-index: 100;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 21px;
      color: var(--text-dark);
      cursor: pointer;
      padding: 7px;
      border-radius: 8px;
      transition: var(--transition);
    }

    .mobile-toggle:hover {
      background: var(--bg);
    }

    .topbar h1 {
      font-size: 21px;
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
      gap: 10px;
    }

    .topbar-btn {
      width: 40px;
      height: 40px;
      border-radius: 11px;
      border: 1px solid var(--border);
      background: var(--card-bg);
      color: var(--text-medium);
      font-size: 16px;
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
      width: 17px;
      height: 17px;
      background: #ef4444;
      color: #fff;
      font-size: 9px;
      font-weight: 700;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid #fff;
    }

    /* ═══ PAGE CONTENT CONTAINER ═══ */
    .page-content {
      flex: 1;
      min-height: 0;
      position: relative;
      overflow: hidden;
    }

    /* EACH PAGE SECTION — independent scroll */
    .content-section {
      display: none;
      position: absolute;
      inset: 0;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 30px 36px 50px;
    }

    .content-section.active {
      display: block;
    }

    /* Per-page scrollbar */
    .content-section::-webkit-scrollbar {
      width: 7px;
    }

    .content-section::-webkit-scrollbar-track {
      background: #f1f5f9;
      border-radius: 10px;
    }

    .content-section::-webkit-scrollbar-thumb {
      background: #c7d2e0;
      border-radius: 10px;
    }

    .content-section::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    /* ═══ ANIMATIONS ═══ */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(18px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .content-section.active {
      animation: fadeInUp .45s ease;
    }

    /* ═══ WELCOME BANNER ═══ */
    .welcome-banner {
      background: var(--primary-gradient);
      border-radius: var(--radius);
      padding: 38px;
      color: #fff;
      position: relative;
      overflow: hidden;
      margin-bottom: 28px;
    }

    .welcome-banner::before {
      content: '';
      position: absolute;
      right: -60px;
      top: -60px;
      width: 240px;
      height: 240px;
      background: rgba(255, 255, 255, 0.07);
      border-radius: 50%;
    }

    .welcome-banner::after {
      content: '';
      position: absolute;
      right: 80px;
      bottom: -40px;
      width: 140px;
      height: 140px;
      background: rgba(255, 255, 255, 0.04);
      border-radius: 50%;
    }

    .welcome-banner h2 {
      font-size: 26px;
      font-weight: 700;
      margin-bottom: 7px;
      position: relative;
      z-index: 1;
    }

    .welcome-banner p {
      font-size: 14.5px;
      opacity: .82;
      position: relative;
      z-index: 1;
      max-width: 480px;
      line-height: 1.6;
    }

    .welcome-banner .welcome-icon {
      position: absolute;
      right: 38px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 75px;
      opacity: .13;
      z-index: 0;
    }

    /* ═══ STATS ═══ */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 18px;
      margin-bottom: 28px;
    }

    .stat-card {
      background: var(--card-bg);
      border-radius: var(--radius);
      padding: 26px;
      border: 1px solid var(--border);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .stat-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-lg);
      border-color: transparent;
    }

    .stat-card .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 13px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 21px;
      margin-bottom: 16px;
    }

    .stat-card .stat-icon.blue {
      background: rgba(59, 130, 246, 0.1);
      color: var(--primary-light);
    }

    .stat-card .stat-icon.green {
      background: rgba(16, 185, 129, 0.1);
      color: #10b981;
    }

    .stat-card .stat-icon.purple {
      background: rgba(139, 92, 246, 0.1);
      color: #8b5cf6;
    }

    .stat-card .stat-icon.orange {
      background: rgba(245, 158, 11, 0.1);
      color: #f59e0b;
    }

    .stat-card h3 {
      font-size: 26px;
      font-weight: 800;
      color: var(--text-dark);
      margin-bottom: 3px;
    }

    .stat-card p {
      font-size: 12.5px;
      color: var(--text-light);
      font-weight: 500;
    }

    /* ═══ CARD ═══ */
    .card {
      background: var(--card-bg);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      padding: 30px;
      margin-bottom: 22px;
      transition: var(--transition);
    }

    .card:hover {
      box-shadow: var(--shadow-md);
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 22px;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--border);
      flex-wrap: wrap;
      gap: 12px;
    }

    .card-header h2 {
      font-size: 19px;
      font-weight: 700;
      color: var(--text-dark);
      display: flex;
      align-items: center;
      gap: 9px;
    }

    .card-header h2 i {
      color: var(--primary-light);
    }

    /* ═══ PROFILE ═══ */
    .profile-grid {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 30px;
    }

    .profile-left {
      text-align: center;
    }

    .profile-avatar-large {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 44px;
      color: #fff;
      font-weight: 700;
      margin: 0 auto 18px;
      box-shadow: 0 8px 28px rgba(59, 130, 246, 0.28);
      border: 4px solid #fff;
      position: relative;
      overflow: hidden;
    }

    .profile-avatar-large.gradient-bg {
      background: var(--primary-gradient);
    }

    .profile-avatar-large img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-avatar-large .online-dot {
      position: absolute;
      bottom: 6px;
      right: 6px;
      width: 18px;
      height: 18px;
      background: #10b981;
      border-radius: 50%;
      border: 3px solid #fff;
    }

    .profile-name {
      font-size: 21px;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 3px;
    }

    .profile-role {
      font-size: 13.5px;
      color: var(--text-light);
      margin-bottom: 18px;
    }

    .profile-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 14px;
      background: rgba(16, 185, 129, 0.1);
      color: #10b981;
      border-radius: 18px;
      font-size: 12.5px;
      font-weight: 600;
    }

    .profile-section-title {
      font-size: 13px;
      font-weight: 700;
      color: var(--primary);
      text-transform: uppercase;
      letter-spacing: .8px;
      margin: 24px 0 12px;
      padding-bottom: 8px;
      border-bottom: 2px solid rgba(59, 130, 246, 0.1);
    }

    .profile-section-title:first-child {
      margin-top: 0;
    }

    .detail-row {
      display: flex;
      align-items: center;
      padding: 14px 0;
      border-bottom: 1px solid var(--border);
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-row .detail-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: rgba(59, 130, 246, 0.07);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary-light);
      font-size: 15px;
      margin-right: 14px;
      flex-shrink: 0;
    }

    .detail-row .detail-label {
      font-size: 11.5px;
      color: var(--text-light);
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: .4px;
    }

    .detail-row .detail-value {
      font-size: 14.5px;
      color: var(--text-dark);
      font-weight: 600;
      margin-top: 1px;
      word-break: break-all;
    }

    .detail-row .detail-value a {
      color: var(--primary-light);
      text-decoration: none;
      transition: var(--transition);
    }

    .detail-row .detail-value a:hover {
      color: var(--primary);
      text-decoration: underline;
    }

    /* ═══ FORM ═══ */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .form-group {
      margin-bottom: 2px;
    }

    .form-group.full-width {
      grid-column: 1/-1;
    }

    .form-group label {
      display: block;
      font-size: 12.5px;
      font-weight: 600;
      color: var(--text-medium);
      margin-bottom: 7px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 11px 15px;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      font-size: 13.5px;
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
      box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 90px;
    }

    .form-actions {
      display: flex;
      gap: 11px;
      margin-top: 22px;
      flex-wrap: wrap;
    }

    /* FILE UPLOAD */
    .file-upload-area {
      border: 2px dashed var(--border);
      border-radius: var(--radius-sm);
      padding: 20px;
      text-align: center;
      cursor: pointer;
      transition: var(--transition);
      background: #fafbff;
    }

    .file-upload-area:hover {
      border-color: var(--primary-light);
      background: #f0f4ff;
    }

    .file-upload-area i {
      font-size: 28px;
      color: var(--primary-light);
      margin-bottom: 8px;
      display: block;
    }

    .file-upload-area span {
      font-size: 13px;
      color: var(--text-light);
      display: block;
    }

    .file-upload-area .file-name {
      font-size: 12.5px;
      color: var(--primary);
      font-weight: 600;
      margin-top: 8px;
      display: none;
    }

    .file-upload-area input[type="file"] {
      display: none;
    }

    .current-file {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 8px;
      padding: 5px 12px;
      background: rgba(59, 130, 246, 0.08);
      border-radius: 6px;
      font-size: 12px;
      color: var(--primary);
    }

    .current-file a {
      color: var(--primary-light);
      text-decoration: none;
      font-weight: 600;
    }

    .current-file a:hover {
      text-decoration: underline;
    }

    .profile-pic-preview {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      object-fit: cover;
      margin-top: 10px;
      border: 3px solid var(--primary-light);
      display: none;
    }

    /* ═══ BUTTONS ═══ */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 11px 26px;
      border: none;
      border-radius: var(--radius-sm);
      font-size: 13.5px;
      font-weight: 600;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      transition: var(--transition);
      text-decoration: none;
    }

    .btn-primary {
      background: var(--primary-gradient);
      color: #fff;
      box-shadow: 0 4px 14px rgba(30, 64, 175, 0.28);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(30, 64, 175, 0.38);
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
      box-shadow: 0 4px 14px rgba(16, 185, 129, 0.28);
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(16, 185, 129, 0.38);
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc2626, #ef4444);
      color: #fff;
      box-shadow: 0 4px 14px rgba(239, 68, 68, 0.28);
    }

    .btn-danger:hover {
      transform: translateY(-2px);
    }

    /* ═══ LOI ═══ */
    .loi-container {
      text-align: center;
      padding: 36px 18px;
    }

    .loi-icon {
      width: 96px;
      height: 96px;
      background: rgba(59, 130, 246, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 38px;
      color: var(--primary-light);
      margin: 0 auto 22px;
    }

    .loi-container h3 {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 9px;
    }

    .loi-container p {
      color: var(--text-light);
      font-size: 14.5px;
      max-width: 440px;
      margin: 0 auto 14px;
      line-height: 1.6;
    }

    .exam-instructions {
      text-align: left;
      background: #f0f4ff;
      border-radius: var(--radius-sm);
      padding: 22px;
      margin: 22px 0;
    }

    .exam-instructions h4 {
      font-size: 15px;
      font-weight: 700;
      margin-bottom: 12px;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 7px;
    }

    .exam-instructions ul {
      list-style: none;
      padding: 0;
    }

    .exam-instructions ul li {
      padding: 7px 0;
      font-size: 13.5px;
      color: var(--text-medium);
      display: flex;
      align-items: flex-start;
      gap: 9px;
    }

    .exam-instructions ul li::before {
      content: '✓';
      color: var(--primary-light);
      font-weight: 700;
      flex-shrink: 0;
      width: 21px;
      height: 21px;
      background: rgba(59, 130, 246, 0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
    }

    .agree-box {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 14px 18px;
      background: #fff;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      margin: 18px 0;
      cursor: pointer;
      transition: var(--transition);
    }

    .agree-box:hover {
      border-color: var(--primary-light);
    }

    .agree-box input[type="checkbox"] {
      width: 19px;
      height: 19px;
      accent-color: var(--primary);
    }

    .agree-box label {
      font-size: 13.5px;
      color: var(--text-medium);
      cursor: pointer;
    }

    /* EXAM */
    .exam-timer {
      background: linear-gradient(135deg, #dc2626, #ef4444);
      color: #fff;
      padding: 10px 22px;
      border-radius: var(--radius-sm);
      display: inline-flex;
      align-items: center;
      gap: 9px;
      font-size: 17px;
      font-weight: 700;
      box-shadow: 0 4px 14px rgba(239, 68, 68, 0.28);
    }

    .question-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 22px;
      margin-bottom: 14px;
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
      width: 30px;
      height: 30px;
      background: var(--primary-gradient);
      color: #fff;
      border-radius: 7px;
      font-size: 12px;
      font-weight: 700;
      margin-right: 9px;
    }

    .question-card .q-text {
      font-size: 14.5px;
      font-weight: 600;
      color: var(--text-dark);
      margin-bottom: 14px;
    }

    .option-label {
      display: flex;
      align-items: center;
      gap: 11px;
      padding: 9px 13px;
      border: 1px solid var(--border);
      border-radius: 7px;
      margin-bottom: 7px;
      cursor: pointer;
      transition: var(--transition);
      font-size: 13.5px;
      color: var(--text-medium);
    }

    .option-label:hover {
      background: #f0f4ff;
      border-color: var(--primary-light);
    }

    .option-label input[type="radio"] {
      accent-color: var(--primary);
    }

    .result-card {
      text-align: center;
      padding: 36px;
    }

    .result-icon {
      width: 96px;
      height: 96px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 42px;
      margin: 0 auto 18px;
    }

    .result-icon.success {
      background: rgba(16, 185, 129, 0.1);
      color: #10b981;
    }

    .result-icon.fail {
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
    }

    .score-display {
      font-size: 46px;
      font-weight: 800;
      color: var(--text-dark);
      margin-bottom: 7px;
    }

    .score-display span {
      font-size: 22px;
      color: var(--text-light);
    }

    /* ═══ STUDY MATERIAL ═══ */
    .material-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
      gap: 18px;
    }

    .material-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 22px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .material-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-lg);
      border-color: transparent;
    }

    .material-card .file-icon {
      width: 54px;
      height: 54px;
      border-radius: 13px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 23px;
      margin-bottom: 14px;
    }

    .material-card .file-icon.pdf {
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
    }

    .material-card .file-icon.video {
      background: rgba(139, 92, 246, 0.1);
      color: #8b5cf6;
    }

    .material-card .file-icon.doc {
      background: rgba(59, 130, 246, 0.1);
      color: var(--primary-light);
    }

    .material-card .file-icon.img {
      background: rgba(16, 185, 129, 0.1);
      color: #10b981;
    }

    .material-card .file-icon.ppt {
      background: rgba(245, 158, 11, 0.1);
      color: #f59e0b;
    }

    .material-card .file-icon.excel {
      background: rgba(16, 185, 129, 0.1);
      color: #059669;
    }

    .material-card h4 {
      font-size: 14.5px;
      font-weight: 600;
      margin-bottom: 5px;
      color: var(--text-dark);
    }

    .material-card .file-meta {
      font-size: 11.5px;
      color: var(--text-light);
      margin-bottom: 14px;
    }

    .material-card .download-link {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 12.5px;
      font-weight: 600;
      color: var(--primary-light);
      text-decoration: none;
      transition: var(--transition);
    }

    .material-card .download-link:hover {
      color: var(--primary);
      gap: 9px;
    }

    .empty-state {
      text-align: center;
      padding: 50px 20px;
      color: var(--text-light);
    }

    .empty-state i {
      font-size: 48px;
      margin-bottom: 14px;
      display: block;
      opacity: .25;
    }

    .empty-state p {
      font-size: 14px;
    }

    /* ═══ DISCORD ═══ */
    .discord-banner {
      background: linear-gradient(135deg, #5865F2, #7289da);
      border-radius: var(--radius);
      padding: 38px;
      color: #fff;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .discord-banner::before {
      content: '';
      position: absolute;
      width: 190px;
      height: 190px;
      background: rgba(255, 255, 255, 0.07);
      border-radius: 50%;
      top: -50px;
      right: -50px;
    }

    .discord-banner i.fab.fa-discord {
      font-size: 46px;
      margin-bottom: 14px;
      opacity: .9;
    }

    .discord-banner h3 {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 9px;
    }

    .discord-banner p {
      font-size: 14.5px;
      opacity: .82;
      margin-bottom: 22px;
      max-width: 380px;
      margin-left: auto;
      margin-right: auto;
    }

    .btn-discord {
      background: #fff;
      color: #5865F2;
      padding: 13px 30px;
      border-radius: var(--radius-sm);
      font-weight: 700;
      font-size: 14.5px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      transition: var(--transition);
    }

    .btn-discord:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
    }

    /* ═══ LOADING ═══ */
    .btn.loading {
      pointer-events: none;
      opacity: .7;
    }

    .btn.loading::after {
      content: '';
      width: 16px;
      height: 16px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .6s linear infinite;
      margin-left: 6px;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    /* ═══ OVERLAY / MOBILE ═══ */
    .sidebar-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    @media(max-width:1024px) {
      .profile-grid {
        grid-template-columns: 1fr;
      }
    }

    @media(max-width:768px) {
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
        padding: 14px 18px;
      }

      .content-section {
        padding: 18px;
      }

      .welcome-banner {
        padding: 26px;
      }

      .welcome-banner h2 {
        font-size: 20px;
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

      .profile-grid {
        grid-template-columns: 1fr;
      }
    }

    @media(max-width:480px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
    }

    .btn-discord {
      background: #5865F2;
      color: #fff;
      font-weight: 600;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .btn-discord:hover {
      background: #4752c4;
      transform: translateY(-2px);
    }
  </style>
</head>

<body>

  <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

  <!-- ═══ SIDEBAR ═══ -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <div class="logo-icon" style="background-color: white;"><img src="favicon.ico" alt="Logo"
            style="height: 100%; width: 100%;"></i></div>
        <div>
          <h2>Internboot</h2><span>Student Dashboard</span>
        </div>
      </div>
    </div>

    <div class="sidebar-scroll">
      <nav class="sidebar-nav">
        <p class="nav-label">Main Menu</p>
        <ul>
          <li><a href="#" class="<?= $defaultPage === 'dashboard' ? 'active' : '' ?>" data-page="dashboard"
              onclick="return showPage('dashboard',this)"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li><a href="#" class="<?= $defaultPage === 'profile' ? 'active' : '' ?>" data-page="profile"
              onclick="return showPage('profile',this)"><i class="fas fa-user-circle"></i> My Profile</a></li>
          <li><a href="#" class="<?= $defaultPage === 'loi' ? 'active' : '' ?>" data-page="loi"
              onclick="return showPage('loi',this)"><i class="fas fa-file-download"></i> Download LOI</a></li>
          <li><a href="#" class="<?= $defaultPage === 'editProfile' ? 'active' : '' ?>" data-page="editProfile"
              onclick="return showPage('editProfile',this)"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
          <li>
            <a href="help_support.php">
              <i class="fas fa-headset"></i> Help & Support
            </a>
          </li>

        </ul>
        <p class="nav-label">Resources</p>
        <ul>
          <li><a href="#" class="<?= $defaultPage === 'studyMaterial' ? 'active' : '' ?>" data-page="studyMaterial"
              onclick="return showPage('studyMaterial',this)"><i class="fas fa-book-open"></i> Study Material</a></li>
          <li><a href="https://discord.gg/XfF7YMbvBN" class="<?= $defaultPage === 'discord' ? 'active' : '' ?>"
              data-page="discord" onclick="return showPage('discord',this)"><i class="fab fa-discord"></i> Join
              Discord</a></li>
          <li>
            <a href="#" class="<?= $defaultPage === 'certificate' ? 'active' : '' ?>" data-page="certificate"
              onclick="return showPage('certificate',this)">
              <i class="fas fa-certificate"></i> Download Certificate
            </a>
          </li>

        </ul>
      </nav>
    </div>

    <div class="sidebar-footer">
      <div class="user-card">
        <div class="user-avatar <?= $hasProfilePic ? '' : 'gradient-bg' ?>">
          <?php if ($hasProfilePic): ?>
            <img src="<?= htmlspecialchars($student['profile_pic']) ?>" alt="Avatar">
          <?php else: ?>
            <?= $initials ?>
          <?php endif; ?>
        </div>
        <div class="user-info">
          <h4><?= $fullName ?></h4>
          <p>Student</p>
        </div>
      </div>
      <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Log Out</a>
    </div>
  </aside>

  <!-- ═══ MAIN ═══ -->
  <main class="main-content">

    <header class="topbar">
      <div class="topbar-left">
        <button class="mobile-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <h1 id="topbarTitle">Welcome <span>back!</span></h1>
      </div>
      <!--<div class="topbar-right">-->
      <!--  <button class="topbar-btn" title="Notifications"><i class="fas fa-bell"></i><span-->
      <!--      class="badge"><?= count($materials) ?></span></button>-->
      <!--  <button class="topbar-btn" title="Messages"><i class="fas fa-envelope"></i></button>-->
      <!--</div>-->
    </header>

    <div class="page-content">

      <!-- ════════ DASHBOARD ════════ -->
      <section class="content-section <?= $defaultPage === 'dashboard' ? 'active' : '' ?>" id="page-dashboard">
        <div class="welcome-banner">
          <h2>Welcome back, <?= htmlspecialchars($student['first_name'] ?? 'Student') ?>! 👋</h2>
          <p>Track your progress, download your LOI, access study materials, and manage your profile all in one place.
          </p>
          <i class="fas fa-rocket welcome-icon"></i>
        </div>
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-book"></i></div>
            <h3><?= count($materials) ?></h3>
            <p>Study Materials</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <h3><?= htmlspecialchars($student['internship_category'] ?? '—') ?></h3>
            <p>Internship Category</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-clock"></i></div>
            <h3><?= htmlspecialchars($student['internship_tenure'] ?? '—') ?></h3>
            <p>Internship Tenure</p>
          </div>
          <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-calendar-check"></i></div>
            <h3><?= htmlspecialchars($student['internship_start_date'] ?? '—') ?></h3>
            <p>Start Date</p>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-clock"></i> Quick Actions</h2>
          </div>
          <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button class="btn btn-primary" onclick="showPage('profile',document.querySelector('[data-page=profile]'))">
              <i class="fas fa-user"></i> View Profile
            </button>

            <button class="btn btn-success"
              onclick="showPage('studyMaterial',document.querySelector('[data-page=studyMaterial]'))">
              <i class="fas fa-book-open"></i> Study Material
            </button>

            <button class="btn btn-secondary"
              onclick="showPage('editProfile',document.querySelector('[data-page=editProfile]'))">
              <i class="fas fa-edit"></i> Edit Profile
            </button>

            <!-- 🚨 New Discord Button -->
            <a href="https://discord.gg/XfF7YMbvBN" target="_blank" class="btn btn-discord">
              <i class="fab fa-discord"></i> Join Discord (Mandatory)
            </a>
          </div>
        </div>
      </section>

      <!-- ════════ PROFILE ════════ -->
      <section class="content-section <?= $defaultPage === 'profile' ? 'active' : '' ?>" id="page-profile">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-user-circle"></i> My Profile</h2>
            <button class="btn btn-primary"
              onclick="showPage('editProfile',document.querySelector('[data-page=editProfile]'))"><i
                class="fas fa-edit"></i> Edit Profile</button>
          </div>
          <div class="profile-grid">
            <div class="profile-left">
              <div class="profile-avatar-large <?= $hasProfilePic ? '' : 'gradient-bg' ?>">
                <?php if ($hasProfilePic): ?>
                  <img src="<?= htmlspecialchars($student['profile_pic']) ?>" alt="Profile">
                <?php else: ?>
                  <?= $initials ?>
                <?php endif; ?>
                <span class="online-dot"></span>
              </div>
              <div class="profile-name"><?= $fullName ?></div>
              <div class="profile-role"><?= htmlspecialchars($student['internship_category'] ?? 'Student') ?></div>
              <div class="profile-badge"><i class="fas fa-check-circle"></i> Active</div>

              <?php if ($hasCv): ?>
                <div style="margin-top:18px;">
                  <a href="<?= htmlspecialchars($student['cv']) ?>" target="_blank" class="btn btn-secondary"
                    style="font-size:12px;padding:8px 16px;">
                    <i class="fas fa-file-pdf"></i> View Resume
                  </a>
                </div>
              <?php endif; ?>
            </div>

            <div class="profile-details">
              <div class="profile-section-title">Personal Information</div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-envelope"></i></div>
                <div>
                  <div class="detail-label">Email</div>
                  <div class="detail-value"><?= htmlspecialchars($student['email'] ?? '—') ?></div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-phone"></i></div>
                <div>
                  <div class="detail-label">Mobile</div>
                  <div class="detail-value"><?= htmlspecialchars($student['mobile'] ?? '—') ?></div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fab fa-whatsapp"></i></div>
                <div>
                  <div class="detail-label">WhatsApp</div>
                  <div class="detail-value"><?= htmlspecialchars($student['whatsapp'] ?? '—') ?></div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-id-card"></i></div>
                <div>
                  <div class="detail-label">Username</div>
                  <div class="detail-value"><?= htmlspecialchars($student['username'] ?? '—') ?></div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-fingerprint"></i></div>
                <div>
                  <div class="detail-label">Aadhar Number</div>
                  <div class="detail-value"><?= htmlspecialchars($student['aadhar_number'] ?? '—') ?></div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-briefcase"></i></div>
                <div>
                  <div class="detail-label">Employment Status</div>
                  <div class="detail-value"><?= htmlspecialchars($student['employment_status'] ?? '—') ?></div>
                </div>
              </div>

              <div class="profile-section-title">Internship Details</div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-layer-group"></i></div>
                <div>
                  <div class="detail-label">Internship Category</div>
                  <div class="detail-value"><?= htmlspecialchars($student['internship_category'] ?? '—') ?></div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-hourglass-half"></i></div>
                <div>
                  <div class="detail-label">Internship Tenure</div>
                  <div class="detail-value"><?= htmlspecialchars($student['internship_tenure'] ?? '—') ?></div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-calendar-alt"></i></div>
                <div>
                  <div class="detail-label">Start Date</div>
                  <div class="detail-value"><?= htmlspecialchars($student['internship_start_date'] ?? '—') ?></div>
                </div>
              </div>

              <div class="profile-section-title">Social & Skills</div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fab fa-linkedin"></i></div>
                <div>
                  <div class="detail-label">LinkedIn</div>
                  <div class="detail-value">
                    <?php if (!empty($student['linkedin_id'])): ?>
                      <a href="<?= htmlspecialchars($student['linkedin_id']) ?>"
                        target="_blank"><?= htmlspecialchars($student['linkedin_id']) ?></a>
                    <?php else: ?>—<?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fab fa-instagram"></i></div>
                <div>
                  <div class="detail-label">Instagram</div>
                  <div class="detail-value">
                    <?php if (!empty($student['instagram_id'])): ?>
                      <a href="https://instagram.com/<?= htmlspecialchars($student['instagram_id']) ?>"
                        target="_blank">@<?= htmlspecialchars($student['instagram_id']) ?></a>
                    <?php else: ?>—<?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="detail-row">
                <div class="detail-icon"><i class="fas fa-lightbulb"></i></div>
                <div>
                  <div class="detail-label">Extra Skills</div>
                  <div class="detail-value"><?= htmlspecialchars($student['extra_skills'] ?? '—') ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ════════ EDIT PROFILE ════════ -->
      <section class="content-section <?= $defaultPage === 'editProfile' ? 'active' : '' ?>" id="page-editProfile">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
            <button class="btn btn-secondary"
              onclick="showPage('profile',document.querySelector('[data-page=profile]'))"><i
                class="fas fa-arrow-left"></i> Back to Profile</button>
          </div>
          <form id="editProfileForm" onsubmit="saveProfile(event)" enctype="multipart/form-data">
            <div class="form-grid">

              <div class="form-group">
                <label for="ef_first_name"><i class="fas fa-user"
                    style="margin-right:4px;color:var(--primary-light);"></i> First Name</label>
                <input type="text" id="ef_first_name" name="first_name"
                  value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label for="ef_last_name"><i class="fas fa-user"
                    style="margin-right:4px;color:var(--primary-light);"></i> Last Name</label>
                <input type="text" id="ef_last_name" name="last_name"
                  value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
              </div>
              <div class="form-group">
                <label for="ef_username"><i class="fas fa-at" style="margin-right:4px;color:var(--primary-light);"></i>
                  Username</label>
                <input type="text" id="ef_username" name="username"
                  value="<?= htmlspecialchars($student['username'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_email"><i class="fas fa-envelope"
                    style="margin-right:4px;color:var(--primary-light);"></i> Email</label>
                <input type="email" id="ef_email" name="email" value="<?= htmlspecialchars($student['email'] ?? '') ?>"
                  required>
              </div>
              <div class="form-group">
                <label for="ef_mobile"><i class="fas fa-phone" style="margin-right:4px;color:var(--primary-light);"></i>
                  Mobile</label>
                <input type="text" id="ef_mobile" name="mobile"
                  value="<?= htmlspecialchars($student['mobile'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_whatsapp"><i class="fab fa-whatsapp" style="margin-right:4px;color:#25D366;"></i>
                  WhatsApp</label>
                <input type="text" id="ef_whatsapp" name="whatsapp"
                  value="<?= htmlspecialchars($student['whatsapp'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_employment"><i class="fas fa-briefcase"
                    style="margin-right:4px;color:var(--primary-light);"></i> Employment Status</label>
                <input type="text" id="ef_employment" name="employment_status"
                  value="<?= htmlspecialchars($student['employment_status'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_category"><i class="fas fa-layer-group"
                    style="margin-right:4px;color:var(--primary-light);"></i> Internship Category</label>
                <input type="text" id="ef_category" name="internship_category"
                  value="<?= htmlspecialchars($student['internship_category'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_tenure"><i class="fas fa-hourglass-half"
                    style="margin-right:4px;color:var(--primary-light);"></i> Internship Tenure</label>
                <input type="text" id="ef_tenure" name="internship_tenure"
                  value="<?= htmlspecialchars($student['internship_tenure'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_startdate"><i class="fas fa-calendar-alt"
                    style="margin-right:4px;color:var(--primary-light);"></i> Internship Start Date</label>
                <input type="date" id="ef_startdate" name="internship_start_date"
                  value="<?= htmlspecialchars($student['internship_start_date'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_aadhar"><i class="fas fa-fingerprint"
                    style="margin-right:4px;color:var(--primary-light);"></i> Aadhar Number</label>
                <input type="text" id="ef_aadhar" name="aadhar_number"
                  value="<?= htmlspecialchars($student['aadhar_number'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_linkedin"><i class="fab fa-linkedin" style="margin-right:4px;color:#0A66C2;"></i>
                  LinkedIn ID</label>
                <input type="text" id="ef_linkedin" name="linkedin_id" placeholder="https://linkedin.com/in/yourprofile"
                  value="<?= htmlspecialchars($student['linkedin_id'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label for="ef_instagram"><i class="fab fa-instagram" style="margin-right:4px;color:#E4405F;"></i>
                  Instagram ID</label>
                <input type="text" id="ef_instagram" name="instagram_id" placeholder="yourusername"
                  value="<?= htmlspecialchars($student['instagram_id'] ?? '') ?>">
              </div>
              <div class="form-group full-width">
                <label for="ef_skills"><i class="fas fa-lightbulb" style="margin-right:4px;color:#f59e0b;"></i> Extra
                  Skills</label>
                <textarea id="ef_skills" name="extra_skills"
                  placeholder="e.g. Python, JavaScript, Photoshop..."><?= htmlspecialchars($student['extra_skills'] ?? '') ?></textarea>
              </div>

              <!-- Profile Picture Upload -->
              <div class="form-group">
                <label><i class="fas fa-camera" style="margin-right:4px;color:var(--primary-light);"></i> Profile
                  Picture</label>
                <div class="file-upload-area" onclick="document.getElementById('profilePicInput').click()">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <span>Click to upload profile picture</span>
                  <span class="file-name" id="picFileName"></span>
                  <input type="file" id="profilePicInput" name="profile_pic" accept="image/*"
                    onchange="previewPic(this)">
                </div>
                <?php if ($hasProfilePic): ?>
                  <div class="current-file"><i class="fas fa-image"></i> Current: <a
                      href="<?= htmlspecialchars($student['profile_pic']) ?>" target="_blank">View Photo</a></div>
                <?php endif; ?>
                <img id="picPreview" class="profile-pic-preview" src="" alt="Preview">
              </div>

              <!-- Resume/CV Upload -->
              <div class="form-group">
                <label><i class="fas fa-file-alt" style="margin-right:4px;color:var(--primary-light);"></i> Current
                  Resume / CV</label>
                <div class="file-upload-area" onclick="document.getElementById('cvInput').click()">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <span>Click to upload resume (PDF/DOC)</span>
                  <span class="file-name" id="cvFileName"></span>
                  <input type="file" id="cvInput" name="cv" accept=".pdf,.doc,.docx"
                    onchange="showFileName(this,'cvFileName')">
                </div>
                <?php if ($hasCv): ?>
                  <div class="current-file"><i class="fas fa-file-pdf"></i> Current: <a
                      href="<?= htmlspecialchars($student['cv']) ?>" target="_blank">View Resume</a></div>
                <?php endif; ?>
              </div>

            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary" id="saveBtn"><i class="fas fa-save"></i> Save
                Changes</button>
              <button type="button" class="btn btn-secondary"
                onclick="showPage('profile',document.querySelector('[data-page=profile]'))"><i class="fas fa-times"></i>
                Cancel</button>
            </div>
          </form>
        </div>
      </section>

      <!-- ════════ LOI ════════ -->
      <section class="content-section <?= $defaultPage === 'loi' ? 'active' : '' ?>" id="page-loi">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-file-download"></i> Download Letter of Intent (LOI)</h2>
          </div>
          <div class="loi-container" id="loiContent">
            <div class="loi-icon"><i class="fas fa-file-signature"></i></div>
            <h3>Download Your LOI</h3>
            <p>Complete the exam to become eligible to download your Letter of Intent. Read the instructions carefully.
            </p>
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
            <button class="btn btn-primary" onclick="startExam()"><i class="fas fa-play-circle"></i> Start Exam</button>
          </div>
        </div>
        <div class="card" id="examContainer" style="display:none;">
          <div class="card-header">
            <h2><i class="fas fa-pencil-alt"></i> Exam in Progress</h2>
            <div class="exam-timer" id="timer"><i class="fas fa-clock"></i> <span id="timerText">30:00</span></div>
          </div>
          <div id="examQuestions"></div>
          <div style="text-align:center;margin-top:22px;">
            <button class="btn btn-success" onclick="submitExam()" style="padding:13px 38px;font-size:15px;"><i
                class="fas fa-paper-plane"></i> Submit Exam</button>
          </div>
        </div>
        <div class="card" id="resultContainer" style="display:none;">
          <div id="resultContent"></div>
        </div>
      </section>
      <!-- ════════ STUDY MATERIAL ════════ -->
      <section class="content-section <?= $defaultPage === 'studyMaterial' ? 'active' : '' ?>" id="page-studyMaterial">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-book-open"></i> Study Material</h2>
            <span style="font-size:13px;color:var(--text-light);">
              <i class="fas fa-folder"></i> <?= count($materials) ?> files available
            </span>
          </div>

          <?php if (count($materials) > 0): ?>
            <div class="material-grid">
              <?php foreach ($materials as $m):
                $icon = getFileIcon($m['file_type'] ?? '');
                ?>
                <div class="material-card">
                  <div class="file-icon <?= $icon[1] ?>"><i class="<?= $icon[0] ?>"></i></div>
                  <h4><?= htmlspecialchars($m['title'] ?? 'Untitled') ?></h4>
                  <div class="file-meta">
                    <?= strtoupper(htmlspecialchars($m['file_type'] ?? 'FILE')) ?> • Uploaded
                    <?= date('M d, Y', strtotime($m['uploaded_at'] ?? 'now')) ?>
                  </div>

                  <!-- ✅ PDF/PPT open in modal -->
                  <?php if (in_array(strtolower($m['file_type']), ['pdf', 'ppt'])): ?>
                    <button class="open-btn" onclick="openMaterial('<?= htmlspecialchars($m['file_path']) ?>')">
                      <i class="fas fa-eye"></i> Open
                    </button>

                    <!-- ✅ Video link direct open -->
                  <?php elseif ($m['file_type'] === 'link'): ?>
                    <a href="<?= htmlspecialchars($m['file_path']) ?>" target="_blank" class="open-btn">
                      <i class="fas fa-video"></i> Watch on YouTube
                    </a>

                    <!-- ✅ Video file -->
                  <?php elseif (in_array(strtolower($m['file_type']), ['mp4', 'webm'])): ?>
                    <video width="100%" height="200" controls>
                      <source src="<?= htmlspecialchars($m['file_path']) ?>" type="video/<?= $m['file_type'] ?>">
                    </video>

                  <?php else: ?>
                    <button class="open-btn" onclick="openMaterial('<?= htmlspecialchars($m['file_path']) ?>')">
                      <i class="fas fa-eye"></i> View
                    </button>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-folder-open"></i>
              <p>No study materials uploaded for your batch yet.<br>Check back later!</p>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- ✅ Viewer Modal -->
      <div id="viewerModal" class="viewer-modal">
        <div class="viewer-content">
          <span class="close-btn" onclick="closeMaterial()">&times;</span>
          <div id="pdfViewer"></div>
          <div class="pdf-controls">
            <button onclick="prevPage()">Prev</button>
            <button onclick="zoomOut()">-</button>
            <span id="pageInfo">Page 1 of ?</span>
            <button onclick="zoomIn()">+</button>
            <button onclick="nextPage()">Next</button>
          </div>
        </div>
      </div>

      <style>
        .open-btn {
          background: #1565c0;
          color: #fff;
          border: none;
          padding: 8px 14px;
          border-radius: 6px;
          cursor: pointer;
          font-weight: 600;
          transition: 0.3s;
          text-decoration: none;
        }

        .open-btn:hover {
          background: #0d47a1;
        }

        .viewer-modal {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.7);
          justify-content: center;
          align-items: center;
          z-index: 9999;
        }

        .viewer-content {
          background: #fff;
          padding: 20px;
          border-radius: 10px;
          width: 80%;
          max-width: 900px;
          height: 80%;
          max-height: 700px;
          overflow: hidden;
          display: flex;
          flex-direction: column;
        }

        #pdfViewer {
          flex: 1;
          overflow: auto;
          border: 1px solid #ccc;
        }

        .close-btn {
          float: right;
          font-size: 24px;
          cursor: pointer;
          color: #333;
        }

        .pdf-controls {
          text-align: center;
          margin-top: 10px;
          flex-shrink: 0;
        }

        .pdf-controls button {
          background: #1565c0;
          color: #fff;
          border: none;
          padding: 8px 14px;
          border-radius: 6px;
          cursor: pointer;
          font-weight: 600;
          margin: 0 5px;
        }

        .pdf-controls button:hover {
          background: #0d47a1;
        }

        #pageInfo {
          font-weight: 600;
          color: #333;
          margin: 0 10px;
        }
      </style>

      <script type="module">
        import * as pdfjsLib from './pdfjs/pdf.mjs';
        pdfjsLib.GlobalWorkerOptions.workerSrc = './pdfjs/pdf.worker.mjs';

        let pdfDoc = null, pageNum = 1, scale = 1.2;

        function renderPage(num) {
          pdfDoc.getPage(num).then(function (page) {
            let viewport = page.getViewport({ scale: scale });
            let canvas = document.createElement('canvas');
            let context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            document.getElementById('pdfViewer').innerHTML = '';
            document.getElementById('pdfViewer').appendChild(canvas);

            let renderContext = { canvasContext: context, viewport: viewport };
            page.render(renderContext);

            document.getElementById('pageInfo').textContent = `Page ${num} of ${pdfDoc.numPages}`;
          });
        }

        // ✅ Open PDF
        window.openMaterial = function (path) {
          document.getElementById('viewerModal').style.display = 'flex';
          pageNum = 1; // reset to first page
          pdfjsLib.getDocument(path).promise.then(function (pdfDoc_) {
            pdfDoc = pdfDoc_;
            renderPage(pageNum);
          });
        }

        window.closeMaterial = function () {
          document.getElementById('viewerModal').style.display = 'none';
          document.getElementById('pdfViewer').innerHTML = '';
        }

        // ✅ Zoom controls
        window.zoomIn = function () { scale += 0.2; renderPage(pageNum); }
        window.zoomOut = function () { if (scale > 0.4) scale -= 0.2; renderPage(pageNum); }

        // ✅ Page navigation
        window.nextPage = function () {
          if (pageNum < pdfDoc.numPages) {
            pageNum++;
            renderPage(pageNum);
          }
        }
        window.prevPage = function () {
          if (pageNum > 1) {
            pageNum--;
            renderPage(pageNum);
          }
        }
      </script>




      <!-- ════════ DISCORD ════════ -->
      <section class="content-section <?= $defaultPage === 'discord' ? 'active' : '' ?>" id="page-discord">
        <div class="discord-banner">
          <i class="fab fa-discord"></i>
          <h3>Join Our Discord Community</h3>
          <p>Stay connected with mentors and peers, ask questions, and share your progress in real-time.</p>
          <a href="https://discord.gg/XfF7YMbvBN" target="_blank" class="btn-discord"><i class="fab fa-discord"></i>
            Join Discord Server</a>
        </div>
        <div class="card" style="margin-top:22px;">
          <div class="card-header">
            <h2><i class="fas fa-star"></i> Why Join?</h2>
          </div>
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-icon blue"><i class="fas fa-users"></i></div>
              <h3>500+</h3>
              <p>Active Members</p>
            </div>
            <div class="stat-card">
              <div class="stat-icon green"><i class="fas fa-comments"></i></div>
              <h3>24/7</h3>
              <p>Support Available</p>
            </div>
            <div class="stat-card">
              <div class="stat-icon purple"><i class="fas fa-chalkboard-teacher"></i></div>
              <h3>Live</h3>
              <p>Mentor Sessions</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ════════ CERTIFICATE ════════ -->
      <section class="content-section <?= $defaultPage === 'certificate' ? 'active' : '' ?>" id="page-certificate">
        <div class="card">
          <div class="card-header">
            <h2><i class="fas fa-certificate"></i> Download Certificate</h2>
          </div>
          <div class="card-body">
            <?php
            // Fetch student record
            $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
            $stmt->execute([$_SESSION['student_id']]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            // Profile completeness check
            function checkProfileComplete($student)
            {
              return !empty($student['first_name']) &&
                !empty($student['last_name']) &&
                !empty($student['email']) &&
                !empty($student['mobile']) &&
                !empty($student['internship_category']) &&
                !empty($student['internship_start_date']);
            }

            $profileComplete = checkProfileComplete($student);
            $canDownload = ($student['certificate_access'] == 1 && $profileComplete);

            if ($canDownload) {
              echo '<a href="download_certificate.php" class="btn btn-primary">Download Certificate</a>';
            } else {
              echo '<button class="btn btn-secondary" disabled>Download Certificate</button>';
              if (!$profileComplete) {
                echo '<p style="color:red;">Please complete your profile to enable certificate download.</p>';
              } elseif ($student['certificate_access'] == 0) {
                echo '<p style="color:red;">Certificate access not yet granted by trainer.</p>';
              }
            }
            ?>
          </div>
        </div>
      </section>


    </div>
  </main>

  <script>
    // ── PAGE NAVIGATION ──
    const titles = {
      dashboard: 'Welcome <span>back!</span>', profile: 'My <span>Profile</span>',
      loi: 'Download <span>LOI</span>', editProfile: 'Edit <span>Profile</span>',
      studyMaterial: 'Study <span>Material</span>', discord: 'Join <span>Discord</span>',
      certificate: 'Download <span>Certificate</span>' // ✅ comma added before this
    };

    function showPage(pageId, el) {
      document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
      const target = document.getElementById('page-' + pageId);
      if (target) target.classList.add('active');
      document.querySelectorAll('.sidebar-nav a').forEach(a => a.classList.remove('active'));
      if (el) el.classList.add('active');
      document.getElementById('topbarTitle').innerHTML = titles[pageId] || 'Dashboard';
      document.getElementById('sidebar').classList.remove('open');
      document.getElementById('sidebarOverlay').classList.remove('active');
      return false;
    }

    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
      document.getElementById('sidebarOverlay').classList.toggle('active');
    }

    // ── SET DEFAULT PAGE ──
    <?php if ($defaultPage !== 'dashboard'): ?>
      showPage('<?= $defaultPage ?>', document.querySelector('[data-page="<?= $defaultPage ?>"]'));
    <?php endif; ?>

    // ── TOAST ──
    <?php if ($savedMsg): ?>
      setTimeout(() => showToast('<?= $savedMsg ?>', 'success'), 300);
    <?php endif; ?>

    function showToast(message, type = 'info') {
      const toast = document.createElement('div');
      const colors = { success: '#10b981', error: '#ef4444', warning: '#f59e0b', info: '#3b82f6' };
      const icons = { success: 'fas fa-check-circle', error: 'fas fa-exclamation-circle', warning: 'fas fa-exclamation-triangle', info: 'fas fa-info-circle' };
      toast.innerHTML = `<i class="${icons[type]}"></i> ${message}`;
      Object.assign(toast.style, {
        position: 'fixed', top: '22px', right: '22px', background: colors[type], color: '#fff',
        padding: '13px 22px', borderRadius: '11px', fontSize: '13.5px', fontWeight: '600',
        fontFamily: 'Inter,sans-serif', zIndex: '9999', display: 'flex', alignItems: 'center',
        gap: '9px', boxShadow: '0 8px 28px rgba(0,0,0,0.18)', animation: 'fadeInUp .4s ease', maxWidth: '380px'
      });
      document.body.appendChild(toast);
      setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-10px)'; toast.style.transition = 'all .3s ease'; setTimeout(() => toast.remove(), 300); }, 3500);
    }

    // ── FILE UPLOAD HELPERS ──
    function previewPic(input) {
      const nameEl = document.getElementById('picFileName');
      const preview = document.getElementById('picPreview');
      if (input.files && input.files[0]) {
        nameEl.textContent = input.files[0].name;
        nameEl.style.display = 'block';
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
      }
    }

    function showFileName(input, elId) {
      const nameEl = document.getElementById(elId);
      if (input.files && input.files[0]) {
        nameEl.textContent = input.files[0].name;
        nameEl.style.display = 'block';
      }
    }

    // ── SAVE PROFILE (AJAX) ──
    async function saveProfile(e) {
      e.preventDefault();
      const btn = document.getElementById('saveBtn');
      btn.classList.add('loading');
      btn.disabled = true;

      const form = document.getElementById('editProfileForm');
      const formData = new FormData(form);

      try {
        const res = await fetch('save_profile.php', { method: 'POST', body: formData });
        const result = await res.json();
        if (result.success) {
          showToast(result.message, 'success');
          setTimeout(() => { window.location.href = 'dashboard.php?saved=1&page=profile'; }, 800);
        } else {
          showToast(result.message || 'Error saving profile', 'error');
          btn.classList.remove('loading');
          btn.disabled = false;
        }
      } catch (err) {
        showToast('Network error. Please try again.', 'error');
        btn.classList.remove('loading');
        btn.disabled = false;
      }
    }

    // ── EXAM ──
    let examTimer, timeLeft = 30 * 60;

    async function startExam() {
      if (!document.getElementById('agree').checked) {
        showToast('Please agree to the terms before starting.', 'warning');
        return;
      }
      try {
        const res = await fetch('get_exam_questions.php');
        const questions = await res.json();
        let html = '';
        questions.forEach((q, i) => {
          html += `<div class="question-card">
          <div class="q-text"><span class="q-number">${i + 1}</span>${q.question}</div>
          <label class="option-label"><input type="radio" name="q${q.id}" value="A"> ${q.option_a}</label>
          <label class="option-label"><input type="radio" name="q${q.id}" value="B"> ${q.option_b}</label>
          <label class="option-label"><input type="radio" name="q${q.id}" value="C"> ${q.option_c}</label>
          <label class="option-label"><input type="radio" name="q${q.id}" value="D"> ${q.option_d}</label>
        </div>`;
        });
        document.getElementById('examQuestions').innerHTML = html;
        document.getElementById('loiContent').style.display = 'none';
        document.getElementById('examContainer').style.display = 'block';
        document.getElementById('resultContainer').style.display = 'none';
        timeLeft = 30 * 60;
        clearInterval(examTimer);
        examTimer = setInterval(updateTimer, 1000);
      } catch (err) {
        showToast('Could not load exam questions.', 'error');
      }
    }

    function updateTimer() {
      let m = Math.floor(timeLeft / 60), s = timeLeft % 60;
      document.getElementById('timerText').textContent = `${m}:${s < 10 ? '0' + s : s}`;
      timeLeft--;
      if (timeLeft < 0) { clearInterval(examTimer); submitExam(); }
    }

    async function submitExam() {
      clearInterval(examTimer);
      const inputs = document.querySelectorAll('#examQuestions input[type="radio"]:checked');
      let answers = {};
      inputs.forEach(inp => { answers[inp.name.substring(1)] = inp.value; });
      try {
        const res = await fetch('submit_exam.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ answers }) });
        const result = await res.json();
        document.getElementById('examContainer').style.display = 'none';
        document.getElementById('resultContainer').style.display = 'block';
        if (result.eligible) {
          document.getElementById('resultContent').innerHTML = `<div class="result-card">
          <div class="result-icon success"><i class="fas fa-check-circle"></i></div>
          <div class="score-display">${result.score} <span>/ 25</span></div>
          <h3 style="color:#10b981;margin-bottom:7px;">Congratulations! 🎉</h3>
          <p style="color:var(--text-light);margin-bottom:22px;">You can now download your LOI.</p>
          <a href="download_loi.php" class="btn btn-success"><i class="fas fa-download"></i> Download LOI</a>
        </div>`;
        } else {
          document.getElementById('resultContent').innerHTML = `<div class="result-card">
          <div class="result-icon fail"><i class="fas fa-times-circle"></i></div>
          <div class="score-display">${result.score} <span>/ 25</span></div>
          <h3 style="color:#ef4444;margin-bottom:7px;">Not Cleared</h3>
          <p style="color:var(--text-light);margin-bottom:22px;">You need at least 20 correct answers. Please retake.</p>
          <button class="btn btn-primary" onclick="retakeExam()"><i class="fas fa-redo"></i> Retake Exam</button>
        </div>`;
        }
      } catch (err) { showToast('Could not submit exam.', 'error'); }
    }

    function retakeExam() {
      document.getElementById('loiContent').style.display = 'block';
      document.getElementById('examContainer').style.display = 'none';
      document.getElementById('resultContainer').style.display = 'none';
      document.getElementById('agree').checked = false;
    }
  </script>
</body>

</html>