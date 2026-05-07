<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
include '../Customize&Database/header.php';
?>
<h2>My Orders</h2>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Order placed successfully! Please upload payment proof.</div>
<?php endif; ?>
<?php if (count($orders) == 0): ?>
    <div class="alert alert-info">You haven't placed any orders yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th>Payment</th><th>Proof</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_number']) ?></td>
                    <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                    <td>RM <?= number_format($order['total_amount'],2) ?></td>
                    <td><span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>"><?= ucfirst($order['status']) ?></span></td>
                    <td><?= ucfirst($order['payment_status']) ?></td>
                    <td>
                        <?php if ($order['payment_proof']): ?>
                            <a href="../assets/uploads/payments/<?= $order['payment_proof'] ?>" target="_blank">View</a>
                        <?php else: ?>
                            Not uploaded
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($order['payment_status'] == 'unpaid' && $order['status'] != 'cancelled'): ?>
                            <a href="upload_payment.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">Upload Payment</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include '../Customize&Database/footer.php'; ?>