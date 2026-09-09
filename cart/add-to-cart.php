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

    header("Location: ../");

    exit();
}

$customerId = $_SESSION["customer_id"];

$flowerId = isset($_POST["flower_id"])
    ? (int) $_POST["flower_id"]
    : 0;

$quantity = isset($_POST["quantity"])
    ? (int) $_POST["quantity"]
    : 1;

if ($flowerId <= 0) {

    die("Invalid flower.");

}

if ($quantity <= 0) {

    die("Invalid quantity.");

}


// -----------------------------------------
// Check flower
// -----------------------------------------

$stmt = $conn->prepare(
    "SELECT
        id,
        flower_name,
        price,
        stock_quantity,
        status
     FROM flowers
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $flowerId
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    die("Flower not found.");
}

$flower = $result->fetch_assoc();


// -----------------------------------------
// Check availability
// -----------------------------------------

if (
    $flower["status"] !== "Available" ||
    $flower["stock_quantity"] <= 0
) {

    die("This flower is currently unavailable.");
}


// -----------------------------------------
// Check existing cart item
// -----------------------------------------

$stmt = $conn->prepare(
    "SELECT
        id,
        quantity
     FROM cart
     WHERE customer_id = ?
     AND flower_id = ?"
);

$stmt->bind_param(
    "ii",
    $customerId,
    $flowerId
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 1) {

    // Already exists
    $cartItem = $result->fetch_assoc();

    $newQuantity =
        $cartItem["quantity"] + $quantity;


    // Check stock
    if (
        $newQuantity >
        $flower["stock_quantity"]
    ) {

        die(
            "Requested quantity exceeds available stock."
        );
    }


    // Update quantity
    $stmt = $conn->prepare(
        "UPDATE cart
         SET quantity = ?
         WHERE id = ?
         AND customer_id = ?"
    );

    $stmt->bind_param(
        "iii",
        $newQuantity,
        $cartItem["id"],
        $customerId
    );

    $stmt->execute();

} else {

    // New cart item
    if (
        $quantity >
        $flower["stock_quantity"]
    ) {

        die(
            "Requested quantity exceeds available stock."
        );
    }


    $stmt = $conn->prepare(
        "INSERT INTO cart
        (
            customer_id,
            flower_id,
            quantity
        )
        VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iii",
        $customerId,
        $flowerId,
        $quantity
    );

    $stmt->execute();
}


// -----------------------------------------
// Go to cart
// -----------------------------------------

header("Location: cart.php");

exit();

?>