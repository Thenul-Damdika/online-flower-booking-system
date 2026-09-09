
<?php

session_start();

require_once "../config/database.php";

$message = "";

if (isset($_GET["registered"])) {
    $message = "Registration successful. Please login.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT
                id,
                first_name,
                last_name,
                email,
                password
             FROM customers
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $customer = $result->fetch_assoc();

            if (password_verify($password, $customer["password"])) {

                $_SESSION["customer_id"] = $customer["id"];

                $_SESSION["customer_name"] =
                    $customer["first_name"] . " " .
                    $customer["last_name"];

                $_SESSION["customer_email"] =
                    $customer["email"];

                header("Location: profile.php");
                exit();

            } else {

                $message = "Invalid email or password.";
            }

        } else {

            $message = "Invalid email or password.";
        }
    }
}

require_once "../includes/header.php";

?>

<style>
/* =====================================================
   BLOOM HEAVEN - CUSTOMER LOGIN PAGE
   Matches the existing login.php HTML exactly
===================================================== */

/* Page */
.customer-page {
    min-height: calc(100vh - 160px);
    padding: 60px 20px;
    background: #fffaf7;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Main Container */
.customer-container {
    width: 100%;
    max-width: 1050px;
    margin: 0 auto;
}

/* =====================================================
   LOGIN CARD
===================================================== */

.auth-wrapper {
    width: 100%;
    min-height: 580px;
    display: grid;
    grid-template-columns: 45% 55%;
    background: #ffffff;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 15px 50px rgba(70, 45, 45, 0.12);
}

/* =====================================================
   LEFT IMAGE SECTION
===================================================== */

.auth-image {
    position: relative;
    min-height: 580px;

    /* Replace this URL with your actual flower image if needed */
    background:
        linear-gradient(
            rgba(60, 35, 40, 0.35),
            rgba(60, 35, 40, 0.55)
        ),
        url("../images/login-flower.jpg");

    background-size: cover;
    background-position: center;
    display: flex;
    align-items: flex-end;
}

/* If image doesn't exist, this background still looks good */
.auth-image::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(60, 30, 40, 0.75),
        rgba(60, 30, 40, 0.05)
    );
}

/* Left Text */
.auth-image-content {
    position: relative;
    z-index: 2;
    padding: 45px;
    color: #ffffff;
}

.auth-image-content h2 {
    margin: 0 0 15px;

    font-family: "Playfair Display", Georgia, serif;

    font-size: 38px;
    font-weight: 600;

    color: #ffffff;
}

.auth-image-content p {
    margin: 0;

    max-width: 390px;

    font-family: "DM Sans", Arial, sans-serif;

    font-size: 15px;
    line-height: 1.8;

    color: rgba(255, 255, 255, 0.92);
}

/* =====================================================
   RIGHT LOGIN FORM
===================================================== */

.auth-form {
    padding: 60px 65px;

    display: flex;
    flex-direction: column;
    justify-content: center;

    background: #ffffff;
}

/* =====================================================
   LOGO
===================================================== */

.auth-logo {
    text-align: center;
    margin-bottom: 35px;
}

.auth-logo .flower-icon {
    display: block;

    font-size: 42px;

    margin-bottom: 8px;
}

.auth-logo h1 {
    margin: 0 0 8px;

    font-family: "Playfair Display", Georgia, serif;

    font-size: 32px;
    font-weight: 600;

    color: #294535;
}

.auth-logo p {
    margin: 0;

    font-family: "DM Sans", Arial, sans-serif;

    font-size: 14px;

    color: #706b68;
}

/* =====================================================
   SUCCESS / ERROR MESSAGE
===================================================== */

.form-success {
    width: 100%;

    padding: 13px 16px;

    margin-bottom: 22px;

    border-radius: 9px;

    background: #edf4ef;

    border: 1px solid #d7e7dc;

    color: #294535;

    font-family: "DM Sans", Arial, sans-serif;

    font-size: 13px;

    line-height: 1.5;

    box-sizing: border-box;
}

/* =====================================================
   FORM
===================================================== */

.auth-form form {
    width: 100%;
}

/* Form Group */
.auth-form .form-group {
    width: 100%;
    margin-bottom: 22px;
}

/* Labels */
.auth-form .form-group label {
    display: block;

    margin-bottom: 8px;

    font-family: "DM Sans", Arial, sans-serif;

    font-size: 14px;
    font-weight: 600;

    color: #292624;
}

/* =====================================================
   INPUTS
===================================================== */

.auth-form .form-group input {
    display: block;

    width: 100%;
    height: 50px;

    padding: 0 15px;

    border: 1px solid #e5ddd9;

    border-radius: 10px;

    background: #ffffff;

    color: #292624;

    font-family: "DM Sans", Arial, sans-serif;

    font-size: 14px;

    outline: none;

    box-sizing: border-box;

    transition:
        border-color 0.3s ease,
        box-shadow 0.3s ease,
        background 0.3s ease;
}

/* Placeholder */
.auth-form .form-group input::placeholder {
    color: #aaa4a1;
}

/* Focus */
.auth-form .form-group input:focus {
    border-color: #b85c70;

    background: #ffffff;

    box-shadow:
        0 0 0 3px rgba(184, 92, 112, 0.12);
}

/* Autofill */
.auth-form .form-group input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0 1000px #ffffff inset;
    -webkit-text-fill-color: #292624;
}

/* =====================================================
   LOGIN BUTTON
===================================================== */

.auth-button {
    display: block;

    width: 100%;
    height: 51px;

    padding: 0 20px;

    margin-top: 5px;

    border: none;

    border-radius: 10px;

    background: #b85c70;

    color: #ffffff;

    font-family: "DM Sans", Arial, sans-serif;

    font-size: 15px;
    font-weight: 600;

    cursor: pointer;

    transition:
        background 0.3s ease,
        transform 0.2s ease,
        box-shadow 0.3s ease;
}

/* Hover */
.auth-button:hover {
    background: #963f55;

    transform: translateY(-2px);

    box-shadow: 0 8px 18px rgba(184, 92, 112, 0.22);
}

/* Click */
.auth-button:active {
    transform: translateY(0);
}

/* =====================================================
   BOTTOM REGISTER LINK
===================================================== */

.auth-bottom {
    margin-top: 25px;

    text-align: center;

    font-family: "DM Sans", Arial, sans-serif;

    font-size: 14px;

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

@media (max-width: 850px) {

    .customer-page {
        padding: 40px 20px;
    }

    .auth-wrapper {
        grid-template-columns: 1fr;

        max-width: 550px;

        min-height: auto;
    }

    .auth-image {
        min-height: 280px;

        background-position: center;
    }

    .auth-image-content {
        padding: 35px;
    }

    .auth-image-content h2 {
        font-size: 32px;
    }

    .auth-image-content p {
        font-size: 14px;
    }

    .auth-form {
        padding: 45px 40px;
    }
}

/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 550px) {

    .customer-page {
        min-height: calc(100vh - 100px);

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
        font-size: 28px;
        margin-bottom: 10px;
    }

    .auth-image-content p {
        font-size: 13px;
        line-height: 1.6;
    }

    .auth-form {
        padding: 35px 25px;
    }

    .auth-logo {
        margin-bottom: 28px;
    }

    .auth-logo .flower-icon {
        font-size: 36px;
    }

    .auth-logo h1 {
        font-size: 28px;
    }

    .auth-logo p {
        font-size: 13px;
    }

    .auth-form .form-group {
        margin-bottom: 18px;
    }

    .auth-form .form-group input {
        height: 48px;
    }

    .auth-button {
        height: 49px;
    }
}

/* =====================================================
   VERY SMALL MOBILE
===================================================== */

@media (max-width: 380px) {

    .customer-page {
        padding: 15px 8px;
    }

    .auth-form {
        padding: 30px 20px;
    }

    .auth-image-content {
        padding: 22px;
    }

    .auth-image-content h2 {
        font-size: 25px;
    }

    .auth-logo h1 {
        font-size: 25px;
    }
}
</style>

<main class="customer-page">

    <div class="customer-container">

        <div class="auth-wrapper">

            <!-- Left Image Section -->
            <div class="auth-image">

                <div class="auth-image-content">

                    <h2>Welcome Back</h2>

                    <p>
                        Your favourite blooms are waiting for you.
                        Sign in and continue creating beautiful
                        moments with Bloom Heaven.
                    </p>

                </div>

            </div>


            <!-- Login Form -->
            <div class="auth-form">

                <div class="auth-logo">

                    <span class="flower-icon">🌸</span>

                    <h1>Bloom Heaven</h1>

                    <p>Welcome back to your account</p>

                </div>


                <?php if ($message !== ""): ?>

                    <div class="form-success">

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </div>

                <?php endif; ?>


                <form method="POST">

                    <div class="form-group">

                        <label for="email">
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                    </div>


                    <button
                        type="submit"
                        class="auth-button"
                    >
                        Login to Account
                    </button>

                </form>


                <div class="auth-bottom">

                    Don't have an account?

                    <a href="register.php">
                        Create Account
                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

<?php
require_once "../includes/footer.php";
?>

