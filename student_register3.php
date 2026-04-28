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
    $internship_start_date = $_POST['internship_start_date']; // should be YYYY-MM-DD format
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

        echo "Registration successful! <a href='student_login.html'>Login here</a>";
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
