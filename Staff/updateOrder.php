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

    $stmt = $pdo->prepare("SELECT payment_status, status, shipped_date, order_number, user_id FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    if (!$order) {
        $_SESSION['flash_error'] = "Order not found.";
        header("Location: staffManage.php");
        exit;
    }

    if ($order['status'] == 'cancelled') {
        $_SESSION['flash_error'] = "Cancelled orders cannot be modified.";
        header("Location: staffManage.php");
        exit;
    }
    
    if ($order['status'] == 'shipped' && in_array($new_status, ['pending', 'paid', 'processing'])) {
        $_SESSION['flash_error'] = "Shipped orders cannot be reverted.";
        header("Location: staffManage.php");
        exit;
    }

    if ($new_status == 'completed' && empty($order['shipped_date'])) {
        $_SESSION['flash_error'] = "Order must be shipped before it can be completed.";
        header("Location: staffManage.php");
        exit;
    }
    $payment_status = $order['payment_status'];

    if ($payment_status !== 'paid' && in_array($new_status, ['processing', 'shipped', 'completed'])) {
        $_SESSION['flash_error'] = "Cannot change status to '$new_status' because order is unpaid. Customer must upload payment proof first.";
        header("Location: staffManage.php");
        exit;
    }

    if ($payment_status === 'paid' && $new_status === 'pending') {
        $_SESSION['flash_error'] = "Order already paid, cannot revert to pending.";
        header("Location: staffManage.php");
        exit;
    }

    $shipped_date = ($new_status == 'shipped') ? date('Y-m-d H:i:s') : null;
    
    $pdo->beginTransaction();
    try {
        if ($new_status == 'shipped') {
            $stmt = $pdo->prepare("UPDATE orders SET status = ?, shipped_date = ? WHERE id = ?");
            $stmt->execute([$new_status, $shipped_date, $order_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
        }

        $userStmt = $pdo->prepare("SELECT u.email, u.name, o.order_number FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $userStmt->execute([$order_id]);
        $user = $userStmt->fetch();

        if ($user) {
            $subject = "Your order #{$user['order_number']} status updated to {$new_status}";
            $body = "Dear {$user['name']},<br><br>Your order status has been updated to: <strong>{$new_status}</strong>.<br>";
            if ($new_status == 'shipped') {
                $body .= "Your books have been shipped on " . date('d M Y H:i', strtotime($shipped_date)) . ".<br>";
            }
            $body .= "Thank you for shopping with BookNest.";
            sendEmail($user['email'], $subject, $body);

            $notifMsg = "Your order #{$user['order_number']} status is now {$new_status}.";
            if ($new_status == 'shipped') {
                $notifMsg .= " It was shipped on " . date('d M Y', strtotime($shipped_date)) . ".";
            }
            $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
            $notifStmt->execute([$order['user_id'], $order_id, $notifMsg]);
        }

        $pdo->commit();
        $_SESSION['flash_success'] = "Order status updated successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = "Update failed: " . $e->getMessage();
    }
    header("Location: staffManage.php");
    exit;
} else {
    header("Location: staffManage.php");
    exit;
}