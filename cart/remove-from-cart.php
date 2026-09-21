
<?php

session_start();

require_once "../config/database.php";

// Check whether customer is logged in
if (!isset($_SESSION["customer_id"])) {
    header("Location: ../login.php");
    exit;
}

$customer_id = (int) $_SESSION["customer_id"];

// Check whether cart item ID was submitted
if (!isset($_POST["cart_id"]) || !is_numeric($_POST["cart_id"])) {
    header("Location: cart.php");
    exit;
}

$cart_id = (int) $_POST["cart_id"];

// Delete only the cart item belonging to the logged-in customer
$stmt = $conn->prepare(
    "DELETE FROM cart
     WHERE id = ? AND customer_id = ?"
);

$stmt->bind_param(
    "ii",
    $cart_id,
    $customer_id
);

$stmt->execute();

$stmt->close();


// Return to cart page
header("Location: cart.php");
exit;

?>

