<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../customer/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: checkout.php");
    exit();
}

$customerId = (int) $_SESSION["customer_id"];
$shippingAddress = trim($_POST["shipping_address"] ?? "");
$shippingCity = trim($_POST["shipping_city"] ?? "");
$shippingPostalCode = trim($_POST["shipping_postal_code"] ?? "");

if ($shippingAddress === "") {
    die("Delivery address is required.");
}

try {

    $conn->begin_transaction();

    /* ---------------------------------------------
       Get customer's cart
       --------------------------------------------- */

    $cartStmt = $conn->prepare(
        "SELECT
            c.flower_id,
            c.quantity,
            f.flower_name,
            f.price,
            f.stock_quantity,
            f.status
         FROM cart c
         INNER JOIN flowers f ON c.flower_id = f.id
         WHERE c.customer_id = ?
         FOR UPDATE"
    );

    $cartStmt->bind_param("i", $customerId);
    $cartStmt->execute();

    $cartResult = $cartStmt->get_result();

    $items = [];
    $total = 0;


    /* ---------------------------------------------
       Validate cart
       --------------------------------------------- */

    while ($row = $cartResult->fetch_assoc()) {

        if ($row["status"] !== "Available") {
            throw new Exception(
                $row["flower_name"] .
                " is currently unavailable."
            );
        }

        if (
            (int)$row["quantity"] <= 0 ||
            (int)$row["quantity"] > (int)$row["stock_quantity"]
        ) {
            throw new Exception(
                "Not enough stock for " .
                $row["flower_name"] .
                "."
            );
        }

        $row["subtotal"] =
            (float)$row["price"] *
            (int)$row["quantity"];

        $total += $row["subtotal"];

        $items[] = $row;
    }


    if (empty($items)) {
        throw new Exception("Your cart is empty.");
    }


    /* ---------------------------------------------
       Create order
       --------------------------------------------- */

    $orderStmt = $conn->prepare(
        "INSERT INTO orders
        (
            customer_id,
            total_amount,
            order_status,
            shipping_address,
            shipping_city,
            shipping_postal_code
        )
        VALUES (?, ?, 'Pending', ?, ?, ?)"
    );

    $orderStmt->bind_param(
        "idsss",
        $customerId,
        $total,
        $shippingAddress,
        $shippingCity,
        $shippingPostalCode
    );

    $orderStmt->execute();

    $orderId = $conn->insert_id;


    /* ---------------------------------------------
       Prepare order items
       --------------------------------------------- */

    $itemStmt = $conn->prepare(
        "INSERT INTO order_items
        (
            order_id,
            flower_id,
            quantity,
            price,
            subtotal
        )
        VALUES (?, ?, ?, ?, ?)"
    );


    /* ---------------------------------------------
       Prepare stock update
       --------------------------------------------- */

    $stockStmt = $conn->prepare(
        "UPDATE flowers
         SET stock_quantity = stock_quantity - ?,
             status = CASE
                 WHEN stock_quantity - ? <= 0
                 THEN 'Out of Stock'
                 ELSE status
             END
         WHERE id = ?"
    );


    /* ---------------------------------------------
       Insert items + reduce stock
       --------------------------------------------- */

    foreach ($items as $item) {

        $flowerId = (int)$item["flower_id"];
        $quantity = (int)$item["quantity"];
        $price = (float)$item["price"];
        $subtotal = (float)$item["subtotal"];

        $itemStmt->bind_param(
            "iiidd",
            $orderId,
            $flowerId,
            $quantity,
            $price,
            $subtotal
        );

        $itemStmt->execute();


        $stockStmt->bind_param(
            "iii",
            $quantity,
            $quantity,
            $flowerId
        );

        $stockStmt->execute();
    }


    /* ---------------------------------------------
       Clear cart
       --------------------------------------------- */

    $clearStmt = $conn->prepare(
        "DELETE FROM cart
         WHERE customer_id = ?"
    );

    $clearStmt->bind_param("i", $customerId);
    $clearStmt->execute();


    /* ---------------------------------------------
       Create payment record
       --------------------------------------------- */

    $paymentStmt = $conn->prepare(
        "INSERT INTO payments
        (
            order_id,
            customer_id,
            amount,
            payment_method,
            payment_status
        )
        VALUES
        (?, ?, ?, 'Cash on Delivery', 'Pending')"
    );

    $paymentStmt->bind_param(
        "iid",
        $orderId,
        $customerId,
        $total
    );

    $paymentStmt->execute();


    /* ---------------------------------------------
       Finish transaction
       --------------------------------------------- */

    $conn->commit();


    /* ---------------------------------------------
       Go to payment page
       --------------------------------------------- */

    header(
        "Location: ../payment/payment.php?order_id=" .
        $orderId
    );

    exit();


} catch (Throwable $e) {

    $conn->rollback();

    /*
     * Styled error page
     */

    $errorMessage = htmlspecialchars(
        $e->getMessage(),
        ENT_QUOTES,
        "UTF-8"
    );

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>Order Error - Bloom Heaven</title>

        <style>

            :root {
                --primary: #b85c70;
                --primary-dark: #963f55;
                --primary-light: #f8e7eb;
                --green: #3f604d;
                --cream: #fffaf7;
                --white: #ffffff;
                --text: #292624;
                --text-light: #706b68;
                --border: #eee5e1;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 25px;
                background: var(--cream);
                font-family: Arial, sans-serif;
                color: var(--text);
            }

            .order-error {
                width: min(100%, 560px);
                padding: 45px 35px;
                text-align: center;
                background: var(--white);
                border: 1px solid var(--border);
                border-radius: 18px;
                box-shadow:
                    0 10px 35px rgba(45, 35, 30, 0.08);
            }

            .error-icon {
                width: 70px;
                height: 70px;
                margin: 0 auto 20px;
                display: grid;
                place-items: center;
                border-radius: 50%;
                background: var(--primary-light);
                color: var(--primary-dark);
                font-size: 30px;
                font-weight: bold;
            }

            .order-error .eyebrow {
                display: block;
                margin-bottom: 8px;
                color: var(--primary);
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 2px;
            }

            .order-error h1 {
                margin: 0 0 12px;
                font-size: 30px;
                color: var(--text);
            }

            .order-error p {
                margin: 0 auto 25px;
                color: var(--text-light);
                font-size: 14px;
                line-height: 1.7;
            }

            .error-message {
                margin-bottom: 25px;
                padding: 14px 16px;
                border-radius: 10px;
                background: var(--primary-light);
                color: var(--primary-dark);
                font-size: 13px;
                line-height: 1.5;
            }

            .error-actions {
                display: flex;
                justify-content: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .error-btn {
                min-width: 150px;
                padding: 13px 22px;
                border-radius: 30px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 700;
                transition: 0.25s ease;
            }

            .primary-btn {
                background: var(--primary);
                color: var(--white);
            }

            .primary-btn:hover {
                background: var(--primary-dark);
            }

            .secondary-btn {
                border: 1px solid var(--green);
                color: var(--green);
                background: transparent;
            }

            .secondary-btn:hover {
                background: var(--green);
                color: var(--white);
            }

            @media (max-width: 480px) {

                .order-error {
                    padding: 35px 22px;
                }

                .order-error h1 {
                    font-size: 26px;
                }

                .error-actions {
                    flex-direction: column;
                }

                .error-btn {
                    width: 100%;
                }
            }

        </style>

    </head>

    <body>

        <div class="order-error">

            <div class="error-icon">
                !
            </div>

            <span class="eyebrow">
                BLOOM HEAVEN
            </span>

            <h1>
                Unable to Create Order
            </h1>

            <p>
                We couldn't complete your order at this time.
                Please check your cart and try again.
            </p>

            <div class="error-message">
                <?= $errorMessage ?>
            </div>

            <div class="error-actions">

                <a
                    href="checkout.php"
                    class="error-btn primary-btn"
                >
                    Back to Checkout
                </a>

                <a
                    href="../index.php"
                    class="error-btn secondary-btn"
                >
                    Continue Shopping
                </a>

            </div>

        </div>

    </body>

    </html>

    <?php
}
?>