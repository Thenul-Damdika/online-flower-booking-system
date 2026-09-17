<?php
// Check admin session
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin/login.php");
    exit();
}

// Include database connection
require_once '../config/database.php';

$error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Insert supplier into database
    if (!empty($supplier_name)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO suppliers (supplier_name, phone, email, address) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $supplier_name, $phone, $email, $address);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../admin/suppliers.php?msg=added");
            exit();
        } else {
            $error = "Failed to add supplier.";
        }
    } else {
        $error = "Supplier Name is required.";
    }
}


require_once '../includes/header.php';
?>


<link rel="stylesheet" href="../css/style.css">


<style>
.admin-add-supplier-wrapper {
    padding: 40px 20px 80px 20px;
    max-width: 1100px;
    margin: 0 auto;
}

.page-header-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-header-action h2 {
    font-family: "Playfair Display", serif;
    font-size: 36px;
    font-weight: 700;
    color: var(--text, #294535);
    margin: 0;
}

.btn-back-link {
    background: #ffffff;
    color: var(--text, #294535);
    border: 1px solid var(--border, #e5ddd9);
    padding: 10px 22px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    transition: 0.25s ease;
    text-decoration: none;
}

.btn-back-link:hover {
    background: var(--border, #e5ddd9);
}

.form-card {
    background: var(--white, #ffffff);
    border: 1px solid var(--border, #e5ddd9);
    border-radius: 24px;
    padding: 40px;
    max-width: 650px;
    margin: 0 auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.form-grid-single {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 700;
    color: var(--text, #294535);
}

.input-field {
    width: 100%;
    padding: 14px 18px;
    border: 1px solid transparent;
    border-radius: 12px;
    font-size: 14px;
    background: #edf2fc;
    color: #292624;
    outline: none;
    box-sizing: border-box;
    transition: 0.25s ease;
}

.input-field:focus {
    background: #ffffff;
    border-color: #f0a8b8;
    box-shadow: 0 0 0 3px rgba(240, 168, 184, 0.25);
}

textarea.input-field {
    resize: vertical;
    min-height: 120px;
    background: #ffffff;
    border: 1px solid #f0a8b8;
}

/* Styled & Centered Button Alignment */
.form-actions {
    margin-top: 15px;
}

.btn-submit {
    width: 100%; 
    padding: 14px 0;
    background: #8b3a4c;
    color: #ffffff;
    border: none;
    border-radius: 25px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.25s ease;
    box-shadow: 0 4px 12px rgba(139, 58, 76, 0.25);
}

.btn-submit:hover {
    background: #6e2d3b;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(139, 58, 76, 0.35);
}

.alert-error {
    background: #fde8e8;
    color: #a43f4f;
    border: 1px solid #b85c70;
    padding: 12px 18px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 25px;
    font-weight: 600;
    max-width: 650px;
    margin-left: auto;
    margin-right: auto;
}
</style>

<main class="container">
    <div class="admin-add-supplier-wrapper">
        
        
        <div class="page-header-action">
            <h2>🚚 Add New Supplier</h2>
            <a href="../admin/suppliers.php" class="btn-back-link">&larr; Back to Manage</a>
        </div>

        
        <?php if (!empty($error)): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

       
        <div class="form-card">
            <form action="" method="POST">
                
                <div class="form-grid-single">
                    <div class="form-group">
                        <label for="supplier_name">Supplier Name</label>
                        <input type="text" id="supplier_name" name="supplier_name" class="input-field" placeholder="Green Valley Flora" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" class="input-field" placeholder="+94724635740">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="input-field" placeholder="greenvalley@gmail.com">
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="input-field" placeholder="Supplier street address or location details..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Save Supplier</button>
                    </div>
                </div>

            </form>
        </div>

    </div>
</main>

<?php require_once '../includes/footer.php'; ?>