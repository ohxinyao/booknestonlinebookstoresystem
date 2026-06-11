<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

$userRole = $_SESSION['user_role'] ?? '';
$userId = $_SESSION['user_id'] ?? 0;

if ($userRole === 'admin' || $userRole === 'staff') {
    $stmt = $pdo->prepare("SELECT order_number, payment_proof FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
} else {
    requireLogin();
    $stmt = $pdo->prepare("SELECT order_number, payment_proof FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
}

$order = $stmt->fetch();

if (!$order || empty($order['payment_proof'])) {
    echo json_encode(['success' => false, 'message' => 'No payment proof found']);
    exit;
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$fileUrl = $protocol . $host . '/finalproject/booknestonlinebookstoresystem/assets/uploads/payments/' . rawurlencode($order['payment_proof']);

echo json_encode([
    'success' => true,
    'order_number' => $order['order_number'],
    'file' => $fileUrl
]);
?>