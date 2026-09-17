<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../customer/login.php");
    exit();
}

$customerId = (int) $_SESSION["customer_id"];

$stmt = $conn->prepare(
    "SELECT
        c.id AS cart_id,
        c.flower_id,
        c.quantity,
        f.flower_name,
        f.price,
        f.stock_quantity,
        f.image,
        f.status
     FROM cart c
     INNER JOIN flowers f ON c.flower_id = f.id
     WHERE c.customer_id = ?
     ORDER BY c.created_at DESC"
);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$grandTotal = 0;

while ($item = $result->fetch_assoc()) {
    if (
        $item["status"] !== "Available" ||
        (int)$item["stock_quantity"] < (int)$item["quantity"]
    ) {
        continue;
    }

    $item["subtotal"] =
        (float)$item["price"] * (int)$item["quantity"];

    $grandTotal += $item["subtotal"];
    $items[] = $item;
}

$customerStmt = $conn->prepare(
    "SELECT first_name, last_name, email, phone, address, city, postal_code
     FROM customers
     WHERE id = ?"
);
$customerStmt->bind_param("i", $customerId);
$customerStmt->execute();
$customer = $customerStmt->get_result()->fetch_assoc();

require_once "../includes/header.php";
?>

<style>

/* =========================================================
   BLOOM HEAVEN - CHECKOUT PAGE
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
   CHECKOUT HEADING
   ========================================================= */

.order-heading {
    text-align: center;
    max-width: 700px;
    margin: 0 auto 45px;
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
    font-family: "Playfair Display", serif;
    font-size: clamp(38px, 5vw, 54px);
    line-height: 1.15;
    color: #292624;
    margin-bottom: 12px;
}

.order-heading p {
    color: #706b68;
    font-size: 14px;
}


/* =========================================================
   CHECKOUT LAYOUT
   ========================================================= */

.checkout-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.55fr) minmax(330px, 0.9fr);
    gap: 28px;
    align-items: start;
}


/* =========================================================
   CARD
   ========================================================= */

.order-card {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(45, 35, 30, 0.07);
}

.order-summary-card {
    position: sticky;
    top: 105px;
}


/* =========================================================
   CARD TITLE
   ========================================================= */

.card-title {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding-bottom: 22px;
    margin-bottom: 25px;
    border-bottom: 1px solid #eee5e1;
}

.card-title > span {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #f8e7eb;
    color: #963f55;
    font-size: 11px;
    font-weight: 800;
}

.card-title h2 {
    font-family: "Playfair Display", serif;
    font-size: 24px;
    line-height: 1.2;
    color: #292624;
    margin-bottom: 4px;
}

.card-title p {
    color: #706b68;
    font-size: 12px;
}


/* =========================================================
   FORM
   ========================================================= */

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.form-group.full {
    grid-column: 1 / -1;
}

.form-group label {
    color: #292624;
    font-size: 12px;
    font-weight: 700;
}

.form-group label span {
    color: #b85c70;
}

.form-group input,
.form-group textarea {
    width: 100%;
    border: 1px solid #eee5e1;
    border-radius: 10px;
    background: #fffdfc;
    color: #292624;
    padding: 13px 14px;
    font-family: inherit;
    font-size: 13px;
    outline: none;
    transition: 0.25s ease;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #b85c70;
    box-shadow: 0 0 0 3px #f8e7eb;
}

.form-group input[readonly] {
    background: #f8f5f3;
    color: #706b68;
}


/* =========================================================
   CHECKOUT ITEMS
   ========================================================= */

.checkout-items {
    display: grid;
    gap: 5px;
    margin-bottom: 20px;
}

.checkout-item {
    display: grid;
    grid-template-columns: 62px minmax(0, 1fr) auto;
    align-items: center;
    gap: 13px;
    padding: 13px 0;
    border-bottom: 1px solid #eee5e1;
}

.checkout-item:first-child {
    padding-top: 0;
}

.checkout-item:last-child {
    border-bottom: none;
}

.checkout-item-image {
    width: 62px;
    height: 62px;
    overflow: hidden;
    border-radius: 12px;
    background: #f8e7eb;
}

.checkout-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.checkout-item-info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.checkout-item-info strong {
    overflow: hidden;
    color: #292624;
    font-size: 13px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.checkout-item-info span {
    color: #706b68;
    font-size: 11px;
}

.checkout-item > strong {
    color: #294535;
    font-size: 12px;
    white-space: nowrap;
}


/* =========================================================
   SUMMARY
   ========================================================= */

.summary-line {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 0;
    color: #706b68;
    font-size: 13px;
}

.summary-line strong {
    color: #292624;
}

.summary-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 8px;
    padding: 18px 0;
    border-top: 1px solid #eee5e1;
    color: #292624;
    font-size: 17px;
    font-weight: 700;
}

.summary-total strong {
    color: #963f55;
    font-size: 22px;
}


/* =========================================================
   BUTTONS
   ========================================================= */

.order-primary-btn {
    width: 100%;
    min-height: 52px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 30px;
    background: #b85c70;
    color: #ffffff;
    padding: 0 25px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(184, 92, 112, 0.25);
    transition: 0.3s ease;
}

.order-primary-btn:hover {
    background: #963f55;
    color: #ffffff;
    transform: translateY(-3px);
}

.order-secondary-btn {
    width: 100%;
    min-height: 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-top: 12px;
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
   EMPTY CART
   ========================================================= */

.empty-order {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
    padding: 55px 30px;
}

.order-icon {
    width: 70px;
    height: 70px;
    display: grid;
    place-items: center;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: #f8e7eb;
    font-size: 30px;
}

.empty-order h2 {
    font-family: "Playfair Display", serif;
    font-size: 30px;
    color: #292624;
    margin-bottom: 8px;
}

.empty-order p {
    color: #706b68;
    font-size: 13px;
    margin-bottom: 25px;
}

.empty-order .order-primary-btn {
    width: auto;
    display: inline-flex;
    padding: 0 28px;
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 900px) {

    .checkout-layout {
        grid-template-columns: 1fr;
    }

    .order-summary-card {
        position: static;
    }

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 768px) {

    .order-page {
        padding: 50px 15px 70px;
    }

    .order-heading {
        margin-bottom: 30px;
    }

    .order-heading h1 {
        font-size: 40px;
    }

    .order-card {
        padding: 22px;
    }

    .form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .form-group.full {
        grid-column: auto;
    }

    .card-title h2 {
        font-size: 21px;
    }

}


/* =========================================================
   SMALL MOBILE
   ========================================================= */

@media (max-width: 480px) {

    .order-page {
        padding: 40px 12px 60px;
    }

    .order-card {
        padding: 18px;
    }

    .order-heading h1 {
        font-size: 35px;
    }

    .checkout-item {
        grid-template-columns: 52px minmax(0, 1fr);
        gap: 10px;
    }

    .checkout-item-image {
        width: 52px;
        height: 52px;
    }

    .checkout-item > strong {
        grid-column: 2;
    }

    .summary-total strong {
        font-size: 19px;
    }

}

</style>

<main class="order-page">

    <div class="order-container">

        <div class="order-heading">

            <span class="order-eyebrow">
                BLOOM HEAVEN
            </span>

            <h1>Checkout</h1>

            <p>
                Confirm your delivery details and place your flower order.
            </p>

        </div>


        <?php if (empty($items)): ?>

            <section class="order-card empty-order">

                <div class="order-icon">🛒</div>

                <h2>Your cart is empty</h2>

                <p>
                    Please add flowers to your cart before checkout.
                </p>

                <a href="../index.php"
                   class="order-primary-btn">
                    Explore Flowers
                </a>

            </section>

        <?php else: ?>


            <form action="create-order.php"
                  method="POST"
                  class="checkout-layout">


                <!-- DELIVERY INFORMATION -->

                <section class="order-card">

                    <div class="card-title">

                        <span>01</span>

                        <div>

                            <h2>Delivery Information</h2>

                            <p>
                                Where should we deliver your flowers?
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">


                        <div class="form-group full">

                            <label>
                                Full Name
                            </label>

                            <input
                                type="text"
                                value="<?= htmlspecialchars(
                                    ($customer["first_name"] ?? "") .
                                    " " .
                                    ($customer["last_name"] ?? "")
                                ) ?>"
                                readonly>

                        </div>


                        <div class="form-group">

                            <label>
                                Email
                            </label>

                            <input
                                type="email"
                                value="<?= htmlspecialchars(
                                    $customer["email"] ?? ""
                                ) ?>"
                                readonly>

                        </div>


                        <div class="form-group">

                            <label>
                                Phone
                            </label>

                            <input
                                type="text"
                                value="<?= htmlspecialchars(
                                    $customer["phone"] ?? ""
                                ) ?>"
                                readonly>

                        </div>


                        <div class="form-group full">

                            <label for="shipping_address">
                                Delivery Address
                                <span>*</span>
                            </label>

                            <textarea
                                id="shipping_address"
                                name="shipping_address"
                                rows="3"
                                required><?= htmlspecialchars(
                                    $customer["address"] ?? ""
                                ) ?></textarea>

                        </div>


                        <div class="form-group">

                            <label for="shipping_city">
                                City
                            </label>

                            <input
                                id="shipping_city"
                                type="text"
                                name="shipping_city"
                                value="<?= htmlspecialchars(
                                    $customer["city"] ?? ""
                                ) ?>">

                        </div>


                        <div class="form-group">

                            <label for="shipping_postal_code">
                                Postal Code
                            </label>

                            <input
                                id="shipping_postal_code"
                                type="text"
                                name="shipping_postal_code"
                                value="<?= htmlspecialchars(
                                    $customer["postal_code"] ?? ""
                                ) ?>">

                        </div>

                    </div>

                </section>


                <!-- ORDER SUMMARY -->

                <aside class="order-card order-summary-card">

                    <div class="card-title">

                        <span>02</span>

                        <div>

                            <h2>Order Summary</h2>

                            <p>
                                <?= count($items) ?> flower item(s)
                            </p>

                        </div>

                    </div>


                    <div class="checkout-items">

                        <?php foreach ($items as $item): ?>

                            <div class="checkout-item">

                                <div class="checkout-item-image">

                                    <img
                                        src="<?= htmlspecialchars(
                                            !empty($item["image"])
                                            ? "../images/" . $item["image"]
                                            : "../images/default-flower.jpg"
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $item["flower_name"]
                                        ) ?>"
                                        onerror="this.src='../images/default-flower.jpg';">

                                </div>


                                <div class="checkout-item-info">

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

                        <?php endforeach; ?>

                    </div>


                    <div class="summary-line">

                        <span>Subtotal</span>

                        <strong>
                            Rs.
                            <?= number_format(
                                $grandTotal,
                                2
                            ) ?>
                        </strong>

                    </div>


                    <div class="summary-line">

                        <span>Delivery</span>

                        <strong>Free</strong>

                    </div>


                    <div class="summary-total">

                        <span>Total</span>

                        <strong>
                            Rs.
                            <?= number_format(
                                $grandTotal,
                                2
                            ) ?>
                        </strong>

                    </div>


                    <button
                        type="submit"
                        class="order-primary-btn">

                        Place Order

                    </button>


                    <a
                        href="../cart/cart.php"
                        class="order-secondary-btn">

                        ← Back to Cart

                    </a>

                </aside>

            </form>

        <?php endif; ?>

    </div>

</main>


<?php require_once "../includes/footer.php"; ?>