<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
require_once '../Customize&Database/function.php';
requireLogin();

$order_id = $_GET['order_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) {
    die("Invalid order.");
}
if ($order['payment_status'] == 'paid') {
    die("Payment already processed.");
}

$originalTotal = $order['total_amount'] + ($order['discount_amount'] ?? 0);
$discount = $order['discount_amount'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_proof'])) {
    $uploadDir = '../assets/uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = uploadFile($_FILES['payment_proof'], $uploadDir, ['jpg','jpeg','png','pdf']);
    if ($fileName) {
        $update = $pdo->prepare("UPDATE orders SET payment_proof = ?, payment_status = 'paid', status = 'paid' WHERE id = ?");
        $update->execute([$fileName, $order_id]);
        $success = "Payment proof uploaded. Your order is now marked as paid. Staff will process it soon.";
    } else {
        $error = "Upload failed. Only JPG, PNG, PDF allowed.";
    }
}
include '../Customize&Database/header.php';
?>
<h2>Upload Payment Proof</h2>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Order #<?= htmlspecialchars($order['order_number']) ?></strong></p>
        <p>Subtotal: RM <?= number_format($originalTotal, 2) ?></p>
        <?php if ($discount > 0): ?>
            <p>Discount: - RM <?= number_format($discount, 2) ?></p>
        <?php endif; ?>
        <p><strong>Final Total: RM <?= number_format($order['total_amount'], 2) ?></strong></p>
    </div>
</div>

<div class="alert alert-info">
    <h5>📌 Bank Transfer Instructions</h5>
    <p>Please transfer the exact amount to the following bank account:</p>
    <ul>
        <li><strong>Bank Name:</strong> Maybank</li>
        <li><strong>Account Name:</strong> BookNest Sdn Bhd</li>
        <li><strong>Account Number:</strong> 5123-4567-8901</li>
        <li><strong>Reference:</strong> Your Order #<?= htmlspecialchars($order['order_number']) ?></li>
    </ul>
    <p>After transferring, upload your payment receipt (screenshot or PDF) below.</p>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?> <a href="orderHistory.php">Back to Orders</a></div>
<?php else: ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Payment Receipt (Screenshot of payment transfer)</label>
            <input type="file" name="payment_proof" class="form-control" accept="image/jpeg,image/png,application/pdf" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload Proof</button>
        <a href="orderHistory.php" class="btn btn-secondary">Cancel</a>
    </form>
    <?php if (isset($error)) echo "<div class='alert alert-danger mt-2'>$error</div>"; ?>
<?php endif; ?>
<?php include '../Customize&Database/footer.php'; ?>