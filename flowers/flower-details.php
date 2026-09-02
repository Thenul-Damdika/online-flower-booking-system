<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT f.*, s.supplier_name 
          FROM flowers f 
          LEFT JOIN suppliers s ON f.supplier_id = s.id 
          WHERE f.id = $id";

$result = mysqli_query($conn, $query);
$flower = mysqli_fetch_assoc($result);

if (!$flower) {
    header("Location: flowers.php");
    exit();
}

$flower_name = $flower['flower_name'] ?? $flower['name'] ?? $flower['title'] ?? 'N/A';
$stock       = $flower['stock'] ?? $flower['quantity'] ?? $flower['qty'] ?? 0;
$category    = $flower['category'] ?? $flower['type'] ?? $flower['flower_type'] ?? 'N/A';
$supplier    = $flower['supplier_name'] ?? 'Not Assigned';
$price       = $flower['price'] ?? 0;
$description = $flower['description'] ?? $flower['details'] ?? 'No description available.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flower Details</title>
</head>
<body>

    <div>
        <h2>Flower Details</h2>
        <a href="flowers.php">← Back to Flowers List</a>
    </div>

    <br>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <td>#<?php echo $flower['id']; ?></td>
        </tr>
        <tr>
            <th>Flower Name</th>
            <td><strong><?php echo htmlspecialchars((string)$flower_name); ?></strong></td>
        </tr>
        <tr>
            <th>Price</th>
            <td>Rs. <?php echo number_format($price, 2); ?></td>
        </tr>
        <tr>
            <th>Stock Available</th>
            <td><?php echo htmlspecialchars((string)$stock); ?></td>
        </tr>
        <tr>
            <th>Category</th>
            <td><?php echo htmlspecialchars((string)$category); ?></td>
        </tr>
        <tr>
            <th>Supplier</th>
            <td><?php echo htmlspecialchars((string)$supplier); ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?php echo htmlspecialchars((string)$description); ?></td>
        </tr>
    </table>

</body>
</html>