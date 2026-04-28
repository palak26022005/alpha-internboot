<?php
session_start();
require_once 'db.php';

$conn = get_db_connection();
$studentId = $_SESSION['student_id'] ?? 0;

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $fields = [
    'first_name',
    'last_name',
    'username',
    'email',
    'mobile',
    'whatsapp',
    'employment_status',
    // 'internship_category',
    // 'internship_tenure',
    // 'internship_start_date',
    'extra_skills',
    'aadhar_number',
    'linkedin_id',
    'instagram_id'
  ];
  $updates = [];
  $values = [];

  foreach ($fields as $f) {
    $updates[] = "$f=?";
    $values[] = $_POST[$f] ?? '';
  }

  // CV Upload
  $cvFile = $student['cv'];
  if (!empty($_FILES['cv']['name'])) {
    $targetDir = "uploads/cv/";
    if (!is_dir($targetDir))
      mkdir($targetDir, 0777, true);
    $fileName = time() . "_" . basename($_FILES['cv']['name']);
    $targetFile = $targetDir . $fileName;
    move_uploaded_file($_FILES['cv']['tmp_name'], $targetFile);
    $cvFile = $targetFile;
  }

  // Profile Pic Upload
  $picFile = $student['profile_pic'];
  if (!empty($_FILES['profile_pic']['name'])) {
    $targetDir = "uploads/profile_pics/";
    if (!is_dir($targetDir))
      mkdir($targetDir, 0777, true);
    $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
    $targetFile = $targetDir . $fileName;
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile);
    $picFile = $targetFile;
  }

  $updates[] = "cv=?";
  $values[] = $cvFile;
  $updates[] = "profile_pic=?";
  $values[] = $picFile;

  $values[] = $studentId;

  $sql = "UPDATE students SET " . implode(",", $updates) . " WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->execute($values);

  echo "<p style='color:green;'>Profile updated successfully!</p>";

  // Refresh data
  $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
  $stmt->execute([$studentId]);
  $student = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link rel="icon" href="https://internboot.com/img/logos/favicon.ico">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="form-container">
    <h2>Edit Profile</h2>
    <form method="POST" enctype="multipart/form-data">

      <label>First Name:</label>
      <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>">

      <label>Last Name:</label>
      <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>">

      <label>Username:</label>
      <input type="text" name="username" value="<?= htmlspecialchars($student['username']) ?>">

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>">

      <label>Mobile:</label>
      <input type="text" name="mobile" value="<?= htmlspecialchars($student['mobile']) ?>">

      <label>WhatsApp:</label>
      <input type="text" name="whatsapp" value="<?= htmlspecialchars($student['whatsapp']) ?>">

      <label>Employment Status:</label>
      <input type="text" name="employment_status" value="<?= htmlspecialchars($student['employment_status']) ?>">

      <label>Internship Category:</label>
      <input type="text" name="internship_category" value="<?= htmlspecialchars($student['internship_category']) ?>"
        readonly>

      <label>Internship Tenure:</label>
      <input type="text" name="internship_tenure" value="<?= htmlspecialchars($student['internship_tenure']) ?>"
        readonly>

      <label>Internship Start Date:</label>
      <input type="date" name="internship_start_date" value="<?= htmlspecialchars($student['internship_start_date']) ?>"
        readonly>


      <label>Extra Skills:</label>
      <textarea name="extra_skills"><?= htmlspecialchars($student['extra_skills']) ?></textarea>

      <label>Aadhar Number:</label>
      <input type="text" name="aadhar_number" value="<?= htmlspecialchars($student['aadhar_number']) ?>">

      <label>LinkedIn ID:</label>
      <input type="text" name="linkedin_id" value="<?= htmlspecialchars($student['linkedin_id']) ?>">

      <label>Instagram ID:</label>
      <input type="text" name="instagram_id" value="<?= htmlspecialchars($student['instagram_id']) ?>">

      <label>Upload CV:</label>
      <input type="file" name="cv">
      <?php if ($student['cv'])
        echo "<a href='{$student['cv']}' target='_blank'>Current CV</a>"; ?>

      <label>Profile Picture:</label>
      <input type="file" name="profile_pic">
      <?php if ($student['profile_pic'])
        echo "<img src='{$student['profile_pic']}' width='100'>"; ?>

      <button type="submit" class="btn">Save Changes</button>
    </form>
  </div>
</body>

</html>