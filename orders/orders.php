<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../customer/login.php");
    exit();
}

$customerId = (int) $_SESSION["customer_id"];

$stmt = $conn->prepare(
    "SELECT id, total_amount, order_status, shipping_city, order_date
     FROM orders
     WHERE customer_id = ?
     ORDER BY order_date DESC"
);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();

require_once "../includes/header.php";
?>

<style>

/* =========================================================
   MY ORDERS
   Bloom Heaven Theme
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
   PAGE HEADING
   ========================================================= */

.order-heading {
    max-width: 700px;
    margin: 0 auto 45px;
    text-align: center;
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
    margin: 0 0 12px;
    color: #292624;
    font-family: "Playfair Display", serif;
    font-size: clamp(38px, 5vw, 54px);
    line-height: 1.15;
}

.order-heading p {
    margin: 0;
    color: #706b68;
    font-size: 14px;
}


/* =========================================================
   ORDERS LIST
   ========================================================= */

.orders-list {
    display: grid;
    gap: 18px;
}


/* =========================================================
   ORDER CARD
   ========================================================= */

.order-list-card {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto 150px;
    align-items: center;
    gap: 25px;
    padding: 25px 28px;
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    box-shadow: 0 5px 20px rgba(45, 35, 30, 0.06);
    transition: 0.25s ease;
}

.order-list-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(45, 35, 30, 0.10);
}


/* =========================================================
   ORDER INFORMATION
   ========================================================= */

.order-number {
    display: inline-block;
    margin-bottom: 7px;
    color: #b85c70;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.order-list-card h2 {
    margin: 0 0 5px;
    color: #292624;
    font-family: "Playfair Display", serif;
    font-size: 25px;
}

.order-list-card p {
    margin: 0;
    color: #706b68;
    font-size: 12px;
}


/* =========================================================
   ORDER META
   ========================================================= */

.order-list-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 9px;
}

.order-list-meta small {
    color: #706b68;
    font-size: 11px;
}


/* =========================================================
   STATUS BADGES
   ========================================================= */

.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 14px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
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
   VIEW DETAILS BUTTON
   ========================================================= */

.order-list-card .order-secondary-btn {
    width: 150px;
    min-height: 45px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #3f604d;
    border-radius: 30px;
    background: transparent;
    color: #294535;
    padding: 0 18px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: 0.25s ease;
}

.order-list-card .order-secondary-btn:hover {
    background: #3f604d;
    color: #ffffff;
}


/* =========================================================
   EMPTY ORDERS
   ========================================================= */

.order-card {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(45, 35, 30, 0.07);
}

.empty-order {
    max-width: 600px;
    margin: 0 auto;
    padding: 55px 30px;
    text-align: center;
}

.order-icon {
    width: 72px;
    height: 72px;
    display: grid;
    place-items: center;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: #f8e7eb;
    font-size: 31px;
}

.empty-order h2 {
    margin: 0 0 9px;
    color: #292624;
    font-family: "Playfair Display", serif;
    font-size: 30px;
}

.empty-order p {
    margin: 0 0 25px;
    color: #706b68;
    font-size: 13px;
}

.empty-order .order-primary-btn {
    width: auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: 0 28px;
    border: none;
    border-radius: 30px;
    background: #b85c70;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: 0.25s ease;
}

.empty-order .order-primary-btn:hover {
    background: #963f55;
    color: #ffffff;
    transform: translateY(-2px);
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 900px) {

    .order-list-card {
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 18px;
    }

    .order-list-card .order-secondary-btn {
        grid-column: 1 / -1;
        width: 100%;
    }

    .order-list-meta {
        align-items: flex-end;
    }

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 600px) {

    .order-page {
        padding: 50px 15px 70px;
    }

    .order-heading {
        margin-bottom: 30px;
    }

    .order-heading h1 {
        font-size: 40px;
    }

    .order-list-card {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 22px;
    }

    .order-list-meta {
        align-items: flex-start;
    }

    .order-list-card .order-secondary-btn {
        grid-column: auto;
    }

}


/* =========================================================
   SMALL MOBILE
   ========================================================= */

@media (max-width: 400px) {

    .order-page {
        padding: 40px 12px 60px;
    }

    .order-list-card {
        padding: 18px;
    }

    .order-list-card h2 {
        font-size: 22px;
    }

    .empty-order {
        padding: 40px 20px;
    }

}

</style>


<main class="order-page">

    <div class="order-container">

        <!-- PAGE HEADING -->

        <div class="order-heading">

            <span class="order-eyebrow">
                BLOOM HEAVEN
            </span>

            <h1>
                My Orders
            </h1>

            <p>
                Track your flower bookings and order status.
            </p>

        </div>


        <?php if ($result->num_rows === 0): ?>


            <!-- NO ORDERS -->

            <section class="order-card empty-order">

                <div class="order-icon">
                    🌸
                </div>

                <h2>
                    No orders yet
                </h2>

                <p>
                    Your completed bookings will appear here.
                </p>

                <a
                    href="../index.php"
                    class="order-primary-btn"
                >
                    Start Shopping
                </a>

            </section>


        <?php else: ?>


            <!-- ORDERS -->

            <section class="orders-list">

                <?php while ($order = $result->fetch_assoc()): ?>

                    <?php

                    $statusClass = strtolower(
                        str_replace(
                            " ",
                            "-",
                            $order["order_status"]
                        )
                    );

                    ?>


                    <article class="order-list-card">


                        <!-- ORDER INFO -->

                        <div>

                            <span class="order-number">
                                Order #<?= (int)$order["id"] ?>
                            </span>

                            <h2>
                                Rs.
                                <?= number_format(
                                    (float)$order["total_amount"],
                                    2
                                ) ?>
                            </h2>

                            <p>
                                <?= htmlspecialchars(
                                    $order["shipping_city"]
                                    ?: "Delivery address saved"
                                ) ?>
                            </p>

                        </div>


                        <!-- STATUS -->

                        <div class="order-list-meta">

                            <span
                                class="status-badge status-<?= htmlspecialchars(
                                    $statusClass
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $order["order_status"]
                                ) ?>

                            </span>

                            <small>
                                <?= date(
                                    "d M Y, h:i A",
                                    strtotime($order["order_date"])
                                ) ?>
                            </small>

                        </div>


                        <!-- DETAILS -->

                        <a
                            href="order-details.php?id=<?= (int)$order["id"] ?>"
                            class="order-secondary-btn"
                        >
                            View Details
                        </a>


                    </article>

                <?php endwhile; ?>

            </section>


        <?php endif; ?>

    </div>

</main>


<?php require_once "../includes/footer.php"; ?>