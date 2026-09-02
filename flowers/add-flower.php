<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

if (isset($_POST['save_flower'])) {
    $name        = mysqli_real_escape_string($conn, $_POST['flower_name']);
    $price       = (float)$_POST['price'];
    $stock       = (int)$_POST['stock'];
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $supplier_id = (int)$_POST['supplier_id'];

    $insert = @mysqli_query($conn, "INSERT INTO flowers (flower_name, price, stock, category, supplier_id) VALUES ('$name', $price, $stock, '$category', $supplier_id)");
    
    if (!$insert) {
        mysqli_query($conn, "INSERT INTO flowers (name, price, stock, category, supplier_id) VALUES ('$name', $price, $stock, '$category', $supplier_id)");
    }

    header("Location: ../admin/flowers.php");
    exit();
}

$suppliers_res = mysqli_query($conn, "SELECT * FROM suppliers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Flower</title>
</head>
<body>

    <a href="../admin/flowers.php">← Back to Flowers List</a>
    <h2>Add New Flower</h2>

    <form action="add-flower.php" method="POST">
        <label>Flower Name:</label><br>
        <input type="text" name="flower_name" required><br><br>

        <label>Price (LKR):</label><br>
        <input type="number" step="0.01" name="price" required><br><br>

        <label>Stock Quantity:</label><br>
        <input type="number" name="stock" required><br><br>

        <label>Category / Type:</label><br>
        <input type="text" name="category"><br><br>

        <label>Select Supplier:</label><br>
        <select name="supplier_id" required>
            <option value="">-- Choose Supplier --</option>
            <?php if ($suppliers_res && mysqli_num_rows($suppliers_res) > 0): ?>
                <?php while ($s = mysqli_fetch_assoc($suppliers_res)): 
                    $s_name = $s['supplier_name'] ?? $s['name'] ?? 'Supplier #' . $s['id'];
                ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars((string)$s_name); ?></option>
                <?php endwhile; ?>
            <?php endif; ?>
        </select><br><br>

        <button type="submit" name="save_flower">Save Flower</button>
    </form>

</body>
</html>