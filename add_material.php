<?php
session_start();
require_once 'db.php';
$conn = get_db_connection();

$batchId = $_GET['batch_id'];

// ✅ Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $title = $_POST['title'];

    // If trainer uploaded a file
    if (!empty($_FILES['file']['name'])) {
        $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $targetDir = "uploads/materials/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES['file']['name']);
        $targetFile = $targetDir . $fileName;
        move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);

        $stmt = $conn->prepare("INSERT INTO study_material (batch_id, title, file_path, file_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$batchId, $title, $targetFile, $fileType]);

    } elseif (!empty($_POST['video_link'])) {
        // If trainer pasted a video link
        $videoLink = $_POST['video_link'];
        $stmt = $conn->prepare("INSERT INTO study_material (batch_id, title, file_path, file_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$batchId, $title, $videoLink, 'link']);
    }

    echo "<script>alert('Material uploaded successfully!');</script>";
}

// ✅ Handle delete
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $conn->prepare("SELECT file_path, file_type FROM study_material WHERE id=?");
    $stmt->execute([$deleteId]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($file) {
        if ($file['file_type'] != 'link' && file_exists($file['file_path'])) {
            unlink($file['file_path']); // delete file from server
        }
        $stmtDel = $conn->prepare("DELETE FROM study_material WHERE id=?");
        $stmtDel->execute([$deleteId]);
        echo "<script>alert('Material deleted successfully!');</script>";
    }
}

// ✅ Fetch materials
$stmt = $conn->prepare("SELECT * FROM study_material WHERE batch_id=? ORDER BY id DESC");
$stmt->execute([$batchId]);
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Study Materials</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background:#f4f6f9; margin:0; padding:20px; }
    h2 { color:#1565c0; }
    form {
      background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1);
      margin-bottom:30px; max-width:500px;
    }
    label { display:block; margin-bottom:8px; font-weight:600; }
    input[type="text"], input[type="file"] {
      width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:6px;
    }
    button {
      background:#1565c0; color:#fff; border:none; padding:12px 20px; border-radius:6px;
      cursor:pointer; font-weight:600; transition:0.3s;
    }
    button:hover { background:#0d47a1; }
    table {
      width:100%; border-collapse:collapse; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding:12px; border-bottom:1px solid #ddd; text-align:left;
    }
    th { background:#1565c0; color:#fff; }
    a.delete-btn {
      color:#c62828; font-weight:600; text-decoration:none;
    }
    a.delete-btn:hover { text-decoration:underline; }
    .back-btn {
      display:inline-block; margin-top:20px; padding:10px 18px;
      background:#555; color:#fff; border-radius:6px; text-decoration:none;
      font-weight:600; transition:0.3s;
    }
    .back-btn:hover { background:#333; }
  </style>
</head>
<body>

<h2>Manage Study Materials (Batch ID: <?php echo $batchId; ?>)</h2>

<!-- Upload Form -->
<form method="POST" enctype="multipart/form-data">
  <label>Title:</label>
  <input type="text" name="title" required>
  
  <label>Upload File (PDF/PPT/Video):</label>
  <input type="file" name="file">
  
  <label>Or Paste Video Link (YouTube/Vimeo):</label>
  <input type="text" name="video_link" placeholder="https://...">
  
  <button type="submit" name="upload">Upload</button>
</form>

<!-- Materials List -->
<?php if ($materials): ?>
<table>
  <tr>
    <th>ID</th>
    <th>Title</th>
    <th>Type</th>
    <th>Action</th>
  </tr>
  <?php foreach ($materials as $m): ?>
  <tr>
    <td><?php echo $m['id']; ?></td>
    <td><?php echo htmlspecialchars($m['title']); ?></td>
    <td><?php echo strtoupper($m['file_type']); ?></td>
    <td>
      <?php if ($m['file_type'] == 'link'): ?>
        <a href="<?php echo $m['file_path']; ?>" target="_blank">Watch Video</a> | 
      <?php else: ?>
        <a href="<?php echo $m['file_path']; ?>" target="_blank">View</a> | 
      <?php endif; ?>
      <a href="?batch_id=<?php echo $batchId; ?>&delete_id=<?php echo $m['id']; ?>" class="delete-btn" onclick="return confirm('Delete this material?')">Delete</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php else: ?>
<p>No materials uploaded yet.</p>
<?php endif; ?>

<!-- Back Button -->
<a href="trainer_dashboard.php" class="back-btn">← Back</a>

</body>
</html>
