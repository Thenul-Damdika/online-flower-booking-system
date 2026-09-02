<?php
$host = "127.0.0.1";
$username = "root";
$password = ""; 
$database = "online_flower_booking";
$port = 3307;

$conn = mysqli_connect($host, $username, $password, $database, $port);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>