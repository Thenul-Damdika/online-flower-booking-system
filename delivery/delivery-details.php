<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer/login.php");
    exit;
}

$customerId = (int) $_SESSION['customer_id'];
$deliveryId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $conn->prepare(
    "SELECT d.*, o.total_amount, o.order_status, o.order_date
     FROM deliveries d
     INNER JOIN orders o ON o.id = d.order_id
     WHERE d.id = ? AND d.customer_id = ?"
);
$stmt->bind_param("ii", $deliveryId, $customerId);
$stmt->execute();
$result = $stmt->get_result();
$delivery = $result->fetch_assoc();

if (!$delivery) {
    header("Location: delivery.php");
    exit;
}

require_once "../includes/header.php";
?>

<style>
    .delivery-details-page {
        min-height: 70vh;
        padding: 70px 20px 80px;
        background: var(--cream, #fffaf7);
    }

    .details-container {
        width: min(700px, 100%);
        margin: 0 auto;
        background: var(--white, #ffffff);
        border: 1px solid var(--border, #eee5e1);
        border-radius: 18px;
        padding: 40px;
        box-shadow: 0 8px 25px rgba(70, 45, 50, 0.06);
    }

    .details-container h1 {
        margin: 0 0 6px;
        color: var(--text, #292624);
        font-size: 28px;
    }

    .details-container .sub {
        color: var(--text-light, #706b68);
        margin-bottom: 30px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 14px 0;
        border-bottom: 1px solid var(--border, #eee5e1);
    }

    .detail-row:last-child { border-bottom: none; }

    .detail-row .label {
        color: var(--text-light, #706b68);
        font-size: 14px;
    }

    .detail-row .value {
        color: var(--text, #292624);
        font-weight: 600;
        text-align: right;
    }

    .back-link {
        display: inline-block;
        margin-bottom: 20px;
        color: var(--primary, #b85c70);
        text-decoration: none;
        font-weight: 600;
    }
</style>

<main class="delivery-details-page">
    <div class="details-container">

        <a href="delivery.php" class="back-link">&larr; Back to my deliveries</a>

        <h1>Order #<?= (int)$delivery['order_id'] ?></h1>
        <div class="sub">Placed on <?= date("d M Y", strtotime($delivery['order_date'])) ?></div>

        <div class="detail-row">
            <span class="label">Delivery Status</span>
            <span class="value"><?= htmlspecialchars($delivery['delivery_status']) ?></span>
        </div>
        <div class="detail-row">
            <span class="label">Delivery Address</span>
            <span class="value"><?= htmlspecialchars($delivery['delivery_address']) ?></span>
        </div>
        <div class="detail-row">
            <span class="label">City</span>
            <span class="value"><?= htmlspecialchars($delivery['delivery_city'] ?? '-') ?></span>
        </div>
        <div class="detail-row">
            <span class="label">Delivery Date</span>
            <span class="value"><?= $delivery['delivery_date'] ? date("d M Y", strtotime($delivery['delivery_date'])) : 'Not scheduled' ?></span>
        </div>
        <div class="detail-row">
            <span class="label">Delivery Time</span>
            <span class="value"><?= htmlspecialchars($delivery['delivery_time'] ?? '-') ?></span>
        </div>
        <div class="detail-row">
            <span class="label">Delivered By</span>
            <span class="value"><?= htmlspecialchars($delivery['delivery_person'] ?? 'Not assigned yet') ?></span>
        </div>
        <div class="detail-row">
            <span class="label">Order Total</span>
            <span class="value">Rs. <?= number_format($delivery['total_amount'], 2) ?></span>
        </div>

        <?php if (!empty($delivery['notes'])): ?>
        <div class="detail-row">
            <span class="label">Notes</span>
            <span class="value"><?= htmlspecialchars($delivery['notes']) ?></span>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php require_once "../includes/footer.php"; ?>