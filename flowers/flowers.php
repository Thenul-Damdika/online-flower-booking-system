<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$search = trim($_GET['search'] ?? '');

// Search flowers by name, category, or supplier
$query = "SELECT f.*, c.category_name, s.supplier_name
          FROM flowers f
          LEFT JOIN flower_categories c ON f.category_id = c.id
          LEFT JOIN suppliers s ON f.supplier_id = s.id
          WHERE f.status = 'Available'";

if ($search !== '') {
    $query .= " AND (
        f.flower_name LIKE ?
        OR c.category_name LIKE ?
        OR s.supplier_name LIKE ?
    )";
}

$query .= " ORDER BY f.id DESC";

if ($search !== '') {
    $stmt = mysqli_prepare($conn, $query);
    $like = "%$search%";
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $query);
}

$page_title = "Flowers";
$base_url = "../";

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Include primary external CSS file -->
<link rel="stylesheet" href="../css/style.css">

<!-- Custom page styles -->
<style>
.flowers-catalog-wrapper {
    padding: 50px 0 80px 0;
}

.page-header-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.page-header-action h2 {
    font-family: "Playfair Display", serif;
    font-size: 32px;
    color: var(--text, #294535);
}

.search-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-input {
    height: 44px;
    padding: 0 16px;
    border: 1px solid var(--border, #e5ddd9);
    border-radius: 22px;
    outline: none;
    font-size: 14px;
    width: 260px;
    transition: 0.2s ease;
}

.search-input:focus {
    border-color: var(--primary, #b85c70);
}

.btn-search {
    height: 44px;
    padding: 0 20px;
    background: var(--primary, #b85c70);
    color: var(--white, #ffffff);
    border: none;
    border-radius: 22px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: 0.25s ease;
}

.btn-search:hover {
    background: #963f55;
}

.btn-clear {
    background: var(--cream, #fffaf7);
    color: var(--text, #294535);
    border: 1px solid var(--border, #e5ddd9);
    padding: 11px 18px;
    border-radius: 22px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.25s ease;
}

.btn-clear:hover {
    background: var(--border, #e5ddd9);
}

.flower-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 25px;
}

.flower-card {
    background: var(--white, #ffffff);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-medium, 12px);
    overflow: hidden;
    box-shadow: var(--shadow-small, 0 4px 15px rgba(0,0,0,0.05));
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    display: flex;
    flex-direction: column;
}

.flower-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.flower-img-wrapper {
    width: 100%;
    height: 200px;
    background: var(--cream, #fffaf7);
    overflow: hidden;
}

.flower-img-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.flower-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.flower-card-body h3 {
    font-family: "Playfair Display", serif;
    font-size: 20px;
    color: var(--text, #294535);
    margin-bottom: 8px;
}

.flower-category {
    font-size: 13px;
    color: var(--text-light, #706b68);
    margin-bottom: 12px;
}

.flower-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    padding-top: 15px;
    border-top: 1px solid var(--border, #e5ddd9);
}

.flower-price {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary, #b85c70);
}

.flower-stock {
    font-size: 12px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 10px;
    background: var(--green-light, #eafaf1);
    color: var(--green-dark, #1e7e34);
}

.btn-details {
    display: block;
    text-align: center;
    margin-top: 15px;
    padding: 10px;
    background: var(--cream, #fffaf7);
    color: var(--text, #294535);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: var(--radius-small, 8px);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.25s ease;
}

.btn-details:hover {
    background: var(--primary, #b85c70);
    color: var(--white, #ffffff);
    border-color: var(--primary, #b85c70);
}

.no-results {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: var(--text-light, #706b68);
    font-size: 16px;
}
</style>

<main class="container">
    <div class="flowers-catalog-wrapper">
        
        <div class="page-header-action">
            <h2>🌸 Our Flowers</h2>

            <form method="GET" class="search-form">
                <input
                    type="search"
                    name="search"
                    class="search-input"
                    placeholder="Search flowers..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >
                <button type="submit" class="btn-search">Search</button>

                <?php if ($search !== ''): ?>
                    <a href="flowers.php" class="btn-clear">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="flower-grid">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($flower = mysqli_fetch_assoc($result)): ?>
                    <?php
                    $image = "../images/flower-placeholder.svg";
                    if (!empty($flower['image']) && file_exists("../images/flowers/" . $flower['image'])) {
                        $image = "../images/flowers/" . $flower['image'];
                    }
                    ?>
                    <div class="flower-card">
                        <div class="flower-img-wrapper">
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($flower['flower_name']); ?>">
                        </div>
                        <div class="flower-card-body">
                            <h3><?php echo htmlspecialchars($flower['flower_name']); ?></h3>
                            <div class="flower-category">
                                Category: <?php echo htmlspecialchars($flower['category_name'] ?? 'General'); ?>
                            </div>
                            
                            <div class="flower-meta">
                                <span class="flower-price">Rs. <?php echo number_format($flower['price'], 2); ?></span>
                                <span class="flower-stock"><?php echo (int)$flower['stock_quantity']; ?> in stock</span>
                            </div>

                            <a href="flower-details.php?id=<?php echo (int)$flower['id']; ?>" class="btn-details">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No flowers found matching your search criteria.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>