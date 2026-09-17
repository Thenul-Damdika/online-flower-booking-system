<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../customer/login.php");
    exit();
}

$customerId = (int) $_SESSION["customer_id"];
$orderId = (int)($_GET["id"] ?? 0);

if ($orderId <= 0) {
    header("Location: orders.php");
    exit();
}

$orderStmt = $conn->prepare(
    "SELECT id, total_amount, order_status, shipping_address, shipping_city,
            shipping_postal_code, order_date
     FROM orders
     WHERE id = ? AND customer_id = ?"
);
$orderStmt->bind_param("ii", $orderId, $customerId);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$itemStmt = $conn->prepare(
    "SELECT oi.quantity, oi.price, oi.subtotal, f.flower_name, f.image
     FROM order_items oi
     INNER JOIN flowers f ON oi.flower_id = f.id
     WHERE oi.order_id = ?
     ORDER BY oi.id ASC"
);
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$items = $itemStmt->get_result();

$paymentStmt = $conn->prepare(
    "SELECT payment_method, payment_status, transaction_id, payment_date
     FROM payments
     WHERE order_id = ?
     ORDER BY id DESC LIMIT 1"
);
$paymentStmt->bind_param("i", $orderId);
$paymentStmt->execute();
$payment = $paymentStmt->get_result()->fetch_assoc();

require_once "../includes/header.php";
?>

<style>

/* =========================================================
   ORDER DETAILS
   Bloom Heaven theme
   ========================================================= */

.order-page {
    min-height: 100vh;
    padding: 70px 20px 90px;
    background: #fffaf7;
}

.order-container {
    width: min(100%, 1200px);
    margin: 0 auto;
}


/* =========================================================
   HEADING
   ========================================================= */

.order-heading {
    text-align: center;
    max-width: 700px;
    margin: 0 auto 45px;
}

.order-heading.compact {
    margin-bottom: 35px;
}

.order-eyebrow {
    display: inline-block;
    margin-bottom: 10px;
    color: #b85c70;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
}

.order-heading h1 {
    margin: 0 0 10px;
    color: #292624;
    font-family: "Playfair Display", serif;
    font-size: clamp(36px, 5vw, 52px);
    line-height: 1.15;
}

.order-heading p {
    margin: 0;
    color: #706b68;
    font-size: 13px;
}


/* =========================================================
   MAIN DETAILS LAYOUT
   ========================================================= */

.details-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.55fr) minmax(320px, 0.9fr);
    gap: 28px;
    align-items: start;
}


/* =========================================================
   CARDS
   ========================================================= */

.order-card {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(45, 35, 30, 0.07);
}


/* =========================================================
   ORDER TOP
   ========================================================= */

.details-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding-bottom: 25px;
    border-bottom: 1px solid #eee5e1;
}

.detail-label {
    display: block;
    margin-bottom: 9px;
    color: #706b68;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.detail-total {
    text-align: right;
}

.detail-total span {
    display: block;
    margin-bottom: 4px;
    color: #706b68;
    font-size: 11px;
}

.detail-total strong {
    color: #963f55;
    font-size: 23px;
}


/* =========================================================
   STATUS BADGES
   ========================================================= */

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 7px 14px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 700;
}

.status-pending {
    background: #fff4dc;
    color: #9a6b1f;
}

.status-confirmed {
    background: #edf4ef;
    color: #294535;
}

.status-processing {
    background: #edf4ef;
    color: #294535;
}

.status-shipped {
    background: #edf4ef;
    color: #294535;
}

.status-delivered {
    background: #edf4ef;
    color: #294535;
}

.status-cancelled {
    background: #f8e7eb;
    color: #963f55;
}


/* =========================================================
   SECTION HEADING
   ========================================================= */

.section-heading {
    margin: 28px 0 18px;
    color: #292624;
    font-family: "Playfair Display", serif;
    font-size: 22px;
}

.section-heading:first-child {
    margin-top: 0;
}


/* =========================================================
   ORDERED FLOWERS
   ========================================================= */

.detail-items {
    display: flex;
    flex-direction: column;
}

.detail-item {
    display: grid;
    grid-template-columns: 68px minmax(0, 1fr) auto;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #eee5e1;
}

.detail-item:first-child {
    padding-top: 0;
}

.detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.detail-item img {
    width: 68px;
    height: 68px;
    object-fit: cover;
    border-radius: 12px;
    background: #f8e7eb;
}

.detail-item div {
    display: flex;
    flex-direction: column;
    gap: 5px;
    min-width: 0;
}

.detail-item div strong {
    color: #292624;
    font-size: 14px;
}

.detail-item div span {
    color: #706b68;
    font-size: 12px;
}

.detail-item > strong {
    color: #294535;
    font-size: 13px;
    white-space: nowrap;
}


/* =========================================================
   DELIVERY / PAYMENT INFORMATION
   ========================================================= */

.info-block {
    padding: 13px 0;
    border-bottom: 1px solid #eee5e1;
}

.info-block:last-of-type {
    border-bottom: none;
}

.info-block span {
    display: block;
    margin-bottom: 5px;
    color: #706b68;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-block strong {
    display: block;
    color: #292624;
    font-size: 13px;
    line-height: 1.6;
    font-weight: 600;
    word-break: break-word;
}

.payment-heading {
    padding-top: 8px;
    border-top: 1px solid #eee5e1;
}


/* =========================================================
   ACTION BUTTONS
   ========================================================= */

.detail-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 25px;
}

.detail-actions form {
    margin: 0;
}

.danger-btn {
    width: 100%;
    min-height: 48px;
    border: 1px solid #b85c70;
    border-radius: 30px;
    background: transparent;
    color: #963f55;
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.25s ease;
}

.danger-btn:hover {
    background: #b85c70;
    color: #ffffff;
}

.order-secondary-btn {
    width: 100%;
    min-height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #3f604d;
    border-radius: 30px;
    background: transparent;
    color: #294535;
    padding: 0 20px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: 0.25s ease;
}

.order-secondary-btn:hover {
    background: #3f604d;
    color: #ffffff;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 900px) {

    .details-layout {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 768px) {

    .order-page {
        padding: 50px 15px 70px;
    }

    .order-card {
        padding: 22px;
    }

    .details-top {
        align-items: flex-start;
        flex-direction: column;
    }

    .detail-total {
        text-align: left;
    }

    .detail-total strong {
        font-size: 21px;
    }

}

@media (max-width: 480px) {

    .order-page {
        padding: 40px 12px 60px;
    }

    .order-card {
        padding: 18px;
    }

    .order-heading h1 {
        font-size: 34px;
    }

    .detail-item {
        grid-template-columns: 55px minmax(0, 1fr);
        gap: 11px;
    }

    .detail-item img {
        width: 55px;
        height: 55px;
    }

    .detail-item > strong {
        grid-column: 2;
    }

}

</style>


<main class="order-page">

    <div class="order-container">

        <div class="order-heading compact">

            <span class="order-eyebrow">
                ORDER DETAILS
            </span>

            <h1>
                Order #<?= (int)$order["id"] ?>
            </h1>

            <p>
                Placed on
                <?= date(
                    "d M Y, h:i A",
                    strtotime($order["order_date"])
                ) ?>
            </p>

        </div>


        <div class="details-layout">


            <!-- ORDER INFORMATION -->

            <section class="order-card">

                <div class="details-top">

                    <div>

                        <span class="detail-label">
                            Order Status
                        </span>

                        <span class="status-badge status-<?= htmlspecialchars(
                            strtolower(
                                str_replace(
                                    " ",
                                    "-",
                                    $order["order_status"]
                                )
                            )
                        ) ?>">

                            <?= htmlspecialchars(
                                $order["order_status"]
                            ) ?>

                        </span>

                    </div>


                    <div class="detail-total">

                        <span>Total</span>

                        <strong>
                            Rs.
                            <?= number_format(
                                (float)$order["total_amount"],
                                2
                            ) ?>
                        </strong>

                    </div>

                </div>


                <h2 class="section-heading">
                    Ordered Flowers
                </h2>


                <div class="detail-items">

                    <?php while ($item = $items->fetch_assoc()): ?>

                        <div class="detail-item">

                            <img
                                src="<?= htmlspecialchars(
                                    !empty($item["image"])
                                    ? "../images/" . $item["image"]
                                    : "../images/default-flower.jpg"
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $item["flower_name"]
                                ) ?>"
                                onerror="this.src='../images/default-flower.jpg';"
                            >


                            <div>

                                <strong>
                                    <?= htmlspecialchars(
                                        $item["flower_name"]
                                    ) ?>
                                </strong>

                                <span>
                                    <?= (int)$item["quantity"] ?>
                                    × Rs.
                                    <?= number_format(
                                        (float)$item["price"],
                                        2
                                    ) ?>
                                </span>

                            </div>


                            <strong>
                                Rs.
                                <?= number_format(
                                    (float)$item["subtotal"],
                                    2
                                ) ?>
                            </strong>

                        </div>

                    <?php endwhile; ?>

                </div>

            </section>


            <!-- DELIVERY + PAYMENT -->

            <aside class="order-card">

                <h2 class="section-heading">
                    Delivery
                </h2>


                <div class="info-block">

                    <span>Address</span>

                    <strong>
                        <?= nl2br(
                            htmlspecialchars(
                                $order["shipping_address"]
                            )
                        ) ?>
                    </strong>

                </div>


                <div class="info-block">

                    <span>City</span>

                    <strong>
                        <?= htmlspecialchars(
                            $order["shipping_city"] ?: "—"
                        ) ?>
                    </strong>

                </div>


                <div class="info-block">

                    <span>Postal Code</span>

                    <strong>
                        <?= htmlspecialchars(
                            $order["shipping_postal_code"] ?: "—"
                        ) ?>
                    </strong>

                </div>


                <h2 class="section-heading payment-heading">
                    Payment
                </h2>


                <div class="info-block">

                    <span>Method</span>

                    <strong>
                        <?= htmlspecialchars(
                            $payment["payment_method"] ?? "—"
                        ) ?>
                    </strong>

                </div>


                <div class="info-block">

                    <span>Status</span>

                    <strong>
                        <?= htmlspecialchars(
                            $payment["payment_status"] ?? "Pending"
                        ) ?>
                    </strong>

                </div>


                <div class="detail-actions">

                    <?php if ($order["order_status"] === "Pending"): ?>

                    <a href="../payment/payment.php?order_id=<?= (int)$order["id"] ?>"
                       class="order-secondary-btn"
                    >
                       Pay Now
                    </a>

                        <form
                            action="cancel-order.php"
                            method="POST"
                            onsubmit="return confirm(
                                'Are you sure you want to cancel this order?'
                            );"
                        >

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?= (int)$order["id"] ?>"
                            >

                            <button
                                type="submit"
                                class="danger-btn"
                            >
                                Cancel Order
                            </button>

                        </form>

                    <?php endif; ?>


                    <a
                        href="../orders/orders.php"
                        class="order-secondary-btn"
                    >
                        ← My Orders
                    </a>

                </div>

            </aside>

        </div>

    </div>

</main>


<?php require_once "../includes/footer.php"; ?>