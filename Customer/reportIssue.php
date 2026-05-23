<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
require_once '../Customize&Database/function.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'] ?? 0;
    $reason = trim($_POST['reason']);
    if (!$order_id || empty($reason)) {
        $_SESSION['flash_error'] = "Invalid request.";
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

    if ($order['status'] != 'processing' && $order['status'] != 'paid') {
        $_SESSION['flash_error'] = "This order cannot be reported at this stage.";
        header("Location: orderHistory.php");
        exit;
    }

    $pdo->beginTransaction();
    try {
        $update = $pdo->prepare("UPDATE orders SET status = 'cancelled', rejection_reason = ? WHERE id = ?");
        $update->execute([$reason, $order_id]);
        $notifMsg = "Your order #{$order['order_number']} has been cancelled due to the issue you reported. A refund will be processed.";
        $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
        $notifStmt->execute([$_SESSION['user_id'], $order_id, $notifMsg]);
        $adminEmails = $pdo->query("SELECT email FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_COLUMN);
        $subject = "Order Issue Reported - #{$order['order_number']}";
        $body = "Customer {$_SESSION['user_name']} (ID: {$_SESSION['user_id']}) reported an issue with order #{$order['order_number']}.<br>Reason: " . nl2br(htmlspecialchars($reason)) . "<br>Please log in to admin panel to handle refund/compensation.";
        foreach ($adminEmails as $adminEmail) {
            sendEmail($adminEmail, $subject, $body);
        }
        $userStmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
        $userStmt->execute([$_SESSION['user_id']]);
        $user = $userStmt->fetch();
        if ($user) {
            $subject = "Order #{$order['order_number']} Cancelled Due to Issue";
            $body = "Dear {$user['name']},<br>Your order #{$order['order_number']} has been cancelled as per your report. We will process a refund within 5-7 business days.<br>Reason: " . nl2br(htmlspecialchars($reason));
            sendEmail($user['email'], $subject, $body);
        }

        $pdo->commit();
        $_SESSION['flash_success'] = "Thank you for reporting. Your order has been cancelled and we will process your refund shortly.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = "Failed to process report: " . $e->getMessage();
    }
    header("Location: orderHistory.php");
    exit;
} else {
    header("Location: orderHistory.php");
    exit;
}
?>