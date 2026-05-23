<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
require_once '../Customize&Database/function.php';
requireLogin();

$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    $_SESSION['flash_error'] = "No order ID provided.";
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
    $_SESSION['flash_error'] = "Order cannot be confirmed at this stage.";
    header("Location: orderHistory.php");
    exit;
}

$pdo->beginTransaction();
try {
    $update = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
    $update->execute([$order_id]);
    $notifMsg = "Your order #{$order['order_number']} has been confirmed as received. Thank you!";
    $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
    $notifStmt->execute([$_SESSION['user_id'], $order_id, $notifMsg]);
    $userStmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch();
    if ($user) {
        $subject = "Order #{$order['order_number']} Confirmed";
        $body = "Dear {$user['name']},<br>Thank you for confirming receipt of your order #{$order['order_number']}. We hope you enjoy your books!";
        sendEmail($user['email'], $subject, $body);
    }

    $pdo->commit();
    $_SESSION['flash_success'] = "Order #{$order['order_number']} has been confirmed as received.";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_error'] = "Failed to confirm order: " . $e->getMessage();
}
header("Location: orderHistory.php");
exit;
?>