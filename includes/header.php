<?php
// Start session only if it has not already been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Customer login status
$isCustomerLoggedIn = isset($_SESSION["customer_id"]);

$customerName = $_SESSION["customer_name"] ?? "Account";

// Admin login status
$isAdminLoggedIn = isset($_SESSION["admin_logged_in"])
    && $_SESSION["admin_logged_in"] === true;

$adminName = $_SESSION["admin_name"] ?? "Admin";
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
    href="/online-flower-booking-system/css/styles.css"
>


</head>

<body>

<!-- TOP ANNOUNCEMENT BAR -->

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

<!-- MAIN HEADER -->

<header class="main-header">


<div class="container navbar">

    <!-- LOGO -->

    <a
        href="/online-flower-booking-system/index.php"
        class="brand"
    >

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
            href="#contact"
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


        <?php if ($isCustomerLoggedIn): ?>

            <!-- CUSTOMER ACCOUNT -->

            <div class="account-dropdown">

                <button
                    type="button"
                    class="account-btn"
                >


                    <span class="account-name">
                        <?php echo htmlspecialchars($customerName); ?>
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

                    <a
                        href="/online-flower-booking-system/orders/orders.php"
                    >
                        📦 My Orders
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


        <?php elseif ($isAdminLoggedIn): ?>

            <!-- ADMIN ACCOUNT -->

            <div class="account-dropdown">

                <button
                    type="button"
                    class="account-btn"
                >

                    

                    <span class="account-name">
                        <?php echo htmlspecialchars($adminName); ?>
                    </span>

                    <span class="dropdown-arrow">
                        ▾
                    </span>

                </button>


                <div class="dropdown-menu">

                    <a
                        href="/online-flower-booking-system/admin/dashboard.php"
                    >
                        📊 Dashboard
                    </a>

                    <a
                        href="/online-flower-booking-system/admin/flowers.php"
                    >
                        🌸 Manage Flowers
                    </a>

                    <a
                        href="/online-flower-booking-system/admin/suppliers.php"
                    >
                        🚚 Manage Suppliers
                    </a>

                    <div class="dropdown-divider"></div>

                    <a
                        href="/online-flower-booking-system/admin/logout.php"
                        class="logout-link"
                    >
                        ↪ Logout
                    </a>

                </div>

            </div>


        <?php else: ?>

            <!-- LOGIN DROPDOWN -->

            <div class="account-dropdown">

                <button
                    type="button"
                    class="account-btn"
                >

                    <span class="account-icon">
                        👤
                    </span>

                    <span class="account-name">
                        Login
                    </span>

                    <span class="dropdown-arrow">
                        ▾
                    </span>

                </button>


                <div class="dropdown-menu">

                    <a
                        href="/online-flower-booking-system/customer/login.php"
                    >
                        👤 Customer Login
                    </a>

                    <a
                        href="/online-flower-booking-system/admin/login.php"
                    >
                        🛡️ Admin Login
                    </a>

                </div>

            </div>


            <!-- REGISTER DROPDOWN -->

            <div class="account-dropdown">

                <button
                    type="button"
                    class="account-btn"
                >

                    <span class="account-icon">
                        ✨
                    </span>

                    <span class="account-name">
                        Register
                    </span>

                    <span class="dropdown-arrow">
                        ▾
                    </span>

                </button>


                <div class="dropdown-menu">

                    <a
                        href="/online-flower-booking-system/customer/register.php"
                    >
                        👤 Customer Register
                    </a>

                    <a
                        href="/online-flower-booking-system/admin/register.php"
                    >
                        🛡️ Admin Register
                    </a>

                </div>

            </div>

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


<!-- SEARCH BOX -->

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


<!-- MOBILE NAVIGATION -->

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

    <a href="#contact">
        Contact
    </a>

    <div class="mobile-nav-divider"></div>

    <a href="/online-flower-booking-system/cart/cart.php">
        🛒 Shopping Cart
    </a>


    <?php if ($isCustomerLoggedIn): ?>

        <a
            href="/online-flower-booking-system/customer/profile.php"
        >
            👤 My Profile
        </a>

        <a
            href="/online-flower-booking-system/orders/orders.php"
        >
            📦 My Orders
        </a>

        <a
            href="/online-flower-booking-system/customer/logout.php"
            class="mobile-logout"
        >
            ↪ Logout
        </a>


    <?php elseif ($isAdminLoggedIn): ?>

        <a
            href="/online-flower-booking-system/admin/dashboard.php"
        >
            📊 Admin Dashboard
        </a>

        <a
            href="/online-flower-booking-system/admin/flowers.php"
        >
            🌸 Manage Flowers
        </a>

        <a
            href="/online-flower-booking-system/admin/logout.php"
            class="mobile-logout"
        >
            ↪ Logout
        </a>


    <?php else: ?>

        <a href="/online-flower-booking-system/customer/login.php">
            👤 Customer Login
        </a>

        <a href="/online-flower-booking-system/admin/login.php">
            🛡️ Admin Login
        </a>

        <div class="mobile-nav-divider"></div>

        <a href="/online-flower-booking-system/customer/register.php">
            👤 Customer Register
        </a>

        <a href="/online-flower-booking-system/admin/register.php">
            🛡️ Admin Register
        </a>

    <?php endif; ?>

</div>


</header>

<!-- HEADER JAVASCRIPT -->

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


/*
 * Handle all account dropdown buttons.
 */

document.addEventListener("click", function(event) {

    const clickedDropdown =
        event.target.closest(".account-dropdown");


    // Close all dropdowns except the clicked one
    document
        .querySelectorAll(".account-dropdown")
        .forEach(function(dropdown) {

            if (dropdown !== clickedDropdown) {
                dropdown.classList.remove("active");
            }

        });


    // Toggle clicked dropdown
    if (clickedDropdown) {

        const button =
            clickedDropdown.querySelector(".account-btn");

        if (button && event.target.closest(".account-btn")) {

            event.preventDefault();

            clickedDropdown.classList.toggle("active");

        }
    }

});

</script>

</body>
</html>
