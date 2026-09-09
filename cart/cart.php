
<?php

session_start();

require_once "../config/database.php";


if (!isset($_SESSION["customer_id"])) {

    header("Location: ../customer/login.php");
    exit();
}


$customerId = $_SESSION["customer_id"];


$stmt = $conn->prepare(
    "SELECT
        c.id AS cart_id,
        c.flower_id,
        c.quantity,

        f.flower_name,
        f.description,
        f.price,
        f.stock_quantity,
        f.image,
        f.status

     FROM cart c

     INNER JOIN flowers f
        ON c.flower_id = f.id

     WHERE c.customer_id = ?

     ORDER BY c.created_at DESC"
);


$stmt->bind_param(
    "i",
    $customerId
);

$stmt->execute();

$result = $stmt->get_result();

$grandTotal = 0;


require_once "../includes/header.php";

?>

<style>
/* =========================================================
   BLOOM HEAVEN - CUSTOMER CART PAGE
   ========================================================= */

/* Main Page */
.customer-page {
    background: #fffaf7;
    min-height: calc(100vh - 160px);
    padding: 55px 20px 70px;
}

.customer-container {
    width: 100%;
    max-width: 1150px;
    margin: 0 auto;
}

/* =========================================================
   PAGE HEADING
   ========================================================= */

.customer-heading {
    text-align: center;
    margin-bottom: 38px;
}

.customer-heading h1 {
    margin: 0 0 10px;
    color: #294535;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 42px;
    font-weight: 700;
    line-height: 1.2;
}

.customer-heading p {
    margin: 0;
    color: #706b68;
    font-family: "DM Sans", Arial, sans-serif;
    font-size: 16px;
}


/* =========================================================
   CART LAYOUT
   ========================================================= */

.cart-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 350px;
    gap: 28px;
    align-items: start;
}


/* =========================================================
   CART ITEMS SECTION
   ========================================================= */

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 18px;
}


/* =========================================================
   SINGLE CART ITEM
   ========================================================= */

.cart-item {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    padding: 20px;
    display: grid;
    grid-template-columns: 115px minmax(0, 1fr) auto;
    gap: 20px;
    align-items: center;
    box-shadow: 0 6px 22px rgba(70, 45, 35, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cart-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(70, 45, 35, 0.09);
}


/* =========================================================
   FLOWER IMAGE
   ========================================================= */

.cart-item-image {
    width: 115px;
    height: 115px;
    object-fit: cover;
    border-radius: 14px;
    background: #f8e7eb;
    display: block;
}


/* =========================================================
   CART ITEM INFORMATION
   ========================================================= */

.cart-item-info {
    min-width: 0;
}

.cart-item-category {
    display: inline-block;
    margin-bottom: 7px;
    color: #b85c70;
    background: #f8e7eb;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.cart-item-info h3 {
    margin: 0 0 8px;
    color: #294535;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 22px;
    font-weight: 700;
}

.cart-item-price {
    color: #706b68;
    font-size: 14px;
    line-height: 1.5;
}


/* =========================================================
   QUANTITY FORM
   ========================================================= */

.quantity-form {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.quantity-control {
    display: flex;
    align-items: center;
}

.quantity-control input {
    width: 70px;
    height: 40px;
    padding: 0 10px;
    border: 1px solid #ddd3ce;
    border-radius: 9px;
    background: #fffaf7;
    color: #292624;
    font-family: "DM Sans", Arial, sans-serif;
    font-size: 14px;
    text-align: center;
    outline: none;
    transition: 0.2s ease;
}

.quantity-control input:focus {
    border-color: #b85c70;
    box-shadow: 0 0 0 3px rgba(184, 92, 112, 0.10);
}


/* =========================================================
   CART ITEM ACTIONS
   ========================================================= */

.cart-item-actions {
    min-width: 115px;
    text-align: right;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: space-between;
    gap: 22px;
}

.cart-item-total {
    color: #294535;
    font-size: 19px;
    font-weight: 700;
    white-space: nowrap;
}

.remove-cart {
    color: #b85c70;
    font-size: 13px;
    font-weight: 600;
    padding: 5px 0;
    transition: color 0.2s ease;
}

.remove-cart:hover {
    color: #963f55;
    text-decoration: underline;
}


/* =========================================================
   SHOP / UPDATE BUTTON
   ========================================================= */

.shop-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 9px 16px;
    border-radius: 9px;
    border: none;
    background: #b85c70;
    color: #ffffff;
    font-family: "DM Sans", Arial, sans-serif;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.shop-button:hover {
    background: #963f55;
    color: #ffffff;
    transform: translateY(-1px);
}


/* =========================================================
   CONTINUE SHOPPING
   ========================================================= */

.continue-shopping {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: 12px 18px;
    margin-top: 5px;
    border-radius: 10px;
    background: #edf4ef;
    color: #3f604d;
    font-family: "DM Sans", Arial, sans-serif;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.continue-shopping:hover {
    background: #dfece3;
    color: #294535;
    transform: translateY(-1px);
}


/* =========================================================
   ORDER SUMMARY
   ========================================================= */

.cart-summary {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    padding: 26px;
    box-shadow: 0 8px 26px rgba(70, 45, 35, 0.07);
    position: sticky;
    top: 25px;
}

.cart-summary h2 {
    margin: 0 0 22px;
    padding-bottom: 17px;
    border-bottom: 1px solid #eee5e1;
    color: #294535;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 25px;
}


/* =========================================================
   SUMMARY ROWS
   ========================================================= */

.summary-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
    padding: 13px 0;
    color: #706b68;
    font-size: 14px;
    line-height: 1.5;
}

.summary-row span:last-child {
    color: #292624;
    font-weight: 600;
    text-align: right;
}

.summary-row.total {
    margin-top: 8px;
    padding-top: 18px;
    border-top: 1px solid #eee5e1;
}

.summary-row.total span {
    color: #294535;
    font-size: 18px;
    font-weight: 700;
}


/* =========================================================
   CHECKOUT BUTTON
   ========================================================= */

.checkout-button {
    width: 100%;
    min-height: 50px;
    margin-top: 18px;
    padding: 13px 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;

    border-radius: 10px;
    background: #b85c70;
    color: #ffffff;

    font-family: "DM Sans", Arial, sans-serif;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;

    transition: all 0.2s ease;
}

.checkout-button:hover {
    background: #963f55;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 7px 18px rgba(184, 92, 112, 0.20);
}

.cart-summary .continue-shopping {
    width: 100%;
    box-sizing: border-box;
    margin-top: 10px;
}


/* =========================================================
   EMPTY CART
   ========================================================= */

.empty-cart {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 20px;
    padding: 65px 30px;
    text-align: center;
    box-shadow: 0 8px 26px rgba(70, 45, 35, 0.06);
}

.empty-cart-icon {
    width: 82px;
    height: 82px;
    margin: 0 auto 20px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;
    background: #f8e7eb;
    font-size: 36px;
}

.empty-cart h2 {
    margin: 0 0 10px;
    color: #294535;
    font-family: "Playfair Display", Georgia, serif;
    font-size: 28px;
}

.empty-cart p {
    max-width: 480px;
    margin: 0 auto 25px;
    color: #706b68;
    font-size: 15px;
    line-height: 1.7;
}

.empty-cart .shop-button {
    min-width: 170px;
}


/* =========================================================
   RESPONSIVE - TABLET
   ========================================================= */

@media (max-width: 900px) {

    .customer-page {
        padding: 45px 18px 60px;
    }

    .customer-heading h1 {
        font-size: 36px;
    }

    .cart-layout {
        grid-template-columns: 1fr;
    }

    .cart-summary {
        position: static;
    }

    .cart-summary {
        max-width: none;
    }
}


/* =========================================================
   RESPONSIVE - MOBILE
   ========================================================= */

@media (max-width: 650px) {

    .customer-page {
        padding: 35px 14px 50px;
    }

    .customer-heading {
        margin-bottom: 28px;
    }

    .customer-heading h1 {
        font-size: 31px;
    }

    .customer-heading p {
        font-size: 14px;
    }

    .cart-item {
        grid-template-columns: 85px minmax(0, 1fr);
        gap: 15px;
        padding: 15px;
    }

    .cart-item-image {
        width: 85px;
        height: 85px;
    }

    .cart-item-info h3 {
        font-size: 19px;
    }

    .cart-item-category {
        font-size: 9px;
        padding: 4px 8px;
    }

    .cart-item-price {
        font-size: 13px;
    }

    .cart-item-actions {
        grid-column: 1 / -1;
        width: 100%;
        min-width: 0;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid #eee5e1;
    }

    .cart-item-total {
        font-size: 17px;
    }

    .quantity-form {
        margin-top: 10px !important;
    }

    .quantity-control input {
        width: 60px;
    }

    .cart-summary {
        padding: 21px;
    }

    .cart-summary h2 {
        font-size: 23px;
    }

    .summary-row {
        font-size: 13px;
    }

    .summary-row.total span {
        font-size: 17px;
    }

    .empty-cart {
        padding: 50px 20px;
    }

    .empty-cart h2 {
        font-size: 25px;
    }
}


/* =========================================================
   SMALL MOBILE
   ========================================================= */

@media (max-width: 430px) {

    .cart-item {
        grid-template-columns: 75px 1fr;
    }

    .cart-item-image {
        width: 75px;
        height: 75px;
    }

    .cart-item-info h3 {
        font-size: 17px;
    }

    .quantity-form {
        align-items: stretch;
    }

    .quantity-control input {
        height: 38px;
    }

    .quantity-form .shop-button {
        padding: 8px 11px;
        font-size: 12px;
    }

    .cart-item-actions {
        flex-wrap: wrap;
        gap: 10px;
    }

    .summary-row {
        gap: 10px;
    }
}
</style>

<main class="customer-page">

    <div class="customer-container">

        <div class="customer-heading">

            <h1>My Shopping Cart</h1>

            <p>
                Review your favourite blooms before checkout
            </p>

        </div>


        <?php if ($result->num_rows === 0): ?>

            <!-- Empty Cart -->

            <div class="cart-items">

                <div class="empty-cart">

                    <div class="empty-cart-icon">
                        🛒
                    </div>

                    <h2>
                        Your cart is empty
                    </h2>

                    <p>
                        Looks like you haven't added any
                        beautiful flowers yet.
                    </p>

                    <a
                        href="../index.php"
                        class="shop-button"
                    >
                        Explore Flowers
                    </a>

                </div>

            </div>


        <?php else: ?>


            <div class="cart-layout">

                <!-- Cart Items -->

                <section class="cart-items">

                    <?php while ($item = $result->fetch_assoc()): ?>

                        <?php

                        $subtotal =
                            $item["price"] *
                            $item["quantity"];

                        $grandTotal += $subtotal;


                        $imagePath =
                            !empty($item["image"])
                            ? "../images/" . $item["image"]
                            : "../images/default-flower.jpg";

                        ?>


                        <div class="cart-item">


                            <!-- Flower Image -->

                            <img
                                src="<?php
                                    echo htmlspecialchars(
                                        $imagePath
                                    );
                                ?>"
                                alt="<?php
                                    echo htmlspecialchars(
                                        $item["flower_name"]
                                    );
                                ?>"
                                class="cart-item-image"
                                onerror="this.src='../images/default-flower.jpg';"
                            >


                            <!-- Flower Information -->

                            <div class="cart-item-info">

                                <div class="cart-item-category">
                                    Bloom Heaven
                                </div>

                                <h3>
                                    <?php
                                    echo htmlspecialchars(
                                        $item["flower_name"]
                                    );
                                    ?>
                                </h3>

                                <div class="cart-item-price">

                                    Rs.
                                    <?php
                                    echo number_format(
                                        $item["price"],
                                        2
                                    );
                                    ?>

                                    each

                                </div>


                                <form
                                    action="update-cart.php"
                                    method="POST"
                                    class="quantity-form"
                                    style="margin-top: 12px;"
                                >

                                    <input
                                        type="hidden"
                                        name="cart_id"
                                        value="<?php
                                            echo $item["cart_id"];
                                        ?>"
                                    >

                                    <div class="quantity-control">

                                        <input
                                            type="number"
                                            name="quantity"
                                            value="<?php
                                                echo $item["quantity"];
                                            ?>"
                                            min="1"
                                            max="<?php
                                                echo $item[
                                                    "stock_quantity"
                                                ];
                                            ?>"
                                            aria-label="Quantity"
                                        >

                                    </div>

                                    <button
                                        type="submit"
                                        class="shop-button"
                                        style="
                                            border: none;
                                            margin-left: 8px;
                                            padding: 9px 14px;
                                            cursor: pointer;
                                        "
                                    >
                                        Update
                                    </button>

                                </form>

                            </div>


                            <!-- Price / Remove -->

                            <div class="cart-item-actions">

                                <div class="cart-item-total">

                                    Rs.
                                    <?php
                                    echo number_format(
                                        $subtotal,
                                        2
                                    );
                                    ?>

                                </div>


                                <form
                                    action="remove-from-cart.php"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="cart_id"
                                        value="<?php
                                            echo $item["cart_id"];
                                        ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="remove-cart"
                                        style="
                                            background: none;
                                            border: none;
                                            cursor: pointer;
                                            font-family: inherit;
                                        "
                                    >
                                        Remove
                                    </button>

                                </form>

                            </div>

                        </div>

                    <?php endwhile; ?>


                    <a
                        href="../index.php"
                        class="continue-shopping"
                    >
                        ← Continue Shopping
                    </a>

                </section>


                <!-- Summary -->

                <aside class="cart-summary">

                    <h2>
                        Order Summary
                    </h2>


                    <div class="summary-row">

                        <span>
                            Items
                        </span>

                        <span>
                            <?php

                            /*
                             * The query result has already
                             * been consumed above, so this
                             * displays the total amount only.
                             */

                            echo "Cart";

                            ?>
                        </span>

                    </div>


                    <div class="summary-row">

                        <span>
                            Subtotal
                        </span>

                        <span>
                            Rs.
                            <?php
                            echo number_format(
                                $grandTotal,
                                2
                            );
                            ?>
                        </span>

                    </div>


                    <div class="summary-row">

                        <span>
                            Delivery
                        </span>

                        <span>
                            Calculated at checkout
                        </span>

                    </div>


                    <div class="summary-row total">

                        <span>
                            Total
                        </span>

                        <span>
                            Rs.
                            <?php
                            echo number_format(
                                $grandTotal,
                                2
                            );
                            ?>
                        </span>

                    </div>


                    <a
                        href="#"
                        class="checkout-button"
                    >
                        Proceed to Checkout
                    </a>


                    <a
                        href="../customer/profile.php"
                        class="continue-shopping"
                    >
                        👤 My Profile
                    </a>

                </aside>

            </div>


        <?php endif; ?>

    </div>

</main>

<?php
require_once "../includes/footer.php";
?>

