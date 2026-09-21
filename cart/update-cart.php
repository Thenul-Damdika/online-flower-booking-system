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

$quantity = isset($_POST["quantity"])
    ? (int) $_POST["quantity"]
    : 0;


if ($cartId <= 0 || $quantity <= 0) {

    header("Location: cart.php");

    exit();
}


// -----------------------------------------
// Check cart belongs to customer
// and get flower stock
// -----------------------------------------

$stmt = $conn->prepare(
    "SELECT
        c.id,
        f.stock_quantity,
        f.status

     FROM cart c

     INNER JOIN flowers f
        ON c.flower_id = f.id

     WHERE c.id = ?
     AND c.customer_id = ?"
);

$stmt->bind_param(
    "ii",
    $cartId,
    $customerId
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    die("Invalid cart item.");
}


$item = $result->fetch_assoc();


// -----------------------------------------
// Check stock
// -----------------------------------------

if (
    $quantity >
    $item["stock_quantity"]
) {

    die(
        "Quantity exceeds available stock."
    );
}


// -----------------------------------------
// Update
// -----------------------------------------

$stmt = $conn->prepare(
    "UPDATE cart

     SET quantity = ?

     WHERE id = ?

     AND customer_id = ?"
);

$stmt->bind_param(
    "iii",
    $quantity,
    $cartId,
    $customerId
);

$stmt->execute();


header("Location: cart.php");

exit();

?>