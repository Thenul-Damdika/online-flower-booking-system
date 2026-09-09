
<?php

session_start();

require_once "../config/database.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);
    $postalCode = trim($_POST["postal_code"]);


    if (
        $firstName === "" ||
        $lastName === "" ||
        $email === "" ||
        $password === ""
    ) {

        $message = "Please fill in all required fields.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters.";
        $messageType = "error";

    } elseif ($password !== $confirmPassword) {

        $message = "Passwords do not match.";
        $messageType = "error";

    } else {

        $stmt = $conn->prepare(
            "SELECT id
             FROM customers
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $message = "This email is already registered.";
            $messageType = "error";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO customers
                (
                    first_name,
                    last_name,
                    email,
                    phone,
                    password,
                    address,
                    city,
                    postal_code
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssssss",
                $firstName,
                $lastName,
                $email,
                $phone,
                $hashedPassword,
                $address,
                $city,
                $postalCode
            );

            if ($stmt->execute()) {

                header(
                    "Location: login.php?registered=1"
                );

                exit();

            } else {

                $message =
                    "Registration failed. Please try again.";

                $messageType = "error";
            }
        }
    }
}

require_once "../includes/header.php";

?>

<style>

/* =====================================================
   BLOOM HEAVEN - CUSTOMER REGISTER
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
    padding: 50px 20px;
    background: #fffaf7;
    display: flex;
    align-items: center;
    justify-content: center;
}

.customer-container {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
}

/* =====================================================
   MAIN AUTH CARD
===================================================== */

.auth-wrapper {
    width: 100%;

    display: grid;
    grid-template-columns: 43% 57%;

    background: #ffffff;

    border-radius: 22px;

    overflow: hidden;

    box-shadow:
        0 15px 50px rgba(70, 45, 45, 0.12);
}

/* =====================================================
   LEFT IMAGE SECTION
===================================================== */

.auth-image {
    position: relative;

    min-height: 700px;

    background:
        linear-gradient(
            rgba(70, 35, 45, 0.25),
            rgba(70, 35, 45, 0.65)
        ),
        url("../images/register-flower.jpg");

    background-size: cover;

    background-position: center;

    display: flex;

    align-items: flex-end;
}

/* Dark gradient */
.auth-image::after {
    content: "";

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            to top,
            rgba(55, 30, 40, 0.75),
            rgba(55, 30, 40, 0.05)
        );

    pointer-events: none;
}

/* Image Text */
.auth-image-content {
    position: relative;

    z-index: 2;

    padding: 45px;
}

.auth-image-content h2 {
    margin-bottom: 15px;

    font-family:
        "Playfair Display",
        Georgia,
        serif;

    font-size: 38px;

    font-weight: 600;

    line-height: 1.2;

    color: #ffffff;
}

.auth-image-content p {
    max-width: 390px;

    margin: 0;

    font-family:
        "DM Sans",
        Arial,
        sans-serif;

    font-size: 15px;

    line-height: 1.8;

    color: rgba(255, 255, 255, 0.93);
}

/* =====================================================
   RIGHT REGISTER FORM
===================================================== */

.auth-form {
    padding: 45px 55px;

    background: #ffffff;
}

/* =====================================================
   LOGO / HEADER
===================================================== */

.auth-logo {
    text-align: center;

    margin-bottom: 25px;
}

.auth-logo .flower-icon {
    display: block;

    font-size: 40px;

    margin-bottom: 5px;
}

.auth-logo h1 {
    margin: 0 0 7px;

    font-family:
        "Playfair Display",
        Georgia,
        serif;

    font-size: 31px;

    font-weight: 600;

    color: #294535;
}

.auth-logo p {
    margin: 0;

    font-size: 14px;

    color: #706b68;
}

/* =====================================================
   SUCCESS MESSAGE
===================================================== */

.form-success {
    width: 100%;

    padding: 12px 15px;

    margin-bottom: 18px;

    border-radius: 9px;

    background: #edf4ef;

    border: 1px solid #d5e5da;

    color: #294535;

    font-size: 13px;

    line-height: 1.5;
}

/* =====================================================
   ERROR MESSAGE
===================================================== */

.form-error {
    width: 100%;

    padding: 12px 15px;

    margin-bottom: 18px;

    border-radius: 9px;

    background: #f8e7eb;

    border: 1px solid #efd0d8;

    color: #963f55;

    font-size: 13px;

    line-height: 1.5;
}

/* =====================================================
   FORM
===================================================== */

.auth-form form {
    width: 100%;
}

/* =====================================================
   TWO COLUMN ROW
===================================================== */

.form-row {
    width: 100%;

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 18px;
}

/* =====================================================
   FORM GROUP
===================================================== */

.form-group {
    width: 100%;

    margin-bottom: 17px;
}

/* =====================================================
   LABEL
===================================================== */

.form-group label {
    display: block;

    margin-bottom: 7px;

    font-family:
        "DM Sans",
        Arial,
        sans-serif;

    font-size: 13px;

    font-weight: 600;

    color: #292624;
}

/* Required star */
.form-group label::first-letter {
    color: #292624;
}

/* =====================================================
   INPUT
===================================================== */

.form-group input {
    display: block;

    width: 100%;

    height: 46px;

    padding: 0 14px;

    border: 1px solid #e5ddd9;

    border-radius: 9px;

    background: #ffffff;

    color: #292624;

    font-family:
        "DM Sans",
        Arial,
        sans-serif;

    font-size: 13px;

    outline: none;

    box-sizing: border-box;

    transition:
        border-color 0.3s ease,
        box-shadow 0.3s ease,
        background 0.3s ease;
}

/* Placeholder */
.form-group input::placeholder {
    color: #aaa4a1;
}

/* Focus */
.form-group input:focus {
    border-color: #b85c70;

    box-shadow:
        0 0 0 3px rgba(184, 92, 112, 0.11);

    background: #ffffff;
}

/* =====================================================
   TEXTAREA
===================================================== */

.form-group textarea {
    display: block;

    width: 100%;

    min-height: 80px;

    padding: 12px 14px;

    border: 1px solid #e5ddd9;

    border-radius: 9px;

    background: #ffffff;

    color: #292624;

    font-family:
        "DM Sans",
        Arial,
        sans-serif;

    font-size: 13px;

    line-height: 1.5;

    resize: vertical;

    outline: none;

    box-sizing: border-box;

    transition:
        border-color 0.3s ease,
        box-shadow 0.3s ease;
}

.form-group textarea::placeholder {
    color: #aaa4a1;
}

.form-group textarea:focus {
    border-color: #b85c70;

    box-shadow:
        0 0 0 3px rgba(184, 92, 112, 0.11);
}

/* =====================================================
   AUTOFILL
===================================================== */

.form-group input:-webkit-autofill {
    -webkit-box-shadow:
        0 0 0 1000px #ffffff inset;

    -webkit-text-fill-color: #292624;
}

/* =====================================================
   REGISTER BUTTON
===================================================== */

.auth-button {
    display: block;

    width: 100%;

    height: 50px;

    margin-top: 4px;

    padding: 0 20px;

    border: none;

    border-radius: 10px;

    background: #b85c70;

    color: #ffffff;

    font-family:
        "DM Sans",
        Arial,
        sans-serif;

    font-size: 14px;

    font-weight: 600;

    cursor: pointer;

    transition:
        background 0.3s ease,
        transform 0.2s ease,
        box-shadow 0.3s ease;
}

.auth-button:hover {
    background: #963f55;

    transform: translateY(-2px);

    box-shadow:
        0 8px 18px rgba(184, 92, 112, 0.22);
}

.auth-button:active {
    transform: translateY(0);
}

/* =====================================================
   BOTTOM LOGIN LINK
===================================================== */

.auth-bottom {
    margin-top: 20px;

    text-align: center;

    font-family:
        "DM Sans",
        Arial,
        sans-serif;

    font-size: 13px;

    line-height: 1.6;

    color: #706b68;
}

.auth-bottom a {
    margin-left: 4px;

    color: #b85c70;

    font-weight: 700;

    text-decoration: none;

    transition: color 0.3s ease;
}

.auth-bottom a:hover {
    color: #963f55;

    text-decoration: underline;
}

/* =====================================================
   TABLET
===================================================== */

@media (max-width: 900px) {

    .customer-page {
        padding: 40px 20px;
    }

    .auth-wrapper {
        grid-template-columns: 1fr;

        max-width: 600px;
    }

    .auth-image {
        min-height: 280px;
    }

    .auth-image-content {
        padding: 35px;
    }

    .auth-image-content h2 {
        font-size: 32px;
    }

    .auth-form {
        padding: 45px 40px;
    }
}

/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 600px) {

    .customer-page {
        min-height: auto;

        padding: 25px 12px;
    }

    .auth-wrapper {
        border-radius: 16px;
    }

    .auth-image {
        min-height: 220px;
    }

    .auth-image-content {
        padding: 25px;
    }

    .auth-image-content h2 {
        font-size: 27px;

        margin-bottom: 10px;
    }

    .auth-image-content p {
        font-size: 13px;

        line-height: 1.6;
    }

    .auth-form {
        padding: 35px 22px;
    }

    .auth-logo {
        margin-bottom: 25px;
    }

    .auth-logo .flower-icon {
        font-size: 35px;
    }

    .auth-logo h1 {
        font-size: 27px;
    }

    .auth-logo p {
        font-size: 13px;
    }

    /* Stack form fields */
    .form-row {
        grid-template-columns: 1fr;

        gap: 0;
    }

    .form-group {
        margin-bottom: 17px;
    }

    .form-group input {
        height: 48px;
    }

    .form-group textarea {
        min-height: 90px;
    }

    .auth-button {
        height: 49px;
    }
}

/* =====================================================
   SMALL MOBILE
===================================================== */

@media (max-width: 380px) {

    .customer-page {
        padding: 15px 8px;
    }

    .auth-form {
        padding: 30px 18px;
    }

    .auth-image-content {
        padding: 22px;
    }

    .auth-image-content h2 {
        font-size: 24px;
    }

    .auth-logo h1 {
        font-size: 24px;
    }
}

</style>

<main class="customer-page">

    <div class="customer-container">

        <div class="auth-wrapper">

            <!-- Image Section -->
            <div class="auth-image">

                <div class="auth-image-content">

                    <h2>Let Your Feelings Bloom</h2>

                    <p>
                        Create your Bloom Heaven account and
                        discover beautiful flowers for every
                        special moment.
                    </p>

                </div>

            </div>


            <!-- Registration Form -->
            <div class="auth-form">

                <div class="auth-logo">

                    <span class="flower-icon">🌷</span>

                    <h1>Create Account</h1>

                    <p>
                        Join Bloom Heaven today
                    </p>

                </div>


                <?php if ($message !== ""): ?>

                    <div class="<?php
                        echo $messageType === "error"
                            ? "form-error"
                            : "form-success";
                    ?>">

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </div>

                <?php endif; ?>


                <form method="POST">

                    <div class="form-row">

                        <div class="form-group">

                            <label for="first_name">
                                First Name *
                            </label>

                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                placeholder="First name"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST["first_name"] ?? ""
                                    );
                                ?>"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="last_name">
                                Last Name *
                            </label>

                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                placeholder="Last name"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST["last_name"] ?? ""
                                    );
                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="email">
                            Email Address *
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            value="<?php
                                echo htmlspecialchars(
                                    $_POST["email"] ?? ""
                                );
                            ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="phone">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            placeholder="Enter your phone number"
                            value="<?php
                                echo htmlspecialchars(
                                    $_POST["phone"] ?? ""
                                );
                            ?>"
                        >

                    </div>


                    <div class="form-row">

                        <div class="form-group">

                            <label for="password">
                                Password *
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Minimum 6 characters"
                                required
                            >

                        </div>


                        <div class="form-group">

                            <label for="confirm_password">
                                Confirm Password *
                            </label>

                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Confirm password"
                                required
                            >

                        </div>

                    </div>


                    <div class="form-group">

                        <label for="address">
                            Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            placeholder="Enter your address"
                        ><?php
                            echo htmlspecialchars(
                                $_POST["address"] ?? ""
                            );
                        ?></textarea>

                    </div>


                    <div class="form-row">

                        <div class="form-group">

                            <label for="city">
                                City
                            </label>

                            <input
                                type="text"
                                id="city"
                                name="city"
                                placeholder="City"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST["city"] ?? ""
                                    );
                                ?>"
                            >

                        </div>


                        <div class="form-group">

                            <label for="postal_code">
                                Postal Code
                            </label>

                            <input
                                type="text"
                                id="postal_code"
                                name="postal_code"
                                placeholder="Postal code"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST["postal_code"] ?? ""
                                    );
                                ?>"
                            >

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="auth-button"
                    >
                        Create My Account
                    </button>

                </form>


                <div class="auth-bottom">

                    Already have an account?

                    <a href="login.php">
                        Login
                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

<?php
require_once "../includes/footer.php";
?>

