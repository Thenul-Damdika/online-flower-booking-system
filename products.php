<?php
session_start();
require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Get Selected Category
|--------------------------------------------------------------------------
*/

$categoryId = (int)($_GET["category"] ?? 0);

/*
|--------------------------------------------------------------------------
| Get Selected Flower
|--------------------------------------------------------------------------
*/

$flowerId = (int)($_GET["flower"] ?? 0);

/*
|--------------------------------------------------------------------------
| Fetch Flower Categories
|--------------------------------------------------------------------------
*/

$categoryStmt = $conn->prepare(
    "SELECT id, category_name
     FROM flower_categories
     ORDER BY id ASC"
);

$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();


/*
|--------------------------------------------------------------------------
| Add To Cart
|--------------------------------------------------------------------------
*/

$cartMessage = "";
$cartError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_to_cart"])) {

    if (!isset($_SESSION["customer_id"])) {

        header("Location: customer/login.php");
        exit();
    }

    $customerId = (int)$_SESSION["customer_id"];
    $flowerIdPost = (int)($_POST["flower_id"] ?? 0);
    $quantity = (int)($_POST["quantity"] ?? 1);

    if ($flowerIdPost <= 0) {

        $cartError = "Invalid flower selected.";

    } elseif ($quantity <= 0) {

        $cartError = "Invalid quantity.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Check Flower
        |--------------------------------------------------------------------------
        */

        $flowerStmt = $conn->prepare(
            "SELECT
                id,
                flower_name,
                stock_quantity,
                status
             FROM flowers
             WHERE id = ?"
        );

        $flowerStmt->bind_param("i", $flowerIdPost);
        $flowerStmt->execute();

        $flowerData =
            $flowerStmt->get_result()->fetch_assoc();


        if (!$flowerData) {

            $cartError = "Flower not found.";

        } elseif ($flowerData["status"] !== "Available") {

            $cartError =
                "This flower is currently unavailable.";

        } elseif ((int)$flowerData["stock_quantity"] <= 0) {

            $cartError =
                "This flower is out of stock.";

        } elseif (
            $quantity > (int)$flowerData["stock_quantity"]
        ) {

            $cartError =
                "Only " .
                (int)$flowerData["stock_quantity"] .
                " item(s) available.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Check Existing Cart Item
            |--------------------------------------------------------------------------
            */

            $cartCheck = $conn->prepare(
                "SELECT quantity
                 FROM cart
                 WHERE customer_id = ?
                 AND flower_id = ?"
            );

            $cartCheck->bind_param(
                "ii",
                $customerId,
                $flowerIdPost
            );

            $cartCheck->execute();

            $existingCart =
                $cartCheck->get_result()->fetch_assoc();


            if ($existingCart) {

                $newQuantity =
                    (int)$existingCart["quantity"] + $quantity;


                if (
                    $newQuantity >
                    (int)$flowerData["stock_quantity"]
                ) {

                    $cartError =
                        "You cannot add more than the available stock.";

                } else {

                    $updateCart = $conn->prepare(
                        "UPDATE cart
                         SET quantity = ?
                         WHERE customer_id = ?
                         AND flower_id = ?"
                    );

                    $updateCart->bind_param(
                        "iii",
                        $newQuantity,
                        $customerId,
                        $flowerIdPost
                    );

                    $updateCart->execute();

                    $cartMessage =
                        "Flower added to your cart.";
                }

            } else {

                /*
                |--------------------------------------------------------------------------
                | Insert New Cart Item
                |--------------------------------------------------------------------------
                */

                $insertCart = $conn->prepare(
                    "INSERT INTO cart
                     (customer_id, flower_id, quantity)
                     VALUES (?, ?, ?)"
                );

                $insertCart->bind_param(
                    "iii",
                    $customerId,
                    $flowerIdPost,
                    $quantity
                );

                $insertCart->execute();

                $cartMessage =
                    "Flower added to your cart.";
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Fetch Selected Flower Details
|--------------------------------------------------------------------------
*/

$selectedFlower = null;

if ($flowerId > 0) {

    $detailsStmt = $conn->prepare(
        "SELECT
            f.id,
            f.flower_name,
            f.description,
            f.price,
            f.stock_quantity,
            f.image,
            f.status,
            c.category_name,
            c.id AS category_id
         FROM flowers f
         INNER JOIN flower_categories c
             ON f.category_id = c.id
         WHERE f.id = ?"
    );

    $detailsStmt->bind_param(
        "i",
        $flowerId
    );

    $detailsStmt->execute();

    $selectedFlower =
        $detailsStmt->get_result()->fetch_assoc();
}


/*
|--------------------------------------------------------------------------
| Fetch Flowers
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");

if ($categoryId > 0 && $search !== "") {

    $searchTerm = "%" . $search . "%";

    $productStmt = $conn->prepare(
        "SELECT
            f.id,
            f.flower_name,
            f.description,
            f.price,
            f.stock_quantity,
            f.image,
            f.status,
            c.category_name
         FROM flowers f
         INNER JOIN flower_categories c
             ON f.category_id = c.id
         WHERE f.category_id = ?
         AND (
             f.flower_name LIKE ?
             OR f.description LIKE ?
             OR c.category_name LIKE ?
         )
         ORDER BY f.id DESC"
    );

    $productStmt->bind_param(
        "isss",
        $categoryId,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

} elseif ($categoryId > 0) {

    $productStmt = $conn->prepare(
        "SELECT
            f.id,
            f.flower_name,
            f.description,
            f.price,
            f.stock_quantity,
            f.image,
            f.status,
            c.category_name
         FROM flowers f
         INNER JOIN flower_categories c
             ON f.category_id = c.id
         WHERE f.category_id = ?
         ORDER BY f.id DESC"
    );

    $productStmt->bind_param(
        "i",
        $categoryId
    );

} elseif ($search !== "") {

    $searchTerm = "%" . $search . "%";

    $productStmt = $conn->prepare(
        "SELECT
            f.id,
            f.flower_name,
            f.description,
            f.price,
            f.stock_quantity,
            f.image,
            f.status,
            c.category_name
         FROM flowers f
         INNER JOIN flower_categories c
             ON f.category_id = c.id
         WHERE
             f.flower_name LIKE ?
             OR f.description LIKE ?
             OR c.category_name LIKE ?
         ORDER BY f.id DESC"
    );

    $productStmt->bind_param(
        "sss",
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

} else {

    $productStmt = $conn->prepare(
        "SELECT
            f.id,
            f.flower_name,
            f.description,
            f.price,
            f.stock_quantity,
            f.image,
            f.status,
            c.category_name
         FROM flowers f
         INNER JOIN flower_categories c
             ON f.category_id = c.id
         ORDER BY f.id DESC"
    );
}

$productStmt->execute();

$productResult = $productStmt->get_result();


/*
|--------------------------------------------------------------------------
| Selected Category Name
|--------------------------------------------------------------------------
*/

$selectedCategoryName = "All Flowers";

if ($search !== "") {
    $selectedCategoryName = "Search Results";
}

if ($categoryId > 0) {

    $selectedCategoryStmt = $conn->prepare(
        "SELECT category_name
         FROM flower_categories
         WHERE id = ?"
    );

    $selectedCategoryStmt->bind_param(
        "i",
        $categoryId
    );

    $selectedCategoryStmt->execute();

    $selectedCategory =
        $selectedCategoryStmt
            ->get_result()
            ->fetch_assoc();

    if ($selectedCategory) {

        $selectedCategoryName =
            $selectedCategory["category_name"];
    }
}


/*
|--------------------------------------------------------------------------
| Header
|--------------------------------------------------------------------------
*/

require_once "includes/header.php";
?>


<style>

/* =====================================================
   PRODUCTS PAGE
===================================================== */

.products-page {
    min-height: 70vh;

    padding: 70px 20px 80px;

    background: var(--cream, #fffaf7);
}

.products-container {
    width: min(1200px, 100%);

    margin: 0 auto;
}


/* =====================================================
   PAGE HEADING
===================================================== */

.products-heading {
    text-align: center;

    margin-bottom: 35px;
}

.products-heading .section-label {
    display: inline-block;

    margin-bottom: 10px;

    color: var(--primary, #b85c70);

    font-size: 12px;

    font-weight: 800;

    letter-spacing: 2px;
}

.products-heading h1 {
    margin: 0;

    color: var(--text, #292624);

    font-size: 42px;

    line-height: 1.2;
}

.products-heading p {
    max-width: 650px;

    margin: 14px auto 0;

    color: var(--text-light, #706b68);

    font-size: 16px;

    line-height: 1.7;
}


/* =====================================================
   CATEGORY FILTER
===================================================== */

.category-filter {
    display: flex;

    justify-content: center;

    align-items: center;

    flex-wrap: wrap;

    gap: 10px;

    margin-bottom: 40px;
}

.category-filter a {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 10px 18px;

    border: 1px solid var(--border, #eee5e1);

    border-radius: 30px;

    background: var(--white, #ffffff);

    color: var(--text-light, #706b68);

    font-size: 14px;

    font-weight: 600;

    text-decoration: none;

    transition:
        background 0.2s ease,
        color 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease;
}

.category-filter a:hover {
    color: var(--primary, #b85c70);

    border-color: #e5b8c3;

    transform: translateY(-2px);
}

.category-filter a.active {
    background: var(--primary, #b85c70);

    border-color: var(--primary, #b85c70);

    color: var(--white, #ffffff);
}


/* =====================================================
   MESSAGES
===================================================== */

.cart-message,
.cart-error {
    max-width: 800px;

    margin: 0 auto 25px;

    padding: 14px 18px;

    border-radius: 10px;

    text-align: center;

    font-size: 14px;

    font-weight: 600;
}

.cart-message {
    background: var(--green-light, #edf4ef);

    border: 1px solid #cfe1d4;

    color: var(--green-dark, #294535);
}

.cart-error {
    background: #fff0f0;

    border: 1px solid #f0cccc;

    color: #a33a3a;
}


/* =====================================================
   SELECTED PRODUCT DETAILS
===================================================== */

.selected-product-details {
    display: grid;

    grid-template-columns: 1fr 1fr;

    overflow: hidden;

    margin-bottom: 45px;

    background: var(--white, #ffffff);

    border: 1px solid var(--border, #eee5e1);

    border-radius: 20px;

    box-shadow:
        0 10px 35px rgba(70, 45, 50, 0.08);
}

.selected-product-image {
    min-height: 450px;

    background: var(--primary-light, #f8e7eb);
}

.selected-product-image img {
    width: 100%;

    height: 100%;

    display: block;

    object-fit: cover;
}

.selected-product-content {
    display: flex;

    flex-direction: column;

    justify-content: center;

    padding: 45px;
}

.selected-product-category {
    margin-bottom: 10px;

    color: var(--primary, #b85c70);

    font-size: 12px;

    font-weight: 800;

    letter-spacing: 2px;

    text-transform: uppercase;
}

.selected-product-content h2 {
    margin: 0 0 15px;

    color: var(--text, #292624);

    font-size: 36px;

    line-height: 1.2;
}

.selected-product-content p {
    margin: 0 0 22px;

    color: var(--text-light, #706b68);

    font-size: 15px;

    line-height: 1.8;
}

.selected-product-price {
    margin-bottom: 10px;

    color: var(--primary, #b85c70);

    font-size: 28px;

    font-weight: 800;
}

.selected-product-stock {
    margin-bottom: 25px;

    font-size: 14px;

    font-weight: 700;
}

.selected-product-stock.available {
    color: var(--green-dark, #294535);
}

.selected-product-stock.unavailable {
    color: #a33a3a;
}


/* =====================================================
   DETAILS CART FORM
===================================================== */

.details-cart-form {
    display: flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 15px;
}

.details-quantity {
    width: 75px;

    padding: 12px 8px;

    border: 1px solid var(--border, #eee5e1);

    border-radius: 9px;

    background: var(--white, #ffffff);

    color: var(--text, #292624);

    font-size: 14px;

    text-align: center;

    outline: none;
}

.details-cart-btn {
    flex: 1;

    min-height: 46px;

    border: 1px solid var(--primary, #b85c70);

    border-radius: 9px;

    background: var(--primary, #b85c70);

    color: var(--white, #ffffff);

    font-size: 14px;

    font-weight: 700;

    cursor: pointer;

    transition:
        background 0.2s ease,
        transform 0.2s ease;
}

.details-cart-btn:hover {
    background: var(--primary-dark, #963f55);

    transform: translateY(-1px);
}

.details-back-btn {
    display: inline-block;

    color: var(--primary, #b85c70);

    font-size: 14px;

    font-weight: 700;

    text-decoration: none;
}

.details-back-btn:hover {
    color: var(--primary-dark, #963f55);
}


/* =====================================================
   PRODUCT GRID
===================================================== */

.product-grid {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 24px;
}


/* =====================================================
   PRODUCT CARD
===================================================== */

.product-card {
    overflow: hidden;

    display: flex;

    flex-direction: column;

    background: var(--white, #ffffff);

    border: 1px solid var(--border, #eee5e1);

    border-radius: 18px;

    box-shadow:
        0 8px 25px rgba(70, 45, 50, 0.06);

    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        border-color 0.25s ease;
}

.product-card:hover {
    transform: translateY(-6px);

    border-color: #e5b8c3;

    box-shadow:
        0 16px 35px rgba(70, 45, 50, 0.12);
}


/* =====================================================
   PRODUCT IMAGE
===================================================== */

.product-image {
    position: relative;

    width: 100%;

    height: 250px;

    overflow: hidden;

    background: var(--primary-light, #f8e7eb);
}

.product-image img {
    width: 100%;

    height: 100%;

    display: block;

    object-fit: cover;

    transition:
        transform 0.35s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.06);
}


/* =====================================================
   PRODUCT STATUS
===================================================== */

.product-status {
    position: absolute;

    top: 14px;

    left: 14px;

    padding: 7px 12px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: 0.5px;
}

.status-available {
    background: var(--green-light, #edf4ef);

    color: var(--green-dark, #294535);
}

.status-out {
    background: #fff0f0;

    color: #a33a3a;
}


/* =====================================================
   PRODUCT CONTENT
===================================================== */

.product-content {
    display: flex;

    flex-direction: column;

    flex: 1;

    padding: 20px;
}

.product-category {
    margin-bottom: 7px;

    color: var(--primary, #b85c70);

    font-size: 11px;

    font-weight: 800;

    letter-spacing: 1.3px;

    text-transform: uppercase;
}

.product-name {
    margin: 0 0 9px;

    color: var(--text, #292624);

    font-size: 20px;

    line-height: 1.3;
}

.product-description {
    min-height: 45px;

    margin: 0 0 15px;

    color: var(--text-light, #706b68);

    font-size: 13px;

    line-height: 1.6;
}

.product-price {
    margin-bottom: 7px;

    color: var(--primary, #b85c70);

    font-size: 21px;

    font-weight: 800;
}

.stock-text {
    margin-bottom: 18px;

    color: var(--text-light, #706b68);

    font-size: 12px;
}


/* =====================================================
   PRODUCT ACTIONS
===================================================== */

.product-actions {
    display: flex;

    gap: 10px;

    margin-top: auto;
}

.view-details-btn,
.add-cart-btn {
    min-height: 42px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border-radius: 9px;

    font-size: 13px;

    font-weight: 700;

    text-decoration: none;

    cursor: pointer;

    transition:
        background 0.2s ease,
        color 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease;
}

.view-details-btn {
    flex: 1;

    background: var(--white, #ffffff);

    border: 1px solid var(--border, #eee5e1);

    color: var(--text, #292624);
}

.view-details-btn:hover {
    border-color: var(--primary, #b85c70);

    color: var(--primary, #b85c70);
}

.add-cart-btn {
    flex: 1.3;

    border: 1px solid var(--primary, #b85c70);

    background: var(--primary, #b85c70);

    color: var(--white, #ffffff);
}

.add-cart-btn:hover {
    background: var(--primary-dark, #963f55);

    border-color: var(--primary-dark, #963f55);

    transform: translateY(-1px);
}

.add-cart-btn:disabled {
    background: #d8d3d0;

    border-color: #d8d3d0;

    color: #ffffff;

    cursor: not-allowed;

    transform: none;
}


/* =====================================================
   EMPTY PRODUCTS
===================================================== */

.empty-products {
    grid-column: 1 / -1;

    padding: 60px 25px;

    background: var(--white, #ffffff);

    border: 1px solid var(--border, #eee5e1);

    border-radius: 18px;

    text-align: center;
}

.empty-products h2 {
    margin: 0 0 10px;

    color: var(--text, #292624);

    font-size: 24px;
}

.empty-products p {
    margin: 0;

    color: var(--text-light, #706b68);

    font-size: 14px;
}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 1050px) {

    .product-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}


@media (max-width: 850px) {

    .selected-product-details {
        grid-template-columns: 1fr;
    }

    .selected-product-image {
        min-height: 350px;

        height: 350px;
    }

    .selected-product-content {
        padding: 35px;
    }
}


@media (max-width: 800px) {

    .products-page {
        padding: 50px 16px 60px;
    }

    .products-heading h1 {
        font-size: 34px;
    }

    .product-grid {
        grid-template-columns: repeat(2, 1fr);

        gap: 18px;
    }

    .product-image {
        height: 220px;
    }
}


@media (max-width: 550px) {

    .products-heading {
        margin-bottom: 28px;
    }

    .products-heading h1 {
        font-size: 30px;
    }

    .products-heading p {
        font-size: 14px;
    }

    .category-filter {
        justify-content: flex-start;

        overflow-x: auto;

        flex-wrap: nowrap;

        padding-bottom: 5px;
    }

    .category-filter a {
        flex-shrink: 0;
    }

    .selected-product-image {
        min-height: 280px;

        height: 280px;
    }

    .selected-product-content {
        padding: 25px;
    }

    .selected-product-content h2 {
        font-size: 30px;
    }

    .details-cart-form {
        flex-direction: column;

        align-items: stretch;
    }

    .details-quantity {
        width: 100%;
    }

    .product-grid {
        grid-template-columns: 1fr;

        gap: 16px;
    }

    .product-image {
        height: 250px;
    }
}

</style>


<main class="products-page">

    <div class="products-container">


        <!-- =================================================
             PAGE HEADING
        ================================================== -->

        <div class="products-heading">

            <span class="section-label">
                OUR FLOWERS
            </span>

            <h1>
                <?= htmlspecialchars($selectedCategoryName) ?>
            </h1>

            <p>
                Discover our beautiful collection of fresh flowers
                carefully selected for every special occasion.
            </p>

        </div>


        <!-- =================================================
             CATEGORY FILTER
        ================================================== -->

        <div class="category-filter">

            <a
                href="products.php"
                class="<?= $categoryId === 0 ? "active" : "" ?>"
            >
                All Flowers
            </a>


            <?php while ($category = $categoryResult->fetch_assoc()): ?>

                <a
                    href="products.php?category=<?= (int)$category["id"] ?>"
                    class="<?= $categoryId === (int)$category["id"] ? "active" : "" ?>"
                >
                    <?= htmlspecialchars($category["category_name"]) ?>
                </a>

            <?php endwhile; ?>

        </div>


        <!-- =================================================
             CART MESSAGE
        ================================================== -->

        <?php if ($cartMessage !== ""): ?>

            <div class="cart-message">
                <?= htmlspecialchars($cartMessage) ?>
            </div>

        <?php endif; ?>


        <?php if ($cartError !== ""): ?>

            <div class="cart-error">
                <?= htmlspecialchars($cartError) ?>
            </div>

        <?php endif; ?>


        <!-- =================================================
             SELECTED PRODUCT DETAILS
        ================================================== -->

        <?php if ($selectedFlower): ?>

    <div class="selected-product-details">

        <div class="selected-product-image">
            <img
                src="<?= htmlspecialchars(
                    !empty($selectedFlower["image"])
                        ? "images/" . $selectedFlower["image"]
                        : "images/default-flower.jpg"
                ) ?>"
                alt="<?= htmlspecialchars($selectedFlower["flower_name"]) ?>"
            >
        </div>

        <div class="selected-product-content">

            <span class="selected-product-category">
                <?= htmlspecialchars($selectedFlower["category_name"]) ?>
            </span>

            <h2>
                <?= htmlspecialchars($selectedFlower["flower_name"]) ?>
            </h2>

            <p>
                <?= htmlspecialchars(
                    $selectedFlower["description"] ?? ""
                ) ?>
            </p>

            <div class="selected-product-price">
                Rs. <?= number_format(
                    (float)$selectedFlower["price"],
                    2
                ) ?>
            </div>

            <div class="selected-product-stock">
                <?= (int)$selectedFlower["stock_quantity"] ?>
                item(s) available
            </div>

        </div>

    </div>

<?php endif; ?>


        <!-- =================================================
             PRODUCT GRID
        ================================================== -->

        <div class="product-grid">


            <?php if (
                $productResult &&
                $productResult->num_rows > 0
            ): ?>


                <?php while (
                    $product =
                    $productResult->fetch_assoc()
                ): ?>


                    <?php

                    $productImage =
                        !empty($product["image"])
                            ? "images/" . $product["image"]
                            : "images/default-flower.jpg";

                    $stockQuantity =
                        (int)$product["stock_quantity"];

                    $isAvailable =
                        $product["status"] === "Available"
                        && $stockQuantity > 0;

                    ?>


                    <article class="product-card">


                        <!-- PRODUCT IMAGE -->

                        <div class="product-image">

                            <img
                                src="<?= htmlspecialchars($productImage) ?>"
                                alt="<?= htmlspecialchars($product["flower_name"]) ?>"
                                onerror="this.src='images/default-flower.jpg';"
                            >


                            <?php if ($isAvailable): ?>

                                <span
                                    class="product-status status-available"
                                >
                                    Available
                                </span>

                            <?php else: ?>

                                <span
                                    class="product-status status-out"
                                >
                                    Out of Stock
                                </span>

                            <?php endif; ?>

                        </div>


                        <!-- PRODUCT CONTENT -->

                        <div class="product-content">


                            <div class="product-category">

                                <?= htmlspecialchars(
                                    $product["category_name"]
                                ) ?>

                            </div>


                            <h2 class="product-name">

                                <?= htmlspecialchars(
                                    $product["flower_name"]
                                ) ?>

                            </h2>


                            <p class="product-description">

                                <?= htmlspecialchars(
                                    !empty($product["description"])
                                        ? $product["description"]
                                        : "Beautiful fresh flowers for your special moments."
                                ) ?>

                            </p>


                            <div class="product-price">

                                Rs.
                                <?= number_format(
                                    (float)$product["price"],
                                    2
                                ) ?>

                            </div>


                            <div class="stock-text">

                                <?php if ($isAvailable): ?>

                                    <?= $stockQuantity ?>
                                    item(s) available

                                <?php else: ?>

                                    Currently unavailable

                                <?php endif; ?>

                            </div>


                            <!-- ACTIONS -->

                            <div class="product-actions">


                                <!-- VIEW DETAILS -->

                                <a
                                    href="products.php?category=<?= (int)$categoryId ?>&flower=<?= (int)$product["id"] ?>"
                                    class="view-details-btn"
                                >
                                    View Details
                                </a>


                                <!-- ADD TO CART -->

                                <?php if ($isAvailable): ?>

                                    <form
                                        method="POST"
                                        action=""
                                        style="flex:1.3; margin:0;"
                                    >

                                        <input
                                            type="hidden"
                                            name="flower_id"
                                            value="<?= (int)$product["id"] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="quantity"
                                            value="1"
                                        >

                                        <button
                                            type="submit"
                                            name="add_to_cart"
                                            class="add-cart-btn"
                                            style="width:100%;"
                                        >
                                            Add to Cart
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <button
                                        type="button"
                                        class="add-cart-btn"
                                        disabled
                                    >
                                        Out of Stock
                                    </button>

                                <?php endif; ?>


                            </div>

                        </div>

                    </article>


                <?php endwhile; ?>


            <?php else: ?>


                <div class="empty-products">

                    <h2>
                        No Flowers Found
                    </h2>

                    <p>
                        There are currently no flowers available
                        in this category.
                    </p>

                </div>


            <?php endif; ?>


        </div>

    </div>

</main>


<?php require_once "includes/footer.php"; ?>