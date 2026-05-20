<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff');
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    
    $allowed = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
    if (!in_array($new_status, $allowed)) {
        $_SESSION['flash_error'] = "Invalid status.";
        header("Location: staffManage.php");
        exit;
    }
   
    $shipped_date = ($new_status == 'shipped') ? date('Y-m-d H:i:s') : null;
    if ($new_status == 'shipped') {
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, shipped_date = ? WHERE id = ?");
        $stmt->execute([$new_status, $shipped_date, $order_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
    }

    $orderStmt = $pdo->prepare("SELECT o.*, u.email, u.name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $orderStmt->execute([$order_id]);
    $order = $orderStmt->fetch();
    
    if ($order) {
        $subject = "Your order #{$order['order_number']} status updated to {$new_status}";
        $body = "Dear {$order['name']},<br><br>Your order status has been updated to: <strong>{$new_status}</strong>.<br>";
        if ($new_status == 'shipped') {
            $body .= "Your books have been shipped on " . date('d M Y H:i', strtotime($shipped_date)) . ".<br>";
        }
        $body .= "You can track your order in your account.<br><br>Thank you.";
        sendEmail($order['email'], $subject, $body);
        $notifMsg = "Your order #{$order['order_number']} status is now {$new_status}.";
        if ($new_status == 'shipped') {
            $notifMsg .= " It was shipped on " . date('d M Y', strtotime($shipped_date)) . ".";
        }
        $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
        $notifStmt->execute([$order['user_id'], $order_id, $notifMsg]);
    }
    
    $_SESSION['flash_success'] = "Order status updated successfully.";
    header("Location: staffManage.php");
    exit;
} else {
    header("Location: staffManage.php");
    exit;
}
?>