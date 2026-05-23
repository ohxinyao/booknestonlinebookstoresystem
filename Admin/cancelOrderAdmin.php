<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('admin');
require_once '../Customize&Database/function.php';
require_once '../Customize&Database/setDatabase.php';  

$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    $_SESSION['flash_error'] = "No order ID provided.";
    header("Location: manageOrder.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['flash_error'] = "Order not found.";
    header("Location: manageOrder.php");
    exit;
}

$status = strtolower($order['status']);
$payment_status = strtolower($order['payment_status']);

if ($payment_status != 'paid' || $status == 'completed' || $status == 'cancelled') {
    $_SESSION['flash_error'] = "This order cannot be cancelled.";
    header("Location: manageOrder.php");
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

    $notifMsg = "Your order #{$order['order_number']} has been cancelled by admin. Any payment made will be refunded within 5-7 business days.";
    $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
    $notifStmt->execute([$order['user_id'], $order_id, $notifMsg]);
    $userStmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
    $userStmt->execute([$order['user_id']]);
    $user = $userStmt->fetch();
    if ($user) {
        $subject = "Your order #{$order['order_number']} has been cancelled";
        $body = "Dear {$user['name']},<br><br>Your order #{$order['order_number']} has been cancelled as requested. Any payment made will be refunded within 5-7 business days.<br><br>Thank you for shopping with BookNest.";
        sendEmail($user['email'], $subject, $body);
    }

    $pdo->commit();
    $_SESSION['flash_success'] = "Order #{$order['order_number']} has been cancelled and stock restored. Customer notified.";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_error'] = "Failed to cancel order: " . $e->getMessage();
}
header("Location: manageOrder.php");
exit;
?>