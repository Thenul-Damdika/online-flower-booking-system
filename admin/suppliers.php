<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

if (isset($_POST['add_supplier'])) {
    $s_name    = mysqli_real_escape_string($conn, $_POST['supplier_name']);
    $s_phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $s_email   = mysqli_real_escape_string($conn, $_POST['email']);
    $s_address = mysqli_real_escape_string($conn, $_POST['address']);

    $insert = @mysqli_query($conn, "INSERT INTO suppliers (supplier_name, phone, email, address) VALUES ('$s_name', '$s_phone', '$s_email', '$s_address')");
    
    if (!$insert) {
        mysqli_query($conn, "INSERT INTO suppliers (name, phone, email, address) VALUES ('$s_name', '$s_phone', '$s_email', '$s_address')");
    }

    header("Location: suppliers.php");
    exit();
}

if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM suppliers WHERE id = $delete_id");
    header("Location: suppliers.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Suppliers</title>
</head>
<body>

    <div>
        <h2>Manage Suppliers</h2>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <br>

    <h3>Add New Supplier</h3>
    <form action="suppliers.php" method="POST">
        <label>Supplier Name:</label><br>
        <input type="text" name="supplier_name" required><br><br>

        <label>Phone Number:</label><br>
        <input type="text" name="phone"><br><br>

        <label>Email Address:</label><br>
        <input type="email" name="email"><br><br>

        <label>Address:</label><br>
        <textarea name="address"></textarea><br><br>

        <button type="submit" name="add_supplier">Save Supplier</button>
    </form>

    <br><hr><br>

    <h3>Suppliers List</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Action</th>
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
                        <td>
                            <a href="suppliers.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No suppliers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>