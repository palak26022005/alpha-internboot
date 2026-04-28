<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Login</title>
      <link rel="icon" href="https://internboot.com/img/logos/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f8ff 0%, #e6f0ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 31, 63, 0.12);
            border: 1px solid rgba(77, 149, 255, 0.15);
        }

        .login-header {
            background: linear-gradient(135deg, #001f3f 0%, #003366 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 15px;
            opacity: 0.9;
            font-weight: 400;
        }

        .login-body {
            padding: 40px 40px 50px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #1a1a1a;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #e1e8ed;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fafcff;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4dabf7;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(77, 171, 247, 0.12);
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #001f3f 0%, #002b55 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #002b55 0%, #003d80 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 31, 63, 0.2);
        }

        .error-msg {
            background: #ffebee;
            color: #c62828;
            padding: 14px 16px;
            border-radius: 10px;
            font-size: 14px;
            text-align: center;
            margin-bottom: 24px;
            border: 1px solid #ffcdd2;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                border-radius: 16px;
            }
            .login-header {
                padding: 30px 20px;
            }
            .login-body {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>

<?php 
session_start(); 
require_once 'db.php'; 

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $email = $_POST['email'] ?? ''; 
    $password = $_POST['password'] ?? ''; 
    
    $conn = get_db_connection(); 
    $stmt = $conn->prepare("SELECT id, name, email, password FROM trainers WHERE email=?"); 
    $stmt->execute([$email]); 
    $trainer = $stmt->fetch(PDO::FETCH_ASSOC); 
    
    // Plain text password check (you should use password_hash/password_verify in production)
    if ($trainer && $password === $trainer['password']) { 
        $_SESSION['trainer_id'] = $trainer['id']; 
        $_SESSION['trainer_name'] = $trainer['name']; 
        header("Location: trainer_dashboard.php"); 
        exit; 
    } else { 
        $error = "Invalid login credentials!";
    } 
} 
?>

<div class="login-container">
    <div class="login-header">
        <h1>Trainer Portal</h1>
        <p>Welcome back! Please login to continue</p>
    </div>
    
    <div class="login-body">
        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="login-btn">Sign In</button>
        </form>
    </div>
</div>

</body>
</html>