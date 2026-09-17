<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../customer/login.php");
    exit();
}

$customerId = (int) $_SESSION["customer_id"];
$orderId = (int)($_GET["order_id"] ?? 0);

if ($orderId <= 0) {
    die("Invalid order.");
}


/* =========================================================
   GET ORDER
   ========================================================= */

$orderStmt = $conn->prepare(
    "SELECT id, total_amount, order_status
     FROM orders
     WHERE id = ? AND customer_id = ?"
);

$orderStmt->bind_param("ii", $orderId, $customerId);
$orderStmt->execute();

$order = $orderStmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}


/* =========================================================
   ORDER MUST BE PENDING
   ========================================================= */

if ($order["order_status"] !== "Pending") {
    die("This order is not available for payment.");
}


require_once "../includes/header.php";
?>

<style>

/* =========================================================
   PAYMENT PAGE
   Bloom Heaven Theme
   ========================================================= */

.payment-page {
    min-height: 100vh;
    padding: 70px 20px 90px;
    background: #fffaf7;
}

.payment-container {
    width: min(100%, 900px);
    margin: 0 auto;
}


/* =========================================================
   HEADING
   ========================================================= */

.payment-heading {
    text-align: center;
    max-width: 700px;
    margin: 0 auto 40px;
}

.payment-eyebrow {
    display: inline-block;
    margin-bottom: 10px;
    color: #b85c70;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 2px;
}

.payment-heading h1 {
    margin: 0 0 10px;
    color: #292624;
    font-family: "Playfair Display", serif;
    font-size: clamp(36px, 5vw, 52px);
    line-height: 1.15;
}

.payment-heading p {
    margin: 0;
    color: #706b68;
    font-size: 13px;
}


/* =========================================================
   MAIN CARD
   ========================================================= */

.payment-card {
    background: #ffffff;
    border: 1px solid #eee5e1;
    border-radius: 18px;
    padding: 32px;
    box-shadow: 0 5px 20px rgba(45, 35, 30, 0.07);
}


/* =========================================================
   ORDER SUMMARY
   ========================================================= */

.order-summary {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 20px;
    margin-bottom: 30px;
    background: #f8e7eb;
    border-radius: 14px;
}

.order-summary-label {
    color: #706b68;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.7px;
}

.order-summary-number {
    margin-top: 5px;
    color: #292624;
    font-size: 15px;
    font-weight: 700;
}

.order-summary-total {
    text-align: right;
}

.order-summary-total span {
    display: block;
    margin-bottom: 4px;
    color: #706b68;
    font-size: 11px;
}

.order-summary-total strong {
    color: #963f55;
    font-size: 23px;
}


/* =========================================================
   SECTION TITLE
   ========================================================= */

.payment-section-title {
    margin: 0 0 18px;
    color: #292624;
    font-family: "Playfair Display", serif;
    font-size: 23px;
}


/* =========================================================
   PAYMENT OPTIONS
   ========================================================= */

.payment-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 28px;
}

.payment-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 58px;
    padding: 0 15px;
    border: 1px solid #eee5e1;
    border-radius: 12px;
    background: #ffffff;
    cursor: pointer;
    transition: 0.25s ease;
}

.payment-option:hover {
    border-color: #b85c70;
    background: #fffaf7;
}

.payment-option input {
    width: 17px;
    height: 17px;
    margin: 0;
    accent-color: #b85c70;
    cursor: pointer;
}

.payment-option span {
    color: #292624;
    font-size: 12px;
    font-weight: 700;
}

.payment-option:has(input:checked) {
    border-color: #b85c70;
    background: #f8e7eb;
}


/* =========================================================
   DETAILS BOX
   ========================================================= */

.card-details,
.bank-details {
    padding: 25px;
    margin-top: 5px;
    border: 1px solid #eee5e1;
    border-radius: 14px;
    background: #fffaf7;
}

.card-details-title {
    margin-bottom: 20px;
    color: #292624;
    font-family: "Playfair Display", serif;
    font-size: 20px;
}


/* =========================================================
   FORM
   ========================================================= */

.form-group {
    margin-bottom: 18px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    margin-bottom: 7px;
    color: #292624;
    font-size: 11px;
    font-weight: 700;
}

.form-group input,
.form-group select {
    width: 100%;
    min-height: 46px;
    box-sizing: border-box;
    padding: 0 14px;
    border: 1px solid #eee5e1;
    border-radius: 10px;
    outline: none;
    background: #ffffff;
    color: #292624;
    font-family: inherit;
    font-size: 13px;
    transition: 0.2s ease;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #b85c70;
    box-shadow: 0 0 0 3px rgba(184, 92, 112, 0.08);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}


/* =========================================================
   BUTTONS
   ========================================================= */

.payment-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}

.payment-submit {
    flex: 1;
    min-height: 50px;
    border: none;
    border-radius: 30px;
    background: #b85c70;
    color: #ffffff;
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.25s ease;
}

.payment-submit:hover {
    background: #963f55;
    transform: translateY(-1px);
}

.payment-cancel {
    flex: 1;
    min-height: 50px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
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

.payment-cancel:hover {
    background: #3f604d;
    color: #ffffff;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 768px) {

    .payment-page {
        padding: 50px 15px 70px;
    }

    .payment-card {
        padding: 22px;
    }

    .payment-options {
        grid-template-columns: 1fr;
    }

    .order-summary {
        align-items: flex-start;
        flex-direction: column;
    }

    .order-summary-total {
        text-align: left;
    }

}

@media (max-width: 480px) {

    .payment-page {
        padding: 40px 12px 60px;
    }

    .payment-card {
        padding: 18px;
    }

    .payment-heading h1 {
        font-size: 34px;
    }

    .card-details,
    .bank-details {
        padding: 18px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .payment-actions {
        flex-direction: column;
    }

}

</style>


<main class="payment-page">

    <div class="payment-container">

        <!-- =================================================
             HEADING
             ================================================= -->

        <div class="payment-heading">

            <span class="payment-eyebrow">
                SECURE CHECKOUT
            </span>

            <h1>
                Complete Payment
            </h1>

            <p>
                Choose your preferred payment method for your order.
            </p>

        </div>


        <!-- =================================================
             PAYMENT CARD
             ================================================= -->

        <div class="payment-card">

            <!-- ORDER SUMMARY -->

            <div class="order-summary">

                <div>

                    <div class="order-summary-label">
                        Order
                    </div>

                    <div class="order-summary-number">
                        #<?= (int)$order["id"] ?>
                    </div>

                </div>


                <div class="order-summary-total">

                    <span>Total Amount</span>

                    <strong>
                        Rs.
                        <?= number_format(
                            (float)$order["total_amount"],
                            2
                        ) ?>
                    </strong>

                </div>

            </div>


            <!-- =================================================
                 PAYMENT FORM
                 ================================================= -->

            <form
                action="process-payment.php"
                method="POST"
                id="paymentForm"
                autocomplete="off"
            >

                <!-- VERY IMPORTANT -->
                <input
                    type="hidden"
                    name="order_id"
                    value="<?= (int)$order["id"] ?>"
                >


                <h2 class="payment-section-title">
                    Payment Method
                </h2>


                <!-- =================================================
                     PAYMENT METHOD OPTIONS
                     ================================================= -->

                <div class="payment-options">

                    <label class="payment-option">

                        <input
                            type="radio"
                            name="payment_method"
                            value="Cash on Delivery"
                            checked
                            onchange="updatePaymentUI()"
                        >

                        <span>
                            Cash on Delivery
                        </span>

                    </label>


                    <label class="payment-option">

                        <input
                            type="radio"
                            name="payment_method"
                            value="Card"
                            onchange="updatePaymentUI()"
                        >

                        <span>
                            Card
                        </span>

                    </label>


                    <label class="payment-option">

                        <input
                            type="radio"
                            name="payment_method"
                            value="Online Banking"
                            onchange="updatePaymentUI()"
                        >

                        <span>
                            Online Banking
                        </span>

                    </label>

                </div>


                <!-- =================================================
                     CARD DETAILS
                     ================================================= -->

                <div
                    class="card-details"
                    id="cardDetails"
                    style="display: none;"
                >

                    <div class="card-details-title">
                        Card Details
                    </div>


                    <div class="form-group">

                        <label for="cardHolder">
                            Card Holder Name
                        </label>

                        <input
                            type="text"
                            id="cardHolder"
                            name="card_holder_name"
                            placeholder="Name on card"
                            maxlength="100"
                        >

                    </div>


                    <div class="form-group">

                        <label for="cardNumber">
                            Card Number
                        </label>

                        <input
                            type="text"
                            id="cardNumber"
                            name="card_number"
                            placeholder="1234 5678 9012 3456"
                            maxlength="19"
                            inputmode="numeric"
                        >

                    </div>


                    <div class="form-row">

                        <div class="form-group">

                            <label for="expiry">
                                Expiry Date
                            </label>

                            <input
                                type="text"
                                id="expiry"
                                name="card_expiry"
                                placeholder="MM/YY"
                                maxlength="5"
                                inputmode="numeric"
                            >

                        </div>


                        <div class="form-group">

                            <label for="cvv">
                                CVV
                            </label>

                            <input
                                type="password"
                                id="cvv"
                                name="card_cvv"
                                placeholder="123"
                                maxlength="4"
                                inputmode="numeric"
                            >

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     ONLINE BANKING DETAILS
                     ================================================= -->

                <div
                    class="bank-details"
                    id="bankDetails"
                    style="display: none;"
                >

                    <div class="card-details-title">
                        Online Banking Details
                    </div>


                    <div class="form-group">

                        <label for="bankName">
                            Select Bank
                        </label>

                        <select
                            id="bankName"
                            name="bank_name"
                        >

                            <option value="">
                                Select your bank
                            </option>

                            <option value="Bank of Ceylon">
                                Bank of Ceylon
                            </option>

                            <option value="Commercial Bank">
                                Commercial Bank
                            </option>

                            <option value="Hatton National Bank">
                                Hatton National Bank
                            </option>

                            <option value="Sampath Bank">
                                Sampath Bank
                            </option>

                            <option value="Nations Trust Bank">
                                Nations Trust Bank
                            </option>

                            <option value="People's Bank">
                                People's Bank
                            </option>

                            <option value="National Development Bank">
                                National Development Bank
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="bankHolder">
                            Account Holder Name
                        </label>

                        <input
                            type="text"
                            id="bankHolder"
                            name="bank_holder_name"
                            placeholder="Account holder name"
                            maxlength="100"
                        >

                    </div>


                    <div class="form-group">

                        <label for="bankReference">
                            Bank Reference Number
                        </label>

                        <input
                            type="text"
                            id="bankReference"
                            name="bank_reference"
                            placeholder="Enter payment reference"
                            maxlength="50"
                        >

                    </div>

                </div>


                <!-- =================================================
                     ACTIONS
                     ================================================= -->

                <div class="payment-actions">

                    <a
                        href="../orders/order-details.php?id=<?= (int)$order["id"] ?>"
                        class="payment-cancel"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="payment-submit"
                    >
                        Continue Payment
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>


<script>

/* =========================================================
   PAYMENT METHOD SWITCHING
   ========================================================= */

function updatePaymentUI() {

    const selected = document.querySelector(
        'input[name="payment_method"]:checked'
    );

    const cardDetails = document.getElementById("cardDetails");
    const bankDetails = document.getElementById("bankDetails");

    const cardHolder = document.getElementById("cardHolder");
    const cardNumber = document.getElementById("cardNumber");
    const expiry = document.getElementById("expiry");
    const cvv = document.getElementById("cvv");

    const bankName = document.getElementById("bankName");
    const bankHolder = document.getElementById("bankHolder");
    const bankReference = document.getElementById("bankReference");


    if (!selected) {
        return;
    }


    const method = selected.value;


    /* ---------------------------------------------------------
       HIDE BOTH SECTIONS FIRST
       --------------------------------------------------------- */

    cardDetails.style.display = "none";
    bankDetails.style.display = "none";


    /* ---------------------------------------------------------
       REMOVE REQUIRED FROM ALL OPTIONAL FIELDS
       --------------------------------------------------------- */

    cardHolder.required = false;
    cardNumber.required = false;
    expiry.required = false;
    cvv.required = false;

    bankName.required = false;
    bankHolder.required = false;
    bankReference.required = false;


    /* ---------------------------------------------------------
       CARD
       --------------------------------------------------------- */

    if (method === "Card") {

        cardDetails.style.display = "block";

        cardHolder.required = true;
        cardNumber.required = true;
        expiry.required = true;
        cvv.required = true;
    }


    /* ---------------------------------------------------------
       ONLINE BANKING
       --------------------------------------------------------- */

    else if (method === "Online Banking") {

        bankDetails.style.display = "block";

        bankName.required = true;
        bankHolder.required = true;
        bankReference.required = true;
    }


    /* ---------------------------------------------------------
       CASH ON DELIVERY
       --------------------------------------------------------- */

    else if (method === "Cash on Delivery") {

        cardDetails.style.display = "none";
        bankDetails.style.display = "none";
    }

}


/* =========================================================
   PAGE LOAD
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    updatePaymentUI();

});


/* =========================================================
   CARD NUMBER FORMATTING
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const cardNumber = document.getElementById("cardNumber");

    if (cardNumber) {

        cardNumber.addEventListener("input", function () {

            let value = this.value.replace(/\D/g, "");

            value = value.substring(0, 16);

            let formatted = value.match(/.{1,4}/g);

            this.value = formatted
                ? formatted.join(" ")
                : "";

        });

    }

});


/* =========================================================
   EXPIRY DATE FORMATTING
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const expiry = document.getElementById("expiry");

    if (expiry) {

        expiry.addEventListener("input", function () {

            let value = this.value.replace(/\D/g, "");

            value = value.substring(0, 4);

            if (value.length >= 3) {

                this.value =
                    value.substring(0, 2) +
                    "/" +
                    value.substring(2);

            } else {

                this.value = value;

            }

        });

    }

});


/* =========================================================
   CVV
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const cvv = document.getElementById("cvv");

    if (cvv) {

        cvv.addEventListener("input", function () {

            this.value = this.value
                .replace(/\D/g, "")
                .substring(0, 4);

        });

    }

});


/* =========================================================
   CARD HOLDER NAME
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const cardHolder = document.getElementById("cardHolder");

    if (cardHolder) {

        cardHolder.addEventListener("input", function () {

            this.value = this.value.replace(
                /[^a-zA-Z .'-]/g,
                ""
            );

        });

    }

});


/* =========================================================
   BANK HOLDER NAME
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const bankHolder = document.getElementById("bankHolder");

    if (bankHolder) {

        bankHolder.addEventListener("input", function () {

            this.value = this.value.replace(
                /[^a-zA-Z .'-]/g,
                ""
            );

        });

    }

});

</script>


<?php require_once "../includes/footer.php"; ?>
