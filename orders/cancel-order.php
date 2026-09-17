<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["customer_id"])) {
    header("Location: ../customer/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit();
}

$customerId = (int) $_SESSION["customer_id"];
$orderId = (int)($_POST["order_id"] ?? 0);

if ($orderId <= 0) {
    header("Location: orders.php");
    exit();
}

try {
    $conn->begin_transaction();

    $orderStmt = $conn->prepare(
        "SELECT order_status FROM orders
         WHERE id = ? AND customer_id = ?
         FOR UPDATE"
    );
    $orderStmt->bind_param("ii", $orderId, $customerId);
    $orderStmt->execute();
    $order = $orderStmt->get_result()->fetch_assoc();

    if (!$order) {
        throw new Exception("Order not found.");
    }

    if ($order["order_status"] !== "Pending") {
        throw new Exception("Only pending orders can be cancelled.");
    }

    $itemsStmt = $conn->prepare(
        "SELECT flower_id, quantity FROM order_items WHERE order_id = ?"
    );
    $itemsStmt->bind_param("i", $orderId);
    $itemsStmt->execute();
    $items = $itemsStmt->get_result();

    $stockStmt = $conn->prepare(
        "UPDATE flowers
         SET stock_quantity = stock_quantity + ?,
             status = CASE
                 WHEN stock_quantity + ? > 0 AND status = 'Out of Stock'
                 THEN 'Available'
                 ELSE status
             END
         WHERE id = ?"
    );

    while ($item = $items->fetch_assoc()) {
        $quantity = (int)$item["quantity"];
        $flowerId = (int)$item["flower_id"];

        $stockStmt->bind_param("iii", $quantity, $quantity, $flowerId);
        $stockStmt->execute();
    }

    $updateStmt = $conn->prepare(
        "UPDATE orders SET order_status = 'Cancelled'
         WHERE id = ? AND customer_id = ?"
    );
    $updateStmt->bind_param("ii", $orderId, $customerId);
    $updateStmt->execute();

    $paymentUpdate = $conn->prepare(
        "UPDATE payments SET payment_status = 'Refunded'
         WHERE order_id = ? AND payment_status = 'Paid'"
    );
    $paymentUpdate->bind_param("i", $orderId);
    $paymentUpdate->execute();

    $conn->commit();

    header("Location: order-details.php?id=" . $orderId);
    exit();

} catch (Throwable $e) {
    $conn->rollback();
    die("Unable to cancel order: " . htmlspecialchars($e->getMessage()));
}
?>
