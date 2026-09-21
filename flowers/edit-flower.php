<?php
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin/login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';

// Validate flower ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../admin/flowers.php");
    exit();
}

$id = (int)$_GET['id'];
$error = '';

// Fetch existing flower details first (needed for getting current image name)
$stmt = mysqli_prepare($conn, "SELECT * FROM flowers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$flower = mysqli_fetch_assoc($result);

if (!$flower) {
    header("Location: ../admin/flowers.php");
    exit();
}

// Process form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flower_name    = trim($_POST['flower_name'] ?? '');
    $price          = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $stock_quantity = isset($_POST['stock_quantity']) ? (int)$_POST['stock_quantity'] : 0;
    $supplier_id    = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null;
    $description    = trim($_POST['description'] ?? '');

    // Keep existing image as default
    $image_name = $flower['image'] ?? '';

    // Check if new image is uploaded
    if (isset($_FILES['flower_image']) && $_FILES['flower_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp   = $_FILES['flower_image']['tmp_name'];
        $original_name = $_FILES['flower_image']['name'];
        $file_ext   = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (in_array($file_ext, $allowed_extensions)) {
            // Generate unique image name
            $new_image_name = time() . '_' . uniqid() . '.' . $file_ext;
            $upload_directory = __DIR__ . '/../images/';

            if (!file_exists($upload_directory)) {
                mkdir($upload_directory, 0777, true);
            }

            $target_file = $upload_directory . $new_image_name;

            if (move_uploaded_file($file_tmp, $target_file)) {
                // Delete old image if it exists
                if (!empty($flower['image']) && file_exists($upload_directory . $flower['image'])) {
                    unlink($upload_directory . $flower['image']);
                }
                $image_name = $new_image_name;
            } else {
                $error = "Failed to upload new image!";
            }
        } else {
            $error = "Invalid image format! Only JPG, JPEG, PNG, WEBP, and GIF are allowed.";
        }
    }

    if (empty($error)) {
        if ($flower_name === '') {
            $error = "Flower name is required!";
        } elseif ($price <= 0) {
            $error = "Price must be greater than 0!";
        } elseif ($stock_quantity < 0) {
            $error = "Stock quantity cannot be negative!";
        } else {
            // Updated SQL query including image column
            $stmt = mysqli_prepare($conn, "UPDATE flowers SET flower_name = ?, price = ?, stock_quantity = ?, supplier_id = ?, description = ?, image = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sdiissi", $flower_name, $price, $stock_quantity, $supplier_id, $description, $image_name, $id);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: ../admin/flowers.php?msg=updated");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch active suppliers list
$suppliers_result = mysqli_query($conn, "SELECT * FROM suppliers");

require_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="../css/style.css">

<style>
.edit-flower-wrapper {
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

input[type="file"].form-control {
    padding-top: 10px;
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

.current-img-preview {
    margin-bottom: 10px;
}

.current-img-preview img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e5ddd9;
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
    <div class="edit-flower-wrapper">
        
        <div class="page-header-action">
            <h2>✏️ Edit Flower</h2>
            <a href="../admin/flowers.php" class="btn-secondary">&larr; Back to Flowers List</a>
        </div>

        <div class="form-container">
            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="flower_name">Flower Name</label>
                    <input type="text" id="flower_name" name="flower_name" class="form-control" value="<?php echo htmlspecialchars($flower['flower_name']); ?>" required>
                </div>

                <!-- Flower Image Upload & Preview Field -->
                <div class="form-group">
                    <label for="flower_image">Flower Image</label>
                    <?php if (!empty($flower['image']) && file_exists(__DIR__ . '/../images/' . $flower['image'])): ?>
                        <div class="current-img-preview">
                            <img src="../images/<?php echo htmlspecialchars($flower['image']); ?>" alt="Current Image">
                            <p style="font-size: 12px; color: #666; margin-top: 4px;">Current Image</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="flower_image" name="flower_image" class="form-control" accept="image/*">
                    <small style="color: #777; font-size: 12px;">Leave empty if you don't want to change the image.</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (LKR)</label>
                        <input type="number" id="price" step="0.01" min="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($flower['price']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="stock_quantity">Stock Quantity</label>
                        <input type="number" id="stock_quantity" min="0" name="stock_quantity" class="form-control" value="<?php echo htmlspecialchars($flower['stock_quantity']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="supplier_id">Select Supplier</label>
                    <select id="supplier_id" name="supplier_id" class="form-control">
                        <option value="">-- Choose Supplier --</option>
                        <?php if ($suppliers_result && mysqli_num_rows($suppliers_result) > 0): ?>
                            <?php while ($supplier = mysqli_fetch_assoc($suppliers_result)): ?>
                                <option value="<?php echo (int)$supplier['id']; ?>" <?php if ($flower['supplier_id'] == $supplier['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($supplier['supplier_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" class="form-control"><?php echo htmlspecialchars($flower['description']); ?></textarea>
                </div>

                <button type="submit" class="btn-submit">Update Flower</button>
            </form>
        </div>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>