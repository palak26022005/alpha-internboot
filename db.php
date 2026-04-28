<?php
function get_db_connection() {
 $host = "localhost";  // Hostinger MySQL host (often 'localhost' or given in panel)
$dbname = "u293157276_alpha";   // full DB name from Hostinger
$username = "u293157276_alpha";  // full MySQL username
$password = "2025#Human";         // password you set

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("DB Connection failed: " . $e->getMessage());
    }
}
?>
