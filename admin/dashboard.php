<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Safe fetching total counts for flowers and suppliers
$flower_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM flowers");
$flower_data = $flower_query ? mysqli_fetch_assoc($flower_query) : array();
$flower_count = isset($flower_data['total']) ? $flower_data['total'] : 0;

$supplier_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM suppliers");
$supplier_data = $supplier_query ? mysqli_fetch_assoc($supplier_query) : array();
$supplier_count = isset($supplier_data['total']) ? $supplier_data['total'] : 0;

$admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';

require_once __DIR__ . '/../includes/header.php';
?>


<link rel="stylesheet" href="../css/style.css">


<style>
.dashboard-wrapper {
    padding: 50px 0 80px 0;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--white, #ffffff);
    padding: 25px 30px;
    border-radius: var(--radius-medium, 12px);
    border: 1px solid var(--border, #e5ddd9);
    box-shadow: var(--shadow-small, 0 4px 15px rgba(0,0,0,0.05));
    margin-bottom: 35px;
}

.dashboard-header h2 {
    font-family: "Playfair Display", serif;
    font-size: 28px;
    color: var(--text, #294535);
}

.logout-btn {
    background: #a43f4f;
    color: #ffffff;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 700;
    transition: 0.25s ease;
    text-decoration: none;
}

.logout-btn:hover {
    background: #822e3c;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--white, #ffffff);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-medium, 12px);
    padding: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: var(--shadow-small, 0 4px 15px rgba(0,0,0,0.05));
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--primary-light, #fde8e8);
    color: var(--primary, #b85c70);
    display: grid;
    place-items: center;
    font-size: 26px;
    flex-shrink: 0;
}

.stat-info h4 {
    font-size: 14px;
    color: var(--text-light, #706b68);
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-info strong {
    font-family: "Playfair Display", serif;
    font-size: 32px;
    color: var(--text, #294535);
}

.panel-section {
    background: var(--white, #ffffff);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-medium, 12px);
    padding: 30px;
    box-shadow: var(--shadow-small, 0 4px 15px rgba(0,0,0,0.05));
}

.panel-section h3 {
    font-family: "Playfair Display", serif;
    font-size: 22px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border, #e5ddd9);
}

.admin-menu-list {
    display: flex;
    gap: 15px;
    list-style: none;
}

.admin-menu-card {
    flex: 1;
    padding: 20px;
    background: var(--cream, #fffaf7);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-small, 8px);
    text-align: center;
    font-weight: 700;
    color: var(--green-dark, #1e7e34);
    transition: 0.25s ease;
    text-decoration: none;
}

.admin-menu-card:hover {
    background: var(--primary, #b85c70);
    color: #ffffff;
    border-color: var(--primary, #b85c70);
    transform: translateY(-3px);
}
</style>

<main class="container">
    <div class="dashboard-wrapper">
        
        <div class="dashboard-header">
            <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?>! 👋</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon">🌸</div>
                <div class="stat-info">
                    <h4>Total Flowers</h4>
                    <strong><?php echo $flower_count; ?></strong>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🚚</div>
                <div class="stat-info">
                    <h4>Total Suppliers</h4>
                    <strong><?php echo $supplier_count; ?></strong>
                </div>
            </div>
        </div>

        <div class="panel-section">
            <h3>Admin Panel Management</h3>
            <div class="admin-menu-list">
                <a href="flowers.php" class="admin-menu-card">
                    🌸 Manage Flowers
                </a>
                <a href="suppliers.php" class="admin-menu-card">
                    🚚 Manage Suppliers
                </a>
            </div>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>