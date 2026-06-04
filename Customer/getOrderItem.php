<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

$check = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
$check->execute([$order_id, $_SESSION['user_id']]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT oi.quantity, oi.price, b.title
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$total = 0;
$result = [];
foreach ($items as $item) {
    $subtotal = $item['quantity'] * $item['price'];
    $total += $subtotal;
    $result[] = [
        'title' => $item['title'],
        'quantity' => $item['quantity'],
        'price' => number_format($item['price'], 2),
        'subtotal' => number_format($subtotal, 2)
    ];
}

echo json_encode([
    'success' => true,
    'items' => $result,
    'total' => number_format($total, 2)
]);
?>