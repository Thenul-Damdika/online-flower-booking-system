<?php
session_start();
require_once "../config/database.php";

// Only admins can update delivery status
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

$deliveryId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['delivery_status'];
    $person = trim($_POST['delivery_person']);
    $phone = trim($_POST['delivery_phone']);
    $date = $_POST['delivery_date'] ?: null;

    $update = $conn->prepare(
        "UPDATE deliveries
         SET delivery_status = ?, delivery_person = ?, delivery_phone = ?, delivery_date = ?
         WHERE id = ?"
    );
    $update->bind_param("ssssi", $status, $person, $phone, $date, $deliveryId);
    $update->execute();
    $message = "Delivery updated successfully.";
}

$stmt = $conn->prepare("SELECT * FROM deliveries WHERE id = ?");
$stmt->bind_param("i", $deliveryId);
$stmt->execute();
$delivery = $stmt->get_result()->fetch_assoc();

if (!$delivery) {
    die("Delivery not found.");
}

require_once "../includes/header.php";
?>

<main style="max-width:600px;margin:60px auto;padding:0 20px;">
    <h1>Update Delivery #<?= (int)$delivery['id'] ?></h1>

    <?php if ($message): ?>
        <p style="color:green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" style="display:flex;flex-direction:column;gap:14px;">
        <label>
            Status
            <select name="delivery_status" required>
                <?php
                $statuses = ['Pending','Preparing','Out for Delivery','Delivered','Failed','Cancelled'];
                foreach ($statuses as $s):
                ?>
                    <option value="<?= $s ?>" <?= $delivery['delivery_status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Delivery Person
            <input type="text" name="delivery_person" value="<?= htmlspecialchars($delivery['delivery_person'] ?? '') ?>">
        </label>

        <label>
            Delivery Phone
            <input type="text" name="delivery_phone" value="<?= htmlspecialchars($delivery['delivery_phone'] ?? '') ?>">
        </label>

        <label>
            Delivery Date
            <input type="date" name="delivery_date" value="<?= htmlspecialchars($delivery['delivery_date'] ?? '') ?>">
        </label>

        <button type="submit">Update Delivery</button>
    </form>
</main>

<?php require_once "../includes/footer.php"; ?>