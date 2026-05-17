<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff');
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    
    $allowed = ['pending', 'paid', 'processing', 'completed', 'cancelled'];
    if (!in_array($new_status, $allowed)) {
        $_SESSION['flash_error'] = "Invalid status.";
        header("Location: staffManage.php");
        exit;
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    $orderStmt = $pdo->prepare("SELECT o.*, u.email, u.name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $orderStmt->execute([$order_id]);
    $order = $orderStmt->fetch();
    if ($order) {
        $subject = "Your order #{$order['order_number']} status updated to {$new_status}";
        $body = "Dear {$order['name']},<br><br>Your order status has been updated to: <strong>{$new_status}</strong>.<br>You can track your order in your account.<br><br>Thank you.";
        sendEmail($order['email'], $subject, $body);
    }
    
    $_SESSION['flash_success'] = "Order status updated successfully.";
    header("Location: staffManage.php");
    exit;
} else {
    header("Location: staffManage.php");
    exit;
}
?>