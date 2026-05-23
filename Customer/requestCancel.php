<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
require_once '../Customize&Database/function.php';
requireLogin();

$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    header("Location: orderHistory.php");
    exit;
}

$stmt = $pdo->prepare("SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['flash_error'] = "Order not found.";
    header("Location: orderHistory.php");
    exit;
}

$status = strtolower($order['status']);
$payment_status = strtolower($order['payment_status']);

if ($payment_status != 'paid' || $status == 'completed' || $status == 'cancelled') {
    $_SESSION['flash_error'] = "This order cannot be cancelled at this stage. (Status: $status, Payment: $payment_status)";
    header("Location: orderHistory.php");
    exit;
}

$adminEmails = $pdo->query("SELECT email FROM users WHERE role = 'admin'")->fetchAll(PDO::FETCH_COLUMN);
$subject = "Order Cancellation Request";
$body = "Customer {$order['name']} (Email: {$order['email']}) has requested to cancel order #{$order['order_number']}.<br>
         Order amount: RM " . number_format($order['total_amount'], 2) . "<br>
         Please log in to the admin panel to process this request.";

foreach ($adminEmails as $adminEmail) {
    sendEmail($adminEmail, $subject, $body);
}

$_SESSION['flash_success'] = "Your cancellation request has been sent. Admin will review it shortly.";
header("Location: orderHistory.php");
exit;
?>