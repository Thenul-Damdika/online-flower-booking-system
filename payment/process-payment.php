<?php
session_start();
require_once "../config/database.php";


/* =========================================================
   LOGIN CHECK
   ========================================================= */

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../customer/login.php");
    exit();
}


/* =========================================================
   POST CHECK
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../orders/orders.php");
    exit();
}


$customerId = (int) $_SESSION["customer_id"];
$orderId = (int)($_POST["order_id"] ?? 0);
$paymentMethod = trim($_POST["payment_method"] ?? "");


/* =========================================================
   BASIC VALIDATION
   ========================================================= */

if ($orderId <= 0) {
    die("Invalid order.");
}

$allowedMethods = [
    "Cash on Delivery",
    "Card",
    "Online Banking"
];

if (!in_array($paymentMethod, $allowedMethods, true)) {
    die("Invalid payment method.");
}


/* =========================================================
   CARD VARIABLES
   ========================================================= */

$cardHolderName = null;
$cardLastFour = null;
$cardNumber = "";
$cardExpiry = "";
$cardCvv = "";


/* =========================================================
   BANK VARIABLES
   ========================================================= */

$bankName = null;
$bankHolderName = null;
$bankReference = null;


/* =========================================================
   CARD VALIDATION
   ========================================================= */

if ($paymentMethod === "Card") {

    $cardHolderName = trim($_POST["card_holder_name"] ?? "");
    $cardNumber = preg_replace(
        "/\D/",
        "",
        $_POST["card_number"] ?? ""
    );
    $cardExpiry = trim($_POST["card_expiry"] ?? "");
    $cardCvv = preg_replace(
        "/\D/",
        "",
        $_POST["card_cvv"] ?? ""
    );


    /* Card holder */

    if ($cardHolderName === "") {
        die("Card holder name is required.");
    }

    if (!preg_match(
        "/^[a-zA-Z .'-]+$/",
        $cardHolderName
    )) {
        die("Invalid card holder name.");
    }


    /* Card number */

    if (strlen($cardNumber) !== 16) {
        die("Card number must contain 16 digits.");
    }


    /* Expiry */

    if (!preg_match(
        "/^(0[1-9]|1[0-2])\/([0-9]{2})$/",
        $cardExpiry,
        $matches
    )) {
        die("Invalid expiry date. Use MM/YY.");
    }

    $expiryMonth = (int)$matches[1];
    $expiryYear = 2000 + (int)$matches[2];

    $currentMonth = (int)date("m");
    $currentYear = (int)date("Y");

    if (
        $expiryYear < $currentYear ||
        (
            $expiryYear === $currentYear &&
            $expiryMonth < $currentMonth
        )
    ) {
        die("Card has expired.");
    }


    /* CVV */

    if (!preg_match("/^[0-9]{3,4}$/", $cardCvv)) {
        die("CVV must contain 3 or 4 digits.");
    }


    /*
     * Store only the last four digits.
     * Never store the complete card number or CVV.
     */

    $cardLastFour = substr($cardNumber, -4);
}


/* =========================================================
   ONLINE BANKING VALIDATION
   ========================================================= */

if ($paymentMethod === "Online Banking") {

    $bankName = trim($_POST["bank_name"] ?? "");
    $bankHolderName = trim($_POST["bank_holder_name"] ?? "");
    $bankReference = trim($_POST["bank_reference"] ?? "");


    $allowedBanks = [
        "Bank of Ceylon",
        "Commercial Bank",
        "Hatton National Bank",
        "Sampath Bank",
        "Nations Trust Bank",
        "People's Bank",
        "National Development Bank"
    ];


    if (!in_array($bankName, $allowedBanks, true)) {
        die("Please select a valid bank.");
    }


    if ($bankHolderName === "") {
        die("Account holder name is required.");
    }


    if (!preg_match(
        "/^[a-zA-Z .'-]+$/",
        $bankHolderName
    )) {
        die("Invalid account holder name.");
    }


    if ($bankReference === "") {
        die("Bank reference number is required.");
    }


    if (strlen($bankReference) > 50) {
        die("Bank reference number is too long.");
    }
}


/* =========================================================
   DATABASE TRANSACTION
   ========================================================= */

try {

    $conn->begin_transaction();


    /* ---------------------------------------------------------
       GET ORDER
       --------------------------------------------------------- */

    $orderStmt = $conn->prepare(
        "SELECT id, total_amount, order_status
         FROM orders
         WHERE id = ? AND customer_id = ?
         FOR UPDATE"
    );

    $orderStmt->bind_param(
        "ii",
        $orderId,
        $customerId
    );

    $orderStmt->execute();

    $order = $orderStmt
        ->get_result()
        ->fetch_assoc();


    if (!$order) {
        throw new Exception("Order not found.");
    }


    /* ---------------------------------------------------------
       ORDER STATUS
       --------------------------------------------------------- */

    if ($order["order_status"] !== "Pending") {
        throw new Exception(
            "This order is not available for payment."
        );
    }


    /* ---------------------------------------------------------
       GET PAYMENT RECORD
       --------------------------------------------------------- */

    $paymentStmt = $conn->prepare(
        "SELECT id
         FROM payments
         WHERE order_id = ?
         ORDER BY id DESC
         LIMIT 1
         FOR UPDATE"
    );

    $paymentStmt->bind_param(
        "i",
        $orderId
    );

    $paymentStmt->execute();

    $payment = $paymentStmt
        ->get_result()
        ->fetch_assoc();


    if (!$payment) {
        throw new Exception(
            "Payment record not found."
        );
    }


    $paymentId = (int)$payment["id"];


    /* ---------------------------------------------------------
       TRANSACTION ID
       --------------------------------------------------------- */

    $transactionId = null;
    $paymentStatus = "Pending";
    $orderStatus = "Pending";
    $paymentDate = null;


    /*
     * COD:
     * Payment remains Pending.
     * Order remains Pending.
     */

    if ($paymentMethod === "Cash on Delivery") {

        $paymentStatus = "Pending";
        $orderStatus = "Pending";
        $transactionId = null;
        $paymentDate = null;
    }


    /*
     * Card / Online Banking:
     * Payment becomes Paid.
     * Order becomes Confirmed.
     */

    else {

        $paymentStatus = "Paid";
        $orderStatus = "Confirmed";
        $transactionId =
            "TXN-" .
            date("YmdHis") .
            "-" .
            strtoupper(bin2hex(random_bytes(3)));

        $paymentDate = date("Y-m-d H:i:s");
    }


    /* ---------------------------------------------------------
       UPDATE PAYMENT
       --------------------------------------------------------- */

    $updatePayment = $conn->prepare(
        "UPDATE payments
         SET amount = ?,
             payment_method = ?,
             card_holder_name = ?,
             card_last_four = ?,
             bank_name = ?,
             bank_holder_name = ?,
             bank_reference = ?,
             payment_status = ?,
             transaction_id = ?,
             payment_date = ?
         WHERE id = ?"
    );


    $totalAmount = (float)$order["total_amount"];


    $updatePayment->bind_param(
        "dsssssssssi",
        $totalAmount,
        $paymentMethod,
        $cardHolderName,
        $cardLastFour,
        $bankName,
        $bankHolderName,
        $bankReference,
        $paymentStatus,
        $transactionId,
        $paymentDate,
        $paymentId
    );


    if (!$updatePayment->execute()) {
        throw new Exception(
            "Unable to update payment."
        );
    }


    /* ---------------------------------------------------------
       UPDATE ORDER STATUS
       --------------------------------------------------------- */

    $updateOrder = $conn->prepare(
        "UPDATE orders
         SET order_status = ?
         WHERE id = ? AND customer_id = ?"
    );

    $updateOrder->bind_param(
        "sii",
        $orderStatus,
        $orderId,
        $customerId
    );

    if (!$updateOrder->execute()) {
        throw new Exception(
            "Unable to update order."
        );
    }


    /* ---------------------------------------------------------
       COMMIT
       --------------------------------------------------------- */

    $conn->commit();


    /* ---------------------------------------------------------
       SUCCESS
       --------------------------------------------------------- */

    header(
        "Location: payment-success.php?order_id=" .
        $orderId
    );

    exit();


} catch (Throwable $e) {

    if ($conn->errno === 0) {
        // Nothing required here.
    }

    try {
        $conn->rollback();
    } catch (Throwable $rollbackError) {
        // Ignore rollback errors.
    }

    die(
        "Unable to process payment: " .
        htmlspecialchars($e->getMessage())
    );
}
?>
