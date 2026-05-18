<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff'); 
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $reason = trim($_POST['reject_reason'] ?? '');
    if (!$order_id || empty($reason)) {
        $_SESSION['flash_error'] = "Invalid request. Reason required.";
        header("Location: staffManage.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT o.*, u.email, u.name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    if (!$order) {
        $_SESSION['flash_error'] = "Order not found.";
        header("Location: staffManage.php");
        exit;
    }

    if ($order['payment_status'] != 'paid' || in_array($order['status'], ['completed', 'cancelled'])) {
        $_SESSION['flash_error'] = "This order cannot be rejected.";
        header("Location: staffManage.php");
        exit;
    }

    $update = $pdo->prepare("UPDATE orders SET status = 'cancelled', rejection_reason = ? WHERE id = ?");
    $update->execute([$reason, $order_id]);
    $items = $pdo->prepare("SELECT book_id, quantity FROM order_items WHERE order_id = ?");
    $items->execute([$order_id]);
    $restore = $pdo->prepare("UPDATE books SET stock = stock + ? WHERE id = ?");
    while ($item = $items->fetch()) {
        $restore->execute([$item['quantity'], $item['book_id']]);
    }

    $subject = "Your order #{$order['order_number']} has been rejected";
    $body = "Dear {$order['name']},<br><br>We regret to inform you that your order #{$order['order_number']} has been rejected due to the following reason:<br><br><strong>{$reason}</strong><br><br>If you have already made a payment, please contact us to arrange a refund. You may place a new order after correcting the issue.<br><br>Thank you.";
    sendEmail($order['email'], $subject, $body);
    $_SESSION['flash_success'] = "Order #{$order['order_number']} has been rejected. Customer notified.";
    header("Location: staffManage.php");
    exit;
} else {
    header("Location: staffManage.php");
    exit;
}
?>