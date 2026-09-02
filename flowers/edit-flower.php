<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

mysqli_report(MYSQLI_REPORT_OFF);

require_once '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isset($_POST['update_flower'])) {
    $name        = mysqli_real_escape_string($conn, $_POST['flower_name']);
    $price       = (float)$_POST['price'];
    $stock       = (int)$_POST['stock'];
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $supplier_id = (int)$_POST['supplier_id'];

    $update = mysqli_query($conn, "UPDATE flowers SET flower_name='$name', price=$price, stock=$stock, category='$category', supplier_id=$supplier_id WHERE id=$id");

    if (!$update) {
        $update = mysqli_query($conn, "UPDATE flowers SET flower_name='$name', price=$price, quantity=$stock, category='$category', supplier_id=$supplier_id WHERE id=$id");
    }

    if (!$update) {
        mysqli_query($conn, "UPDATE flowers SET name='$name', price=$price, quantity=$stock, category='$category', supplier_id=$supplier_id WHERE id=$id");
    }

    header("Location: ../admin/flowers.php");
    exit();
}

$flower_res = mysqli_query($conn, "SELECT * FROM flowers WHERE id = $id");
$flower = mysqli_fetch_assoc($flower_res);

if (!$flower) {
    header("Location: ../admin/flowers.php");
    exit();
}

$suppliers_res = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Flower</title>
</head>
<body>

    <a href="../admin/flowers.php">← Back to Flowers List</a>
    <h2>Edit Flower Details</h2>

    <form action="edit-flower.php?id=<?php echo $id; ?>" method="POST">
        <label>Flower Name:</label><br>
        <input type="text" name="flower_name" value="<?php echo htmlspecialchars($flower['flower_name'] ?? $flower['name'] ?? $flower['title'] ?? ''); ?>" required><br><br>

        <label>Price (LKR):</label><br>
        <input type="number" step="0.01" name="price" value="<?php echo $flower['price'] ?? 0; ?>" required><br><br>

        <label>Stock Quantity:</label><br>
        <input type="number" name="stock" value="<?php echo $flower['stock'] ?? $flower['quantity'] ?? $flower['qty'] ?? 0; ?>" required><br><br>

        <label>Category / Type:</label><br>
        <input type="text" name="category" value="<?php echo htmlspecialchars($flower['category'] ?? $flower['type'] ?? ''); ?>"><br><br>

        <label>Select Supplier:</label><br>
        <select name="supplier_id" required>
            <option value="">-- Choose Supplier --</option>
            <?php if ($suppliers_res && mysqli_num_rows($suppliers_res) > 0): ?>
                <?php while ($s = mysqli_fetch_assoc($suppliers_res)): 
                    $s_name = $s['supplier_name'] ?? $s['name'] ?? 'Supplier #' . $s['id'];
                    $selected = ($s['id'] == ($flower['supplier_id'] ?? 0)) ? 'selected' : '';
                ?>
                    <option value="<?php echo $s['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars((string)$s_name); ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select><br><br>

        <button type="submit" name="update_flower">Update Flower</button>
    </form>

</body>
</html>