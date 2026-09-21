<?php
// Database Configuration
$host     = "127.0.0.1";
$username = "root";
$password = "";
$database = "online_flower_booking";
$port     = 3307;

// Enable MySQLi Exception reporting for error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $database, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>