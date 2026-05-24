<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff'); 
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$stmt = $pdo->query("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll();

if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success'>{$_SESSION['flash_success']}</div>";
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    echo "<div class='alert alert-danger'>{$_SESSION['flash_error']}</div>";
    unset($_SESSION['flash_error']);
}
?>

<h2>Incoming Orders</h2>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Final Total</th>
                <th>Voucher</th>
                <th>Status</th>
                <th>Shipped Date</th>
                <th>Payment</th>
                <th>Proof</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($orders) == 0): ?>
            <tr><td colspan="12">No orders found.</td></td>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $discount = $order['discount_amount'] ?? 0;
                $originalTotal = $order['total_amount'] + $discount;
            ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_number']) ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
                    <td>RM <?= number_format($originalTotal, 2) ?></td>
                    <td><?= $discount > 0 ? 'RM ' . number_format($discount, 2) : '-' ?></td>
                    <td><strong>RM <?= number_format($order['total_amount'], 2) ?></strong></td>
                    <td><?= htmlspecialchars($order['voucher_code'] ?? '-') ?></td>
                    <td>
                        <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?= $order['shipped_date'] ? date('d M Y', strtotime($order['shipped_date'])) : '-' ?>
                    </td>
                    <td><?= ucfirst($order['payment_status']) ?></td>
                    <td>
                        <?php if ($order['payment_proof']): ?>
                            <a href="../assets/uploads/payments/<?= htmlspecialchars($order['payment_proof']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" action="updateOrder.php" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="paid" <?= $order['status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                        
                        <?php if ($order['payment_status'] == 'paid' && $order['status'] != 'completed' && $order['status'] != 'cancelled'): ?>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" 
                                    data-order-id="<?= $order['id'] ?>" data-order-number="<?= $order['order_number'] ?>">Reject Order</button>
                        <?php endif; ?>
                     </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="rejectOrder.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="reject_order_id" value="">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">Reason for rejection (e.g., wrong amount, payment not received):</label>
                        <textarea class="form-control" name="reject_reason" id="reject_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var rejectModal = document.getElementById('rejectModal');
    rejectModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var orderId = button.getAttribute('data-order-id');
        var orderNumber = button.getAttribute('data-order-number');
        var inputId = rejectModal.querySelector('#reject_order_id');
        var modalTitle = rejectModal.querySelector('.modal-title');
        inputId.value = orderId;
        modalTitle.innerText = 'Reject Order #' + orderNumber;
    });
});
</script>

<?php include '../Customize&Database/footer.php'; ?>