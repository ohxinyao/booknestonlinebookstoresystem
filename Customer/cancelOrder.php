<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    $_SESSION['flash_error'] = "Invalid order ID.";
    header("Location: orderHistory.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['flash_error'] = "Order not found.";
    header("Location: orderHistory.php");
    exit;
}

if ($order['status'] != 'pending' || $order['payment_status'] != 'unpaid') {
    $_SESSION['flash_error'] = "This order cannot be cancelled at this stage.";
    header("Location: orderHistory.php");
    exit;
}

$pdo->beginTransaction();
try {
    $update = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $update->execute([$order_id]);

    $items = $pdo->prepare("SELECT book_id, quantity FROM order_items WHERE order_id = ?");
    $items->execute([$order_id]);
    $restoreStock = $pdo->prepare("UPDATE books SET stock = stock + ? WHERE id = ?");
    while ($item = $items->fetch()) {
        $restoreStock->execute([$item['quantity'], $item['book_id']]);
    }

    $notifMsg = "Your order #{$order['order_number']} has been cancelled as requested.";
    $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
    $notifStmt->execute([$_SESSION['user_id'], $order_id, $notifMsg]);

    $pdo->commit();
    $_SESSION['flash_success'] = "Order #{$order['order_number']} has been cancelled.";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_error'] = "Failed to cancel order: " . $e->getMessage();
}
header("Location: orderHistory.php");
exit;
?>