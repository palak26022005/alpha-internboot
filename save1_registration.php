<?php
// Database connection (Hostinger live)
$conn = new mysqli("localhost", "u293157276_internbootuser", "2025#Human", "u293157276_alphacampany");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Collect form data
$name = $_POST['name'];
$whatsapp = $_POST['whatsapp'];
$email = $_POST['email'];
$country = $_POST['Country'];
$qualification = $_POST['qualification'];
$passingYear = $_POST['passingYear'];
$college = $_POST['college'];
$domain = $_POST['domain'];
$startDate = $_POST['startDate'];
$tenure = $_POST['tenure'];
$reference = $_POST['reference'];

// Insert query
$sql = "INSERT INTO registrations 
(name, whatsapp, email, country, qualification, passingYear, college, domain, startDate, tenure, reference) 
VALUES ('$name', '$whatsapp', '$email', '$country', '$qualification', '$passingYear', '$college', '$domain', '$startDate', '$tenure', '$reference')";

if ($conn->query($sql) === TRUE) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Registration Success</title>
        <style>
            body {
                font-family: "Poppins", sans-serif;
                background: linear-gradient(135deg, #002b5c 0%, #004080 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .popup {
                background: #fff;
                padding: 40px;
                border-radius: 16px;
                text-align: center;
                box-shadow: 0 8px 32px rgba(0,0,0,0.2);
                animation: fadeIn 0.6s ease-in-out;
                position: relative;
            }
            .popup h1 {
                color: #004080;
                margin-bottom: 15px;
            }
            .popup p {
                font-size: 16px;
                color: #333;
                margin-bottom: 20px;
            }
            .close-btn {
                position: absolute;
                top: 12px;
                right: 12px;
                background: #ff4d4d;
                color: #fff;
                border: none;
                border-radius: 50%;
                width: 32px;
                height: 32px;
                font-size: 18px;
                cursor: pointer;
                transition: 0.3s;
            }
            .close-btn:hover {
                background: #cc0000;
            }
            .back-btn {
                display: inline-block;
                padding: 10px 20px;
                background: #004080;
                color: #fff;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 500;
                transition: 0.3s;
            }
            .back-btn:hover {
                background: #002b5c;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: scale(0.9); }
                to { opacity: 1; transform: scale(1); }
            }
        </style>
    </head>
    <body>
        <div class="popup">
            <button class="close-btn" onclick="window.location.href='https://internboot.com/'">&times;</button>
            <h1>🎉 Registration Successful!</h1>
            <p>Thank you <?php echo htmlspecialchars($name); ?>, we’ll contact you soon 🚀</p>
            <a href="https://internboot.com/" class="back-btn">Go to InternBoot</a>
        </div>
    </body>
    </html>
    <?php
}


$conn->close();
?>
