<?php

session_start();

require_once "../config/database.php";


if (!isset($_SESSION["customer_id"])) {

    header(
        "Location: ../customer/login.php"
    );

    exit();
}


if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: cart.php");

    exit();
}


$customerId = $_SESSION["customer_id"];

$cartId = isset($_POST["cart_id"])
    ? (int) $_POST["cart_id"]
    : 0;


if ($cartId <= 0) {

    header("Location: cart.php");

    exit();
}


// -----------------------------------------
// Delete only customer's own cart item
// -----------------------------------------

$stmt = $conn->prepare(
    "DELETE FROM cart

     WHERE id = ?

     AND customer_id = ?"
);

$stmt->bind_param(
    "ii",
    $cartId,
    $customerId
);

$stmt->execute();


header("Location: cart.php");

exit();

?>