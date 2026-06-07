<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff');
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = "Invalid request.";
    header("Location: staffManage.php");
    exit;
}

$order_id = (int)$_POST['order_id'];
$reason = trim($_POST['reason'] ?? '');

if (!$order_id || empty($reason)) {
    $_SESSION['flash_error'] = "No order ID or reason provided.";
    header("Location: staffManage.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['flash_error'] = "Order not found.";
    header("Location: staffManage.php");
    exit;
}

if ($order['payment_status'] != 'paid' || in_array($order['status'], ['completed', 'cancelled', 'shipped'])) {
    $_SESSION['flash_error'] = "This order cannot be rejected.";
    header("Location: staffManage.php");
    exit;
}

$pdo->beginTransaction();
try {
    $update = $pdo->prepare("UPDATE orders SET status = 'cancelled', cancellation_reason = ? WHERE id = ?");
    $update->execute([$reason, $order_id]);
    $items = $pdo->prepare("SELECT book_id, quantity FROM order_items WHERE order_id = ?");
    $items->execute([$order_id]);
    $restoreStock = $pdo->prepare("UPDATE books SET stock = stock + ? WHERE id = ?");
    while ($item = $items->fetch()) {
        $restoreStock->execute([$item['quantity'], $item['book_id']]);
    }

    $notifMsg = "Your order #{$order['order_number']} has been rejected. Reason: {$reason}. Any payment made will be refunded within 5-7 business days.";
    $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
    $notifStmt->execute([$order['user_id'], $order_id, $notifMsg]);
    $userStmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
    $userStmt->execute([$order['user_id']]);
    $user = $userStmt->fetch();
    if ($user) {
        $subject = "Your order #{$order['order_number']} has been rejected";
        $body = "Dear {$user['name']},<br><br>Your order #{$order['order_number']} has been rejected.<br>";
        $body .= "<strong>Rejection reason:</strong> " . nl2br(htmlspecialchars($reason)) . "<br><br>";
        $body .= "If you have already made a payment, a refund will be processed within 5-7 business days.<br><br>";
        $body .= "Thank you for shopping with BookNest.";
        sendEmail($user['email'], $subject, $body);
    }

    $pdo->commit();
    $_SESSION['flash_success'] = "Order #{$order['order_number']} has been rejected. Customer notified.";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_error'] = "Failed to reject order: " . $e->getMessage();
}
header("Location: staffManage.php");
exit;
?>