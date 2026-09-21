
<?php

session_start();

require_once "../config/database.php";


if (!isset($_SESSION["customer_id"])) {

    header("Location: login.php");
    exit();
}


$customerId = $_SESSION["customer_id"];


$stmt = $conn->prepare(
    "SELECT
        id,
        first_name,
        last_name,
        email,
        phone,
        address,
        city,
        postal_code,
        profile_image,
        created_at
     FROM customers
     WHERE id = ?"
);

$stmt->bind_param("i", $customerId);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    session_destroy();

    header("Location: login.php");
    exit();
}


$customer = $result->fetch_assoc();


require_once "../includes/header.php";

?>

<style>

/* =====================================================
   BLOOM HEAVEN - CUSTOMER PROFILE
   Internal CSS
===================================================== */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "DM Sans", Arial, sans-serif;
    background: #fffaf7;
    color: #292624;
}

/* =====================================================
   MAIN PAGE
===================================================== */

.customer-page {
    min-height: calc(100vh - 160px);
    padding: 55px 20px;
    background: #fffaf7;
}

.customer-container {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
}

/* =====================================================
   PAGE HEADING
===================================================== */

.customer-heading {
    text-align: center;
    margin-bottom: 35px;
}

.customer-heading h1 {
    margin-bottom: 8px;

    font-family:
        "Playfair Display",
        Georgia,
        serif;

    font-size: 36px;
    font-weight: 600;

    color: #294535;
}

.customer-heading p {
    margin: 0;

    font-size: 14px;

    color: #706b68;
}

/* =====================================================
   PROFILE LAYOUT
===================================================== */

.profile-layout {
    display: grid;

    grid-template-columns: 280px 1fr;

    gap: 25px;

    align-items: start;
}

/* =====================================================
   PROFILE SIDEBAR
===================================================== */

.profile-sidebar {
    background: #ffffff;

    border-radius: 18px;

    padding: 32px 22px;

    text-align: center;

    box-shadow:
        0 10px 35px rgba(70, 45, 45, 0.08);
}

/* =====================================================
   PROFILE AVATAR
===================================================== */

.profile-avatar {
    width: 95px;
    height: 95px;

    margin: 0 auto 18px;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f8e7eb;

    border: 4px solid #ffffff;

    box-shadow:
        0 5px 15px rgba(184, 92, 112, 0.12);

    font-size: 38px;
}

/* =====================================================
   SIDEBAR NAME
===================================================== */

.profile-sidebar h2 {
    margin-bottom: 6px;

    font-family:
        "Playfair Display",
        Georgia,
        serif;

    font-size: 21px;

    font-weight: 600;

    color: #294535;

    word-break: break-word;
}

/* Email */
.profile-sidebar > p {
    margin-bottom: 25px;

    font-size: 12px;

    line-height: 1.5;

    color: #706b68;

    word-break: break-word;
}

/* =====================================================
   PROFILE MENU
===================================================== */

.profile-menu {
    display: flex;

    flex-direction: column;

    gap: 6px;

    text-align: left;
}

.profile-menu a {
    display: flex;

    align-items: center;

    gap: 10px;

    width: 100%;

    padding: 13px 15px;

    border-radius: 9px;

    color: #4e4946;

    background: transparent;

    text-decoration: none;

    font-size: 13px;

    font-weight: 500;

    transition:
        background 0.3s ease,
        color 0.3s ease,
        transform 0.2s ease;
}

.profile-menu a:hover {
    background: #f8e7eb;

    color: #b85c70;

    transform: translateX(2px);
}

/* Active */
.profile-menu a.active {
    background: #f8e7eb;

    color: #b85c70;

    font-weight: 700;
}

/* Logout */
.profile-menu a.logout {
    margin-top: 8px;

    color: #963f55;
}

.profile-menu a.logout:hover {
    background: #f8e7eb;

    color: #963f55;
}

/* =====================================================
   PROFILE CONTENT
===================================================== */

.profile-content {
    min-width: 0;
}

/* =====================================================
   PROFILE CARD
===================================================== */

.profile-card {
    width: 100%;

    margin-bottom: 22px;

    padding: 30px;

    background: #ffffff;

    border-radius: 18px;

    box-shadow:
        0 10px 35px rgba(70, 45, 45, 0.07);
}

/* Last card */
.profile-card:last-child {
    margin-bottom: 0;
}

/* =====================================================
   CARD HEADER
===================================================== */

.profile-card-header {
    margin-bottom: 25px;

    padding-bottom: 17px;

    border-bottom: 1px solid #eee5e1;
}

.profile-card-header h2 {
    margin-bottom: 6px;

    font-family:
        "Playfair Display",
        Georgia,
        serif;

    font-size: 23px;

    font-weight: 600;

    color: #294535;
}

.profile-card-header p {
    margin: 0;

    font-size: 13px;

    color: #706b68;
}

/* =====================================================
   INFORMATION GRID
===================================================== */

.profile-info-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 18px;
}

/* =====================================================
   INFORMATION ITEM
===================================================== */

.profile-info-item {
    padding: 17px 18px;

    background: #fffaf7;

    border: 1px solid #eee5e1;

    border-radius: 10px;

    min-width: 0;
}

/* Label */
.profile-info-item small {
    display: block;

    margin-bottom: 7px;

    font-size: 11px;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: 0.5px;

    color: #8b817c;
}

/* Value */
.profile-info-item strong {
    display: block;

    font-size: 14px;

    font-weight: 600;

    line-height: 1.5;

    color: #292624;

    word-break: break-word;
}

/* =====================================================
   DELIVERY ADDRESS
===================================================== */

.profile-card > .profile-info-item {
    width: 100%;
}

.profile-card > .profile-info-item strong {
    line-height: 1.7;
}

/* =====================================================
   QUICK ACTION BUTTON
===================================================== */

.checkout-button {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 100%;

    min-height: 48px;

    padding: 12px 20px;

    margin-bottom: 10px;

    border-radius: 9px;

    background: #b85c70;

    color: #ffffff;

    text-decoration: none;

    font-size: 14px;

    font-weight: 600;

    transition:
        background 0.3s ease,
        transform 0.2s ease,
        box-shadow 0.3s ease;
}

.checkout-button:hover {
    background: #963f55;

    color: #ffffff;

    transform: translateY(-2px);

    box-shadow:
        0 7px 18px rgba(184, 92, 112, 0.20);
}

/* =====================================================
   CONTINUE SHOPPING
===================================================== */

.continue-shopping {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    width: 100%;

    min-height: 48px;

    padding: 12px 20px;

    border-radius: 9px;

    background: #edf4ef;

    color: #294535;

    text-decoration: none;

    font-size: 14px;

    font-weight: 600;

    transition:
        background 0.3s ease,
        color 0.3s ease,
        transform 0.2s ease;
}

.continue-shopping:hover {
    background: #dcebe1;

    color: #294535;

    transform: translateY(-1px);
}

/* =====================================================
   TABLET
===================================================== */

@media (max-width: 850px) {

    .customer-page {
        padding: 45px 20px;
    }

    .profile-layout {
        grid-template-columns: 230px 1fr;

        gap: 20px;
    }

    .profile-sidebar {
        padding: 27px 17px;
    }

    .profile-card {
        padding: 25px;
    }

    .customer-heading h1 {
        font-size: 32px;
    }
}

/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 700px) {

    .customer-page {
        min-height: auto;

        padding: 35px 15px;
    }

    .customer-heading {
        margin-bottom: 25px;
    }

    .customer-heading h1 {
        font-size: 29px;
    }

    .customer-heading p {
        font-size: 13px;
    }

    .profile-layout {
        grid-template-columns: 1fr;

        gap: 20px;
    }

    .profile-sidebar {
        width: 100%;

        padding: 25px 20px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;

        font-size: 32px;
    }

    .profile-menu {
        flex-direction: row;

        flex-wrap: wrap;

        justify-content: center;
    }

    .profile-menu a {
        width: auto;

        flex: 1;

        min-width: 130px;

        justify-content: center;

        text-align: center;
    }

    .profile-menu a.logout {
        margin-top: 0;
    }

    .profile-card {
        padding: 23px 20px;
    }

    .profile-info-grid {
        grid-template-columns: 1fr;

        gap: 12px;
    }

    .profile-card-header {
        margin-bottom: 20px;
    }

    .profile-card-header h2 {
        font-size: 21px;
    }
}

/* =====================================================
   SMALL MOBILE
===================================================== */

@media (max-width: 450px) {

    .customer-page {
        padding: 25px 10px;
    }

    .customer-heading h1 {
        font-size: 26px;
    }

    .profile-sidebar {
        padding: 22px 15px;
    }

    .profile-menu {
        display: flex;

        flex-direction: column;
    }

    .profile-menu a {
        width: 100%;

        min-width: 0;

        justify-content: flex-start;

        text-align: left;
    }

    .profile-card {
        padding: 20px 15px;

        border-radius: 14px;
    }

    .profile-info-item {
        padding: 15px;
    }

    .profile-info-item strong {
        font-size: 13px;
    }
}

</style>

<main class="customer-page">

    <div class="customer-container">

        <div class="customer-heading">

            <h1>My Profile</h1>

            <p>
                Manage your Bloom Heaven customer information
            </p>

        </div>


        <div class="profile-layout">

            <!-- Sidebar -->
            <aside class="profile-sidebar">

                <div class="profile-avatar">
                    🌸
                </div>

                <h2>
                    <?php
                    echo htmlspecialchars(
                        $customer["first_name"]
                        . " "
                        . $customer["last_name"]
                    );
                    ?>
                </h2>

                <p>
                    <?php
                    echo htmlspecialchars(
                        $customer["email"]
                    );
                    ?>
                </p>


                <nav class="profile-menu">

                    <a href="profile.php" class="active">
                        👤 My Profile
                    </a>

                    <a href="../cart/cart.php">
                        🛒 My Cart
                    </a>

                    <a href="logout.php" class="logout">
                        ↪ Logout
                    </a>

                </nav>

            </aside>


            <!-- Profile Content -->
            <section class="profile-content">

                <div class="profile-card">

                    <div class="profile-card-header">

                        <h2>
                            Personal Information
                        </h2>

                        <p>
                            Your registered customer details
                        </p>

                    </div>


                    <div class="profile-info-grid">

                        <div class="profile-info-item">

                            <small>First Name</small>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $customer["first_name"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-info-item">

                            <small>Last Name</small>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $customer["last_name"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-info-item">

                            <small>Email Address</small>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $customer["email"]
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-info-item">

                            <small>Phone Number</small>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $customer["phone"] ?? ""
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-info-item">

                            <small>City</small>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $customer["city"] ?? ""
                                );
                                ?>
                            </strong>

                        </div>


                        <div class="profile-info-item">

                            <small>Postal Code</small>

                            <strong>
                                <?php
                                echo htmlspecialchars(
                                    $customer["postal_code"] ?? ""
                                );
                                ?>
                            </strong>

                        </div>

                    </div>

                </div>


                <div class="profile-card">

                    <div class="profile-card-header">

                        <h2>
                            Delivery Address
                        </h2>

                        <p>
                            Your saved delivery information
                        </p>

                    </div>


                    <div class="profile-info-item">

                        <small>Address</small>

                        <strong>
                            <?php

                            $address =
                                $customer["address"] ?? "";

                            echo $address !== ""
                                ? nl2br(
                                    htmlspecialchars($address)
                                )
                                : "No address added yet.";

                            ?>
                        </strong>

                    </div>

                </div>


                <div class="profile-card">

                    <div class="profile-card-header">

                        <h2>
                            Quick Actions
                        </h2>

                        <p>
                            Continue shopping or manage your account
                        </p>

                    </div>


                    <a
                        href="../cart/cart.php"
                        class="checkout-button"
                    >
                        🛒 View My Cart
                    </a>


                    <a
                        href="../index.php"
                        class="continue-shopping"
                    >
                        ← Continue Shopping
                    </a>

                </div>

            </section>

        </div>

    </div>

</main>

<?php
require_once "../includes/footer.php";
?>

