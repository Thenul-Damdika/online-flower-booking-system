<?php
session_start();
require_once "../config/database.php";

// Only logged-in customers can view their deliveries
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer/login.php");
    exit;
}

$customerId = (int) $_SESSION['customer_id'];

/*
|--------------------------------------------------------------------------
| Fetch this customer's deliveries, joined with order info
|--------------------------------------------------------------------------
*/

$deliveryStmt = $conn->prepare(
    "SELECT d.id, d.order_id, d.delivery_address, d.delivery_city,
            d.delivery_date, d.delivery_time, d.delivery_status,
            d.delivery_person, o.total_amount, o.order_date
     FROM deliveries d
     INNER JOIN orders o ON o.id = d.order_id
     WHERE d.customer_id = ?
     ORDER BY d.created_at DESC"
);

$deliveryStmt->bind_param("i", $customerId);
$deliveryStmt->execute();
$deliveryResult = $deliveryStmt->get_result();

require_once "../includes/header.php";
?>

<style>
    .delivery-page {
        min-height: 70vh;
        padding: 70px 20px 80px;
        background: var(--cream, #fffaf7);
    }

    .delivery-container {
        width: min(1000px, 100%);
        margin: 0 auto;
    }

    .delivery-heading {
        text-align: center;
        margin-bottom: 40px;
    }

    .delivery-heading .section-label {
        display: inline-block;
        margin-bottom: 10px;
        color: var(--primary, #b85c70);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 2px;
    }

    .delivery-heading h1 {
        margin: 0;
        color: var(--text, #292624);
        font-size: 38px;
    }

    .delivery-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .delivery-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 22px 24px;
        background: var(--white, #ffffff);
        border: 1px solid var(--border, #eee5e1);
        border-radius: 16px;
        text-decoration: none;
        box-shadow: 0 8px 25px rgba(70, 45, 50, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .delivery-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 30px rgba(70, 45, 50, 0.1);
    }

    .delivery-card .order-id {
        font-weight: 700;
        color: var(--text, #292624);
        margin-bottom: 4px;
    }

    .delivery-card .address {
        color: var(--text-light, #706b68);
        font-size: 14px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-Pending { background: #fdf1d6; color: #9a7a1f; }
    .status-Preparing { background: #e4ecfb; color: #35538f; }
    .status-Out.for.Delivery, .status-OutforDelivery { background: #fdeecf; color: #a9631a; }
    .status-Delivered { background: #dff3e3; color: #2c7a44; }
    .status-Failed, .status-Cancelled { background: #fbe2e2; color: #a1332f; }

    .empty-message {
        padding: 50px 25px;
        text-align: center;
        background: var(--white, #ffffff);
        border: 1px solid var(--border, #eee5e1);
        border-radius: 18px;
        color: var(--text-light, #706b68);
    }

    @media (max-width: 600px) {
        .delivery-card {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<main class="delivery-page">
    <div class="delivery-container">

        <div class="delivery-heading">
            <span class="section-label">TRACK YOUR ORDERS</span>
            <h1>My Deliveries</h1>
        </div>

        <div class="delivery-list">

            <?php if ($deliveryResult && $deliveryResult->num_rows > 0): ?>

                <?php while ($delivery = $deliveryResult->fetch_assoc()): ?>

                    <?php $statusClass = "status-" . str_replace(" ", "", $delivery["delivery_status"]); ?>

                    <a href="delivery-details.php?id=<?= (int)$delivery['id'] ?>" class="delivery-card">
                        <div>
                            <div class="order-id">Order #<?= (int)$delivery['order_id'] ?></div>
                            <div class="address">
                                <?= htmlspecialchars($delivery['delivery_address']) ?><?= $delivery['delivery_city'] ? ', ' . htmlspecialchars($delivery['delivery_city']) : '' ?>
                            </div>
                        </div>
                        <span class="status-badge <?= $statusClass ?>">
                            <?= htmlspecialchars($delivery['delivery_status']) ?>
                        </span>
                    </a>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="empty-message">
                    You don't have any deliveries yet.
                </div>

            <?php endif; ?>

        </div>

    </div>
</main>

<?php require_once "../includes/footer.php"; ?>