<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Redirect if ID parameter is missing
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: flowers.php");
    exit();
}

$id = (int)$_GET['id'];

// Fetch flower details with category and supplier information
$query = "SELECT f.*, c.category_name, s.supplier_name, s.phone as supplier_phone, s.email as supplier_email
          FROM flowers f
          LEFT JOIN flower_categories c ON f.category_id = c.id
          LEFT JOIN suppliers s ON f.supplier_id = s.id
          WHERE f.id = ? LIMIT 1";

$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $flower = mysqli_fetch_assoc($result);
} else {
    $flower = false;
}

if (!$flower) {
    header("Location: flowers.php");
    exit();
}

$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

require_once __DIR__ . '/../includes/header.php';
?>


<link rel="stylesheet" href="../css/style.css">

<style>
.flower-details-wrapper {
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

.details-card {
    max-width: 750px;
    margin: 0 auto;
    background: var(--white, #ffffff);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-medium, 12px);
    box-shadow: var(--shadow-small, 0 4px 15px rgba(0,0,0,0.05));
    overflow: hidden;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
}

.styled-table th {
    width: 30%;
    background: var(--cream, #fffaf7);
    color: var(--text, #294535);
    font-weight: 700;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border, #e5ddd9);
    vertical-align: top;
}

.styled-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border, #e5ddd9);
    color: var(--text, #294535);
}

.styled-table tr:last-child th,
.styled-table tr:last-child td {
    border-bottom: none;
}

.badge-stock {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    background: var(--green-light, #eafaf1);
    color: var(--green-dark, #1e7e34);
}

.badge-out-of-stock {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    background: #fde8e8;
    color: #a43f4f;
}
</style>

<main class="container">
    <div class="flower-details-wrapper">
        
        <div class="page-header-action">
            <h2>🌸 Flower Details</h2>
            <?php if ($is_admin): ?>
                <a href="../admin/flowers.php" class="btn-secondary">&larr; Back to Admin Manage Flowers</a>
            <?php else: ?>
                <a href="flowers.php" class="btn-secondary">&larr; Back to Available Flowers</a>
            <?php endif; ?>
        </div>

        <div class="details-card">
            <table class="styled-table">
                <tr>
                    <th>Flower ID</th>
                    <td>#<?php echo (int)$flower['id']; ?></td>
                </tr>
                <tr>
                    <th>Flower Name</th>
                    <td><strong><?php echo htmlspecialchars($flower['flower_name']); ?></strong></td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td><?php echo htmlspecialchars($flower['category_name'] ?? 'General'); ?></td>
                </tr>
                <tr>
                    <th>Price (LKR)</th>
                    <td><strong>Rs. <?php echo number_format($flower['price'], 2); ?></strong></td>
                </tr>
                <tr>
                    <th>Stock Quantity</th>
                    <td>
                        <?php if ($flower['stock_quantity'] > 0): ?>
                            <span class="badge-stock"><?php echo (int)$flower['stock_quantity']; ?> units available</span>
                        <?php else: ?>
                            <span class="badge-out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Supplier Name</th>
                    <td><?php echo htmlspecialchars($flower['supplier_name'] ?? 'Not Assigned'); ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?php echo nl2br(htmlspecialchars($flower['description'] ?? 'No description available.')); ?></td>
                </tr>
            </table>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>