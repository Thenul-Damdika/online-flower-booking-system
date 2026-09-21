```php
<?php

session_start();

require_once "../config/database.php";

header("Content-Type: application/json");

// Check customer login
if (!isset($_SESSION["customer_id"])) {
    echo json_encode([
        "success" => false,
        "login_required" => true,
        "message" => "Please login to add items to your cart."
    ]);
    exit;
}

$customer_id = (int) $_SESSION["customer_id"];

// Check flower ID
if (!isset($_POST["flower_id"]) || !is_numeric($_POST["flower_id"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid flower."
    ]);
    exit;
}

$flower_id = (int) $_POST["flower_id"];

// Quantity
$quantity = 1;

if (isset($_POST["quantity"]) && is_numeric($_POST["quantity"])) {
    $quantity = (int) $_POST["quantity"];
}

if ($quantity < 1) {
    $quantity = 1;
}


// Check whether flower already exists in cart
$stmt = $conn->prepare(
    "SELECT id, quantity
     FROM cart
     WHERE customer_id = ? AND flower_id = ?"
);

$stmt->bind_param(
    "ii",
    $customer_id,
    $flower_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows > 0) {

    // Existing item → increase quantity
    $cartItem = $result->fetch_assoc();

    $newQuantity = (int) $cartItem["quantity"] + $quantity;

    $update = $conn->prepare(
        "UPDATE cart
         SET quantity = ?, updated_at = NOW()
         WHERE id = ? AND customer_id = ?"
    );

    $update->bind_param(
        "iii",
        $newQuantity,
        $cartItem["id"],
        $customer_id
    );

    $update->execute();
    $update->close();

} else {

    // New item → add to cart
    $insert = $conn->prepare(
        "INSERT INTO cart
        (customer_id, flower_id, quantity, created_at, updated_at)
        VALUES (?, ?, ?, NOW(), NOW())"
    );

    $insert->bind_param(
        "iii",
        $customer_id,
        $flower_id,
        $quantity
    );

    $insert->execute();
    $insert->close();
}

$stmt->close();


// Get total number of items in cart
$countStmt = $conn->prepare(
    "SELECT COALESCE(SUM(quantity), 0) AS total_items
     FROM cart
     WHERE customer_id = ?"
);

$countStmt->bind_param(
    "i",
    $customer_id
);

$countStmt->execute();

$countResult = $countStmt->get_result();
$countData = $countResult->fetch_assoc();

$totalItems = (int) $countData["total_items"];

$countStmt->close();


// Send response to JavaScript
echo json_encode([
    "success" => true,
    "message" => "Added to cart successfully!",
    "cart_count" => $totalItems
]);

exit;
?>
```
