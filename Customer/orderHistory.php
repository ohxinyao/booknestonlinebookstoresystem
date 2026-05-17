<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, order_number, total_amount, discount_amount, voucher_code, status, payment_status, payment_proof, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

include '../Customize&Database/header.php';

if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success'>{$_SESSION['flash_success']}</div>";
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    echo "<div class='alert alert-danger'>{$_SESSION['flash_error']}</div>";
    unset($_SESSION['flash_error']);
}
?>

<h2>My Orders</h2>

<?php if (count($orders) == 0): ?>
    <div class="alert alert-info">You haven't placed any orders yet. <a href="index.php">Start shopping</a></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Subtotal</th>
                    <th>Discount</th>
                    <th>Final Total</th>
                    <th>Voucher</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Proof</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): 
                $discount = $order['discount_amount'] ?? 0;
                $original = $order['total_amount'] + $discount;
            ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_number']) ?></td>
                    <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                    <td>RM <?= number_format($original, 2) ?></td>
                    <td><?= $discount > 0 ? 'RM ' . number_format($discount, 2) : '-' ?></td>
                    <td><strong>RM <?= number_format($order['total_amount'], 2) ?></strong></td>
                    <td><?= htmlspecialchars($order['voucher_code'] ?? '-') ?></td>
                    <td>
                        <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td><?= ucfirst($order['payment_status']) ?></td>
                    <td>
                        <?php if (!empty($order['payment_proof'])): ?>
                            <a href="../assets/uploads/payments/<?= htmlspecialchars($order['payment_proof']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            Not uploaded
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($order['payment_status'] == 'unpaid' && $order['status'] != 'cancelled'): ?>
                            <a href="uploadPayment.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">Upload Payment</a>
                        <?php endif; ?>
                     </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include '../Customize&Database/footer.php'; ?>