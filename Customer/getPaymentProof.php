<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$stmt = $pdo->prepare("SELECT id, order_number, payment_proof FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order || empty($order['payment_proof'])) {
    echo json_encode(['success' => false]);
    exit;
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$fullUrl = $protocol . $host . '/finalproject/booknestonlinebookstoresystem/assets/uploads/payments/' . rawurlencode($order['payment_proof']);

echo json_encode([
    'success' => true,
    'order_number' => $order['order_number'],
    'file' => $fullUrl
]);
?>