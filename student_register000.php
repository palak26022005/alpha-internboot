<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $whatsapp = $_POST['whatsapp'];
    $employment_status = $_POST['employment_status'];
    $internship_category = $_POST['internship_category'];
    $internship_tenure = $_POST['internship_tenure'];
    $internship_start_date = $_POST['internship_start_date']; // YYYY-MM-DD format
    $extra_skills = $_POST['extra_skills'];
    $aadhar_number = $_POST['aadhar_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    try {
        $conn = get_db_connection();

        // ✅ Find course_id from internship_category
        $stmtCourse = $conn->prepare("SELECT id FROM courses WHERE name=?");
        $stmtCourse->execute([$internship_category]);
        $course = $stmtCourse->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            die("Invalid internship category selected!");
        }
        $course_id = $course['id'];

        // ✅ Check if batch already exists for this course + date
        $stmtBatch = $conn->prepare("SELECT id FROM batches WHERE course_id=? AND start_date=?");
        $stmtBatch->execute([$course_id, $internship_start_date]);
        $batch = $stmtBatch->fetch(PDO::FETCH_ASSOC);

        if ($batch) {
            $batch_id = $batch['id'];
        } else {
            // ✅ Create new batch dynamically
            $batch_name = $internship_category . " Batch " . $internship_start_date;
            $stmtNewBatch = $conn->prepare("INSERT INTO batches (course_id, batch_name, start_date) VALUES (?, ?, ?)");
            $stmtNewBatch->execute([$course_id, $batch_name, $internship_start_date]);
            $batch_id = $conn->lastInsertId();
        }

        // ✅ Insert student with batch_id
        $stmt = $conn->prepare("INSERT INTO students 
            (first_name, last_name, username, email, mobile, whatsapp, employment_status, internship_category, internship_tenure, internship_start_date, extra_skills, aadhar_number, password, created_at, batch_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->execute([
            $first_name, $last_name, $username, $email, $mobile, $whatsapp,
            $employment_status, $internship_category, $internship_tenure,
            $internship_start_date, $extra_skills, $aadhar_number,
            $password, $batch_id
        ]);

        // ✅ Show success popup
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                  document.getElementById('successPopup').style.display = 'flex';
                });
              </script>";

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!-- Success Popup -->
<div id="successPopup" class="popup">
  <div class="popup-content">
    <h2>🎉 Registration Successful!</h2>
    <p>You have successfully registered.</p>
    <p class="login-msg">👉 Please login to continue</p>
    <a href="student_login.html" class="popup-btn">Login Now</a>
  </div>
</div>

<style>
  .popup {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,50,0.6);
    justify-content: center;
    align-items: center;
    z-index: 9999;
    padding: 15px;
  }
  .popup-content {
    background: #ffffff;
    padding: 30px;
    border-radius: 14px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    animation: fadeIn 0.4s ease;
    width: 100%;
    max-width: 420px;
  }
  .popup-content h2 {
    margin-bottom: 12px;
    color: #1565c0;
    font-family: 'Segoe UI', sans-serif;
    font-size: 20px;
  }
  .popup-content p {
    color: #333;
    margin-bottom: 12px;
    font-size: 15px;
  }
  .popup-content .login-msg {
    font-weight: 600;
    color: #1565c0;
    margin-bottom: 18px;
  }
  .popup-btn {
    display: inline-block;
    padding: 12px 24px;
    background: #1565c0;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }
  .popup-btn:hover {
    background: #0d47a1;
    transform: scale(1.05);
  }
  @keyframes fadeIn {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
</style>