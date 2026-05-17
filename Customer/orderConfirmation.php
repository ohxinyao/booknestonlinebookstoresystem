<?php
session_start();
require_once '../Customize&Database/access.php';
requireLogin();

$orderNumber = $_GET['order_number'] ?? '';
if (!$orderNumber) {
    header("Location: index.php");
    exit;
}

require_once '../Customize&Database/setDatabase.php';
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ?");
$stmt->execute([$orderNumber, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found.");
}

$originalTotal = $order['total_amount'] + $order['discount_amount'];
$discount = $order['discount_amount'];
$finalTotal = $order['total_amount'];

include '../Customize&Database/header.php';
?>
<div class="container text-center">
    <div class="alert alert-success mt-5">
        <h3>✅ Order Placed Successfully!</h3>
        <p>Your order number is: <strong><?= htmlspecialchars($orderNumber) ?></strong></p>
        <?php if ($discount > 0): ?>
            <p><strong>Order Summary:</strong><br>
            Original total: RM <?= number_format($originalTotal, 2) ?><br>
            Discount applied: - RM <?= number_format($discount, 2) ?><br>
            <strong>Final total paid: RM <?= number_format($finalTotal, 2) ?></strong>
            </p>
        <?php else: ?>
            <p>Total amount: <strong>RM <?= number_format($finalTotal, 2) ?></strong></p>
        <?php endif; ?>
        <p>Please go to <a href="orderHistory.php">My Orders</a> to upload payment proof.</p>
        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</div>
<?php include '../Customize&Database/footer.php'; ?>