<?php
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Delete flower record if delete ID is passed
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM flowers WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    header("Location: flowers.php?msg=deleted");
    exit();
}

// Fetch all flowers with Supplier & Category details
$query = "SELECT f.*, s.supplier_name, c.category_name
          FROM flowers f
          LEFT JOIN suppliers s ON f.supplier_id = s.id
          LEFT JOIN flower_categories c ON f.category_id = c.id
          ORDER BY f.id DESC";

$result = mysqli_query($conn, $query);

require_once __DIR__ . '/../includes/header.php';
?>


<link rel="stylesheet" href="../css/style.css">


<style>
.admin-flowers-wrapper {
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

.action-links {
    display: flex;
    gap: 12px;
    align-items: center;
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

.alert-banner {
    padding: 12px 20px;
    border-radius: var(--radius-small, 8px);
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 25px;
}

.alert-success {
    background: var(--green-light, #eafaf1);
    color: var(--green-dark, #1e7e34);
    border: 1px solid var(--green, #27ae60);
}

.table-container {
    background: var(--white, #ffffff);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-medium, 12px);
    box-shadow: var(--shadow-small, 0 4px 15px rgba(0,0,0,0.05));
    overflow-x: auto;
}

.styled-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
}

.styled-table th {
    background: var(--cream, #fffaf7);
    color: var(--text, #294535);
    font-weight: 700;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border, #e5ddd9);
}

.styled-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border, #e5ddd9);
    color: var(--text, #294535);
}

.styled-table tbody tr:last-child td {
    border-bottom: none;
}

.styled-table tbody tr:hover {
    background: rgba(255, 250, 247, 0.6);
}

.badge-stock {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 700;
    background: var(--green-light, #eafaf1);
    color: var(--green-dark, #1e7e34);
}

.action-btn-group {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    transition: 0.2s ease;
    text-decoration: none;
}

.btn-view {
    background: var(--primary-light, #fde8e8);
    color: var(--primary-dark, #a43f4f);
}

.btn-edit {
    background: var(--green-light, #eafaf1);
    color: var(--green-dark, #1e7e34);
}

.btn-delete {
    background: #fde8e8;
    color: #a43f4f;
}

.btn-sm:hover {
    opacity: 0.85;
}
</style>

<main class="container">
    <div class="admin-flowers-wrapper">
        
        <div class="page-header-action">
            <h2>🌸 Manage Flowers</h2>
            <div class="action-links">
                <a href="dashboard.php" class="btn-secondary">&larr; Back to Dashboard</a>
                <a href="../flowers/add-flower.php" class="btn btn-primary">+ Add New Flower</a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert-banner alert-success">
                <?php 
                    if ($_GET['msg'] === 'deleted') echo "Flower deleted successfully!";
                    elseif ($_GET['msg'] === 'added') echo "Flower added successfully!";
                    elseif ($_GET['msg'] === 'updated') echo "Flower updated successfully!";
                ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="styled-table">
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
                            $category = isset($row['category_name']) ? $row['category_name'] : 'N/A';
                            $supplier = isset($row['supplier_name']) ? $row['supplier_name'] : 'Not Assigned';
                        ?>
                            <tr>
                                <td>#<?php echo (int)$row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['flower_name']); ?></strong></td>
                                <td>Rs. <?php echo number_format($row['price'], 2); ?></td>
                                <td><span class="badge-stock"><?php echo (int)$row['stock_quantity']; ?></span></td>
                                <td><?php echo htmlspecialchars($category); ?></td>
                                <td><?php echo htmlspecialchars($supplier); ?></td>
                                <td>
                                    <div class="action-btn-group">
                                        <a href="../flowers/flower-details.php?id=<?php echo (int)$row['id']; ?>" class="btn-sm btn-view">View</a>
                                        <a href="../flowers/edit-flower.php?id=<?php echo (int)$row['id']; ?>" class="btn-sm btn-edit">Edit</a>
                                        <a href="flowers.php?delete=<?php echo (int)$row['id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this flower?');">Delete</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-light, #706b68);">No flowers found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>