<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, order_number, total_amount, discount_amount, voucher_code, status, payment_status, payment_proof, order_date, shipped_date 
                       FROM orders 
                       WHERE user_id = ? 
                       ORDER BY order_date DESC");
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

<div class="mb-3">
    <a href="selectBook.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Continue Shopping
    </a>
</div>

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
                    <th>Shipped Date</th>
                    <th>Payment</th>
                    <th>Proof</th>
                    <th style="min-width: 120px;">Action</th>
                </td>
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
                    <td>
                        <?= $order['shipped_date'] ? date('d M Y', strtotime($order['shipped_date'])) : '-' ?>
                    </td>
                    <td><?= ucfirst($order['payment_status']) ?></td>
                    <td>
                        <?php if (!empty($order['payment_proof'])): ?>
                            <a href="../assets/uploads/payments/<?= htmlspecialchars($order['payment_proof']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            Not uploaded
                        <?php endif; ?>
                    </div>
                    </td>
                    <td class="align-middle">
                        <div class="d-flex flex-column gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#orderItemsModal" onclick="loadOrderItems(<?= $order['id'] ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>

                            <?php if ($order['payment_status'] == 'unpaid' && $order['status'] != 'cancelled'): ?>
                                <a href="uploadPayment.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fas fa-credit-card"></i> Pay
                                </a>
                            <?php endif; ?>
            
                            <?php if ($order['status'] == 'pending' && $order['payment_status'] == 'unpaid'): ?>
                                <a href="cancelOrder.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Cancel this order?')">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<div class="modal fade" id="orderItemsModal" tabindex="-1" aria-labelledby="orderItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderItemsModalLabel">Order Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr><th>Book Title</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody id="orderItemsList">
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th colspan="3" class="text-end">Total Amount:</th>
                                <th id="modalTotalAmount">-</th>
                             </tr>
                        </tfoot>
                     </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function loadOrderItems(orderId) {
    const tbody = document.getElementById('orderItemsList');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
    document.getElementById('modalTotalAmount').innerText = '-';
    fetch('getOrderItem.php?order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '';
                data.items.forEach(item => {
                    html += `
                        <tr>
                            <td>${escapeHtml(item.title)}</td>
                            <td class="text-center">${item.quantity}</td>
                            <td class="text-end">RM ${item.price}</td>
                            <td class="text-end">RM ${item.subtotal}</td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
                document.getElementById('modalTotalAmount').innerText = 'RM ' + data.total;
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load items.</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading items.</td></tr>';
        });
}

function escapeHtml(str) {
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}
</script>

<style>
    .btn-sm {
        border-radius: 30px;
        padding: 0.25rem 0.8rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .btn-outline-secondary:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }
    .btn-outline-primary {
        color: #b85c38;
        border-color: #b85c38;
    }
    .btn-outline-primary:hover {
        background-color: #b85c38;
        border-color: #b85c38;
        color: white;
        transform: translateY(-1px);
    }
    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    .d-flex.flex-column {
        gap: 0.5rem;
    }
    .w-100 {
        width: 100%;
    }
    .table td {
        vertical-align: middle;
    }
</style>

<?php include '../Customize&Database/footer.php'; ?>