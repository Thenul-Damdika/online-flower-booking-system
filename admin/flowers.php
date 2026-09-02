<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM flowers WHERE id = $delete_id");
    header("Location: flowers.php");
    exit();
}

$query = "SELECT f.*, s.supplier_name 
          FROM flowers f 
          LEFT JOIN suppliers s ON f.supplier_id = s.id 
          ORDER BY f.id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    $query = "SELECT * FROM flowers ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Flowers - Admin</title>
</head>
<body>

    <div>
        <h2>Manage Flowers</h2>
        <a href="dashboard.php">Back to Dashboard</a> | 
        <a href="../flowers/add-flower.php">+ Add New Flower</a>
    </div>

    <br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Flower Name</th>
                <th>Price (LKR)</th>
                <th>Stock</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $flower_name = $row['flower_name'] ?? $row['name'] ?? $row['title'] ?? 'N/A';
                    $stock       = $row['stock'] ?? $row['quantity'] ?? $row['qty'] ?? 0;
                    $category    = $row['category'] ?? $row['type'] ?? $row['flower_type'] ?? 'N/A';
                    $supplier    = $row['supplier_name'] ?? 'Not Assigned';
                ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars((string)$flower_name); ?></strong></td>
                        <td>Rs. <?php echo number_format($row['price'] ?? 0, 2); ?></td>
                        <td><?php echo htmlspecialchars((string)$stock); ?></td>
                        <td><?php echo htmlspecialchars((string)$category); ?></td>
                        <td><?php echo htmlspecialchars((string)$supplier); ?></td>
                        <td>
                            <a href="../flowers/flower-details.php?id=<?php echo $row['id']; ?>">View Details</a> | 
                            <a href="../flowers/edit-flower.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                            <a href="flowers.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this flower?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No flowers found in the database.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>