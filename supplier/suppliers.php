<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

$query = "SELECT * FROM suppliers ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Suppliers - Flower Booking</title>
</head>
<body>

    <div>
        <h2>Registered Suppliers</h2>
        <a href="../admin/dashboard.php">← Back to Dashboard</a>
    </div>

    <br>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Supplier Name</th>
                <th>Contact Phone</th>
                <th>Email Address</th>
                <th>Location / Address</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): 
                    $s_name = $row['supplier_name'] ?? $row['name'] ?? 'N/A';
                ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars((string)$s_name); ?></strong></td>
                        <td><?php echo htmlspecialchars((string)($row['phone'] ?? 'N/A')); ?></td>
                        <td><?php echo htmlspecialchars((string)($row['email'] ?? 'N/A')); ?></td>
                        <td><?php echo htmlspecialchars((string)($row['address'] ?? 'N/A')); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No suppliers available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>