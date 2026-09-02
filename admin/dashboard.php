<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$total_flowers   = 0;
$total_suppliers = 0;
$total_orders    = 0;
$total_users     = 0;

$res1 = mysqli_query($conn, "SELECT COUNT(*) as count FROM flowers");
if ($res1) { $total_flowers = mysqli_fetch_assoc($res1)['count']; }

$res2 = mysqli_query($conn, "SELECT COUNT(*) as count FROM suppliers");
if ($res2) { $total_suppliers = mysqli_fetch_assoc($res2)['count']; }

$res3 = @mysqli_query($conn, "SELECT COUNT(*) as count FROM orders");
if ($res3) { $total_orders = mysqli_fetch_assoc($res3)['count']; }

$res4 = @mysqli_query($conn, "SELECT COUNT(*) as count FROM customers");
if ($res4) { $total_users = mysqli_fetch_assoc($res4)['count']; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>

    <div>
        <h2>Online Flower Booking System - Admin Dashboard</h2>
        <span>Logged in as <strong>Admin</strong></span>
    </div>

    <hr>

    <div>
        <div>
            <h3><?php echo $total_flowers; ?></h3>
            <p>Total Flowers</p>
        </div>
        <div>
            <h3><?php echo $total_suppliers; ?></h3>
            <p>Total Suppliers</p>
        </div>
        <div>
            <h3><?php echo $total_orders; ?></h3>
            <p>Total Orders</p>
        </div>
        <div>
            <h3><?php echo $total_users; ?></h3>
            <p>Total Customers</p>
        </div>
    </div>

    <hr>

    <div>
        <a href="flowers.php">Manage Flowers</a> | 
        <a href="suppliers.php">Manage Suppliers</a> | 
        <a href="login.php">Logout</a>
    </div>

</body>
</html>