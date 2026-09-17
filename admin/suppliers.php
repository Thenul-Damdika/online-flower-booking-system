<?php
// Check admin session
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Delete supplier record if delete ID is passed
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM suppliers WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    header("Location: suppliers.php?msg=deleted");
    exit();
}

// Fetch all suppliers from database
$query = "SELECT * FROM suppliers ORDER BY id DESC";
$result = mysqli_query($conn, $query);


require_once __DIR__ . '/../includes/header.php';
?>


<link rel="stylesheet" href="../css/style.css">


<style>
.admin-suppliers-wrapper {
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

.btn-primary {
    background: var(--primary, #b85c70);
    color: #ffffff;
    padding: 10px 18px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.25s ease;
}

.btn-primary:hover {
    background: #963f55;
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
    background: #eafaf1;
    color: #1e7e34;
    border: 1px solid #27ae60;
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

.btn-delete {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    background: #fde8e8;
    color: #a43f4f;
    text-decoration: none;
    transition: 0.22s ease;
}

.btn-delete:hover {
    opacity: 0.85;
}
</style>

<main class="container">
    <div class="admin-suppliers-wrapper">
        
        <div class="page-header-action">
            <h2>🚚 Manage Suppliers</h2>
            <div class="action-links">
                <a href="dashboard.php" class="btn-secondary">&larr; Back to Dashboard</a>
                <!-- Link updated to redirect correctly to suppliers form page -->
                <a href="../suppliers/suppliers.php" class="btn-primary">+ Add New Supplier</a>
            </div>
        </div>

        <!-- Success Alert Message -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert-banner alert-success">
                <?php 
                    if ($_GET['msg'] === 'deleted') echo "Supplier deleted successfully!";
                    elseif ($_GET['msg'] === 'added') echo "Supplier added successfully!";
                ?>
            </div>
        <?php endif; ?>

        <!-- Suppliers Data Table -->
        <div class="table-container">
            <table class="styled-table">
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
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo (int)$row['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($row['supplier_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="suppliers.php?delete=<?php echo (int)$row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-light, #706b68);">No suppliers found in the database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>