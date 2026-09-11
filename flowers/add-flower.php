<?php
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin/login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id    = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $supplier_id    = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
    $flower_name    = trim($_POST['flower_name'] ?? '');
    $description    = trim($_POST['description'] ?? '');
    $price          = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $stock_quantity = isset($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 0;

    if (empty($category_id)) {
        $error = "Please select a flower category!";
    } elseif ($flower_name === '') {
        $error = "Flower name is required!";
    } elseif ($price <= 0) {
        $error = "Price must be greater than 0!";
    } elseif ($stock_quantity < 0) {
        $error = "Stock quantity cannot be negative!";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO flowers (category_id, supplier_id, flower_name, description, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iissdi", $category_id, $supplier_id, $flower_name, $description, $price, $stock_quantity);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../admin/flowers.php?msg=added");
            exit();
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}

// Fetch categories and suppliers
$categories_result = mysqli_query($conn, "SELECT * FROM flower_categories WHERE status = 'Active'");
$suppliers_result  = mysqli_query($conn, "SELECT * FROM suppliers WHERE status = 'Active'");

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Include primary external CSS file -->
<link rel="stylesheet" href="../css/style.css">

<!-- Custom page styles -->
<style>
.add-flower-wrapper {
    padding: 50px 0 80px 0;
}

.page-header-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.page-header-action h2 {
    font-family: "Playfair Display", serif;
    font-size: 30px;
    color: var(--text, #294535);
}

.btn-secondary {
    background: var(--cream, #fffaf7);
    color: var(--text, #294535);
    border: 1px solid var(--border, #e5ddd9);
    padding: 10px 18px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    transition: 0.25s ease;
    text-decoration: none;
}

.btn-secondary:hover {
    background: var(--border, #e5ddd9);
}

.form-container {
    max-width: 650px;
    margin: 0 auto;
    background: var(--white, #ffffff);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-medium, 12px);
    padding: 35px;
    box-shadow: var(--shadow-small, 0 4px 15px rgba(0,0,0,0.05));
}

.alert-error {
    background: var(--primary-light, #fde8e8);
    color: var(--primary-dark, #a43f4f);
    border: 1px solid var(--primary, #b85b6c);
    padding: 12px 18px;
    border-radius: var(--radius-small, 8px);
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--text, #294535);
}

.form-control {
    width: 100%;
    height: 48px;
    padding: 0 15px;
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-small, 8px);
    background: var(--white, #ffffff);
    color: var(--text, #294535);
    font-size: 14px;
    outline: none;
    box-sizing: border-box;
    transition: 0.2s ease;
}

textarea.form-control {
    height: auto;
    padding: 12px 15px;
}

.form-control:focus {
    border-color: var(--primary, #b85c70);
    box-shadow: 0 0 0 3px rgba(184, 92, 112, 0.12);
}

.form-row {
    display: flex;
    gap: 15px;
}

.form-row .form-group {
    flex: 1;
}

.btn-submit {
    display: block;
    width: 100%;
    height: 50px;
    border: none;
    border-radius: var(--radius-small, 8px);
    background: var(--primary, #b85c70);
    color: var(--white, #ffffff);
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
}

.btn-submit:hover {
    background: #963f55;
}
</style>

<main class="container">
    <div class="add-flower-wrapper">
        
        <div class="page-header-action">
            <h2>🌸 Add New Flower</h2>
            <a href="../admin/flowers.php" class="btn-secondary">&larr; Back to Flowers List</a>
        </div>

        <div class="form-container">
            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="flower_name">Flower Name</label>
                    <input type="text" id="flower_name" name="flower_name" class="form-control" placeholder="e.g., Red Roses Bouquet" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (LKR)</label>
                        <input type="number" id="price" step="0.01" min="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" id="stock_quantity" min="0" name="stock_quantity" class="form-control" placeholder="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            <option value="">-- Choose Category --</option>
                            <?php if ($categories_result && mysqli_num_rows($categories_result) > 0): ?>
                                <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                                    <option value="<?php echo (int)$cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="supplier_id">Supplier (Optional)</label>
                        <select id="supplier_id" name="supplier_id" class="form-control">
                            <option value="">-- Choose Supplier --</option>
                            <?php if ($suppliers_result && mysqli_num_rows($suppliers_result) > 0): ?>
                                <?php while ($supplier = mysqli_fetch_assoc($suppliers_result)): ?>
                                    <option value="<?php echo (int)$supplier['id']; ?>">
                                        <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-control" placeholder="Write a short description about the flower..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Save Flower</button>
            </form>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>