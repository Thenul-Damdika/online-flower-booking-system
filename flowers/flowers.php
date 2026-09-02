<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if (!empty($search)) {
    $query = "SELECT f.*, s.supplier_name 
              FROM flowers f 
              LEFT JOIN suppliers s ON f.supplier_id = s.id 
              WHERE f.flower_name LIKE '%$search%' OR f.category LIKE '%$search%' 
              ORDER BY f.id DESC";
} else {
    $query = "SELECT f.*, s.supplier_name 
              FROM flowers f 
              LEFT JOIN suppliers s ON f.supplier_id = s.id 
              ORDER BY f.id DESC";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Flowers</title>
</head>
<body>

    <div>
        <h2>Flowers Catalog</h2>
        <a href="../admin/dashboard.php">Back to Dashboard</a>
    </div>

    <br>

    <form action="flowers.php" method="GET">
        <input type="text" name="search" placeholder="Search by name or category..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if (!empty($search)): ?>
            <a href="flowers.php">Clear</a>
        <?php endif; ?>
    </form>

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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $flower_name = $row['flower_name'] ?? $row['name'] ?? 'N/A';
                    $stock       = $row['stock'] ?? $row['quantity'] ?? 0;
                    $category    = $row['category'] ?? $row['type'] ?? 'N/A';
                    $supplier    = $row['supplier_name'] ?? 'Not Assigned';
                ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars((string)$flower_name); ?></strong></td>
                        <td>Rs. <?php echo number_format($row['price'] ?? 0, 2); ?></td>
                        <td><?php echo htmlspecialchars((string)$stock); ?></td>
                        <td><?php echo htmlspecialchars((string)$category); ?></td>
                        <td><?php echo htmlspecialchars((string)$supplier); ?></td>
                        <td>
                            <a href="flower-details.php?id=<?php echo $row['id']; ?>">View Details</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No flowers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>