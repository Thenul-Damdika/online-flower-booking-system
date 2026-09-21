
<?php
// Start session only if it has not already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check whether customer is logged in
$isLoggedIn = isset($_SESSION["customer_id"]);

$customerName = $_SESSION["customer_name"] ?? "Account";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Bloom Heaven - Flower Booking</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Main Website CSS -->
    <link
        rel="stylesheet"
        href="/online-flower-booking-system/css/style.css"
    >
</head>

<body>

<!-- =========================
     TOP ANNOUNCEMENT BAR
========================= -->

<div class="top-bar">
    <div class="container top-bar-content">

        <span>
            🌸 Fresh flowers, beautiful moments
        </span>

        <span>
            Free delivery on selected orders
        </span>

    </div>
</div>


<!-- =========================
     MAIN HEADER
========================= -->

<header class="main-header">

    <div class="container navbar">

        <!-- LOGO -->

        <a href="/online-flower-booking-system/index.php"
           class="brand">

            <span class="brand-icon">✿</span>

            <span class="brand-text">
                <strong>Bloom</strong>
                <span>Heaven</span>
            </span>

        </a>


        <!-- DESKTOP NAVIGATION -->

        <nav class="nav-menu">

            <a
                href="/online-flower-booking-system/index.php"
                class="nav-link"
            >
                Home
            </a>

            <a
                href="/online-flower-booking-system/products.php"
                class="nav-link"
            >
                Flowers
            </a>

            <a
                href="/online-flower-booking-system/categories.php"
                class="nav-link"
            >
                Categories
            </a>

            <a
                href="/online-flower-booking-system/about.php"
                class="nav-link"
            >
                About Us
            </a>

            <a
                href="/online-flower-booking-system/contact.php"
                class="nav-link"
            >
                Contact
            </a>

        </nav>


        <!-- RIGHT SIDE ACTIONS -->

        <div class="nav-actions">

            <!-- SEARCH -->

            <button
                type="button"
                class="nav-icon-btn"
                aria-label="Search"
                onclick="toggleSearch()"
            >
                🔍
            </button>


            <!-- CART -->

            <a
                href="/online-flower-booking-system/cart/cart.php"
                class="nav-icon-btn cart-btn"
                aria-label="Shopping Cart"
            >
                🛒
                <span class="cart-count">0</span>
            </a>


            <?php if ($isLoggedIn): ?>

                <!-- CUSTOMER ACCOUNT -->

                <div class="account-dropdown">

                    <button
                        type="button"
                        class="account-btn"
                    >

                        <span class="account-icon">
                            👤
                        </span>

                        <span class="account-name">
                            <?php
                            echo htmlspecialchars($customerName);
                            ?>
                        </span>

                        <span class="dropdown-arrow">
                            ▾
                        </span>

                    </button>


                    <div class="dropdown-menu">

                        <a
                            href="/online-flower-booking-system/customer/profile.php"
                        >
                            👤 My Profile
                        </a>

                        <a
                            href="/online-flower-booking-system/cart/cart.php"
                        >
                            🛒 My Cart
                        </a>

                        <div class="dropdown-divider"></div>

                        <a
                            href="/online-flower-booking-system/customer/logout.php"
                            class="logout-link"
                        >
                            ↪ Logout
                        </a>

                    </div>

                </div>

            <?php else: ?>

                <!-- LOGIN -->

                <a
                    href="/online-flower-booking-system/customer/login.php"
                    class="login-btn"
                >
                    Login
                </a>


                <!-- REGISTER -->

                <a
                    href="/online-flower-booking-system/customer/register.php"
                    class="register-btn"
                >
                    Register
                </a>

            <?php endif; ?>


            <!-- MOBILE MENU BUTTON -->

            <button
                type="button"
                class="mobile-menu-btn"
                onclick="toggleMobileMenu()"
                aria-label="Open Menu"
            >
                ☰
            </button>

        </div>

    </div>


    <!-- =========================
         SEARCH BOX
    ========================= -->

    <div
        class="search-container"
        id="searchContainer"
    >

        <div class="container">

            <form
                action="/online-flower-booking-system/products.php"
                method="GET"
                class="header-search-form"
            >

                <input
                    type="text"
                    name="search"
                    placeholder="Search for roses, lilies, bouquets..."
                    autocomplete="off"
                >

                <button type="submit">
                    🔍 Search
                </button>

            </form>

        </div>

    </div>


    <!-- =========================
         MOBILE NAVIGATION
    ========================= -->

    <div
        class="mobile-nav"
        id="mobileNav"
    >

        <a href="/online-flower-booking-system/index.php">
            Home
        </a>

        <a href="/online-flower-booking-system/products.php">
            Flowers
        </a>

        <a href="/online-flower-booking-system/categories.php">
            Categories
        </a>

        <a href="/online-flower-booking-system/about.php">
            About Us
        </a>

        <a href="/online-flower-booking-system/contact.php">
            Contact
        </a>

        <div class="mobile-nav-divider"></div>

        <a href="/online-flower-booking-system/cart/cart.php">
            🛒 Shopping Cart
        </a>


        <?php if ($isLoggedIn): ?>

            <a
                href="/online-flower-booking-system/customer/profile.php"
            >
                👤 My Profile
            </a>

            <a
                href="/online-flower-booking-system/customer/logout.php"
                class="mobile-logout"
            >
                ↪ Logout
            </a>

        <?php else: ?>

            <a
                href="/online-flower-booking-system/customer/login.php"
            >
                Login
            </a>

            <a
                href="/online-flower-booking-system/customer/register.php"
            >
                Register
            </a>

        <?php endif; ?>

    </div>

</header>


<!-- =========================
     HEADER JAVASCRIPT
========================= -->

<script>

function toggleSearch() {

    const searchBox =
        document.getElementById("searchContainer");

    searchBox.classList.toggle("active");

    if (searchBox.classList.contains("active")) {

        const input =
            searchBox.querySelector("input");

        if (input) {
            input.focus();
        }
    }
}


function toggleMobileMenu() {

    const mobileNav =
        document.getElementById("mobileNav");

    mobileNav.classList.toggle("active");
}


document.addEventListener("click", function(event) {

    const account =
        document.querySelector(".account-dropdown");

    const button =
        document.querySelector(".account-btn");

    if (
        account &&
        button &&
        !account.contains(event.target)
    ) {

        account.classList.remove("active");

    }

});


const accountButton =
    document.querySelector(".account-btn");

if (accountButton) {

    accountButton.addEventListener("click", function(event) {

        event.stopPropagation();

        const account =
            document.querySelector(".account-dropdown");

        account.classList.toggle("active");

    });

}

</script>

