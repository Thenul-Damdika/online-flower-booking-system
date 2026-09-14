<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../customer/login.php");
    exit();
}

$customerId = (int) $_SESSION["customer_id"];
$orderId = (int)($_GET["order_id"] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}

$stmt = $conn->prepare(
    "SELECT
        o.id,
        o.total_amount,
        o.order_status,
        p.payment_method,
        p.card_holder_name,
        p.card_last_four,
        p.payment_status,
        p.transaction_id,
        p.payment_date
     FROM orders o
     LEFT JOIN payments p
        ON p.order_id = o.id
     WHERE o.id = ? AND o.customer_id = ?
     ORDER BY p.id DESC
     LIMIT 1"
);

$stmt->bind_param("ii", $orderId, $customerId);
$stmt->execute();

$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Order not found.");
}

require_once "../includes/header.php";
?>

<style>
    .payment-page {
        min-height: 70vh;
        padding: 70px 20px;
        background: var(--cream, #fffaf7);
    }

    .payment-container {
        width: min(760px, 100%);
        margin: 0 auto;
    }

    .payment-success-card {
        background: var(--white, #ffffff);
        border: 1px solid var(--border, #eee5e1);
        border-radius: 24px;
        padding: 48px 45px;
        text-align: center;
        box-shadow: 0 14px 40px rgba(70, 45, 50, 0.08);
    }

    .success-icon {
        width: 78px;
        height: 78px;
        margin: 0 auto 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--green-light, #edf4ef);
        color: var(--green, #3f604d);
        border: 2px solid #d6e7da;
        font-size: 42px;
        font-weight: 700;
    }

    .order-eyebrow {
        display: inline-block;
        margin-bottom: 10px;
        color: var(--primary, #b85c70);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 2px;
    }

    .payment-success-card h1 {
        margin: 0;
        color: var(--text, #292624);
        font-size: 38px;
        line-height: 1.2;
    }

    .payment-success-card > p {
        margin: 12px 0 30px;
        color: var(--text-light, #706b68);
        font-size: 16px;
        line-height: 1.6;
    }

    .success-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin: 30px 0 22px;
        text-align: left;
    }

    .success-details > div {
        padding: 18px 20px;
        background: var(--cream, #fffaf7);
        border: 1px solid var(--border, #eee5e1);
        border-radius: 14px;
    }

    .success-details span {
        display: block;
        margin-bottom: 7px;
        color: var(--text-light, #706b68);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    .success-details strong {
        display: block;
        color: var(--text, #292624);
        font-size: 17px;
    }

    .transaction-text {
        padding: 14px 18px;
        margin: 0 0 28px !important;
        background: var(--green-light, #edf4ef);
        border-radius: 10px;
        color: var(--green-dark, #294535) !important;
        font-size: 14px !important;
        word-break: break-word;
    }

    .card-info {
        padding: 14px 18px;
        margin: 0 0 18px;
        background: var(--primary-light, #f8e7eb);
        border-radius: 10px;
        color: var(--primary-dark, #963f55);
        font-size: 14px;
        text-align: left;
    }

    .card-info strong {
        color: var(--text, #292624);
    }

    .success-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .order-primary-btn,
    .order-secondary-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 24px;
        border-radius: 10px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .order-primary-btn {
        background: var(--primary, #b85c70);
        color: #ffffff;
        border: 1px solid var(--primary, #b85c70);
    }

    .order-primary-btn:hover {
        background: var(--primary-dark, #963f55);
        border-color: var(--primary-dark, #963f55);
        transform: translateY(-1px);
    }

    .order-secondary-btn {
        background: #ffffff;
        color: var(--primary, #b85c70);
        border: 1px solid var(--border, #eee5e1);
    }

    .order-secondary-btn:hover {
        background: var(--primary-light, #f8e7eb);
        border-color: var(--primary, #b85c70);
        transform: translateY(-1px);
    }

    @media (max-width: 600px) {
        .payment-page {
            padding: 45px 15px;
        }

        .payment-success-card {
            padding: 35px 22px;
            border-radius: 18px;
        }

        .success-icon {
            width: 68px;
            height: 68px;
            font-size: 36px;
        }

        .payment-success-card h1 {
            font-size: 30px;
        }

        .success-details {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .success-details > div {
            padding: 15px 17px;
        }

        .success-actions {
            flex-direction: column;
        }

        .order-primary-btn,
        .order-secondary-btn {
            width: 100%;
        }
    }

    @media (max-width: 400px) {
        .payment-success-card {
            padding: 30px 17px;
        }

        .payment-success-card h1 {
            font-size: 27px;
        }
    }
</style>

<main class="payment-page">
    <div class="payment-container">

        <section class="payment-success-card">

            <div class="success-icon">✓</div>

            <span class="order-eyebrow">ORDER CONFIRMED</span>

            <h1>Thank You!</h1>

            <p>
                Your flower order has been placed successfully.
            </p>

            <div class="success-details">

                <div>
                    <span>Order Number</span>
                    <strong>
                        #<?= (int)$data["id"] ?>
                    </strong>
                </div>

                <div>
                    <span>Total Amount</span>
                    <strong>
                        Rs. <?= number_format((float)$data["total_amount"], 2) ?>
                    </strong>
                </div>

                <div>
                    <span>Payment Method</span>
                    <strong>
                        <?= htmlspecialchars($data["payment_method"] ?? "—") ?>
                    </strong>
                </div>

                <div>
                    <span>Payment Status</span>
                    <strong>
                        <?= htmlspecialchars($data["payment_status"] ?? "Pending") ?>
                    </strong>
                </div>

            </div>

            <?php if (!empty($data["card_last_four"])): ?>

                <div class="card-info">
                    Card ending in
                    <strong>
                        **** <?= htmlspecialchars($data["card_last_four"]) ?>
                    </strong>

                    <?php if (!empty($data["card_holder_name"])): ?>
                        <br>
                        Card Holder:
                        <strong>
                            <?= htmlspecialchars($data["card_holder_name"]) ?>
                        </strong>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($data["transaction_id"])): ?>

                <p class="transaction-text">
                    Transaction ID:
                    <strong>
                        <?= htmlspecialchars($data["transaction_id"]) ?>
                    </strong>
                </p>

            <?php endif; ?>

            <div class="success-actions">

                <a
                    href="../orders/order-details.php?id=<?= (int)$data["id"] ?>"
                    class="order-primary-btn"
                >
                    View Order
                </a>

                <a
                    href="../index.php"
                    class="order-secondary-btn"
                >
                    Continue Shopping
                </a>

            </div>

        </section>

    </div>
</main>

<?php require_once "../includes/footer.php"; ?>