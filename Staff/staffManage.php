<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff'); 
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$order_status = isset($_GET['order_status']) ? trim($_GET['order_status']) : '';
$payment_status = isset($_GET['payment_status']) ? trim($_GET['payment_status']) : '';
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

$sql = "SELECT o.*, u.name as customer_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE 1=1";
$params = [];

if (!empty($order_status)) {
    $sql .= " AND o.status = ?";
    $params[] = $order_status;
}
if (!empty($payment_status)) {
    $sql .= " AND o.payment_status = ?";
    $params[] = $payment_status;
}
if (!empty($date_from)) {
    $sql .= " AND DATE(o.order_date) >= ?";
    $params[] = $date_from;
}
if (!empty($date_to)) {
    $sql .= " AND DATE(o.order_date) <= ?";
    $params[] = $date_to;
}
$sql .= " ORDER BY o.order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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

<style>
    .table td,
    .table th {
        text-align: center !important;
        vertical-align: middle !important;
    }
    
    .filter-form .form-select,
    .filter-form select {
        min-width: 180px !important;
        width: auto !important;
        padding-right: 2rem !important;
    }
    
    .filter-form input[type="date"] {
        min-width: 150px !important;
    }
    
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 120px;
        min-width: 120px;
    }
    
    .action-buttons .form-select,
    .action-buttons select {
        width: 100% !important;
        min-width: 110px !important;
        padding: 0.3rem 1.5rem 0.3rem 0.5rem !important;
        font-size: 0.7rem !important;
    }
    
    .action-buttons .btn-sm,
    .action-buttons .btn,
    .action-buttons form .btn-sm {
        width: 100% !important;
        padding: 0.35rem 0 !important;
        font-size: 0.7rem !important;
        text-align: center !important;
        display: block !important;
        margin: 0 !important;
    }
    
    .action-buttons form {
        width: 100% !important;
        margin: 0 !important;
    }
    
    .badge-status {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 30px;
    }
    
    @media (max-width: 768px) {
        .filter-form .col-auto {
            width: 100%;
        }
        .filter-form select,
        .filter-form input,
        .filter-form button {
            width: 100%;
        }
        .action-buttons {
            width: 100%;
        }
    }
</style>

<h2>Manage Orders</h2>

<form method="GET" class="row g-2 mb-3 align-items-end filter-form">
    <div class="col-auto">
        <label class="form-label small">Order Status</label>
        <select name="order_status" class="form-select form-select-sm">
            <option value="">All Order Status</option>
            <option value="pending" <?= $order_status == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processing" <?= $order_status == 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="shipped" <?= $order_status == 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="completed" <?= $order_status == 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= $order_status == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </div>
    <div class="col-auto">
        <label class="form-label small">Payment Status</label>
        <select name="payment_status" class="form-select form-select-sm">
            <option value="">All Payment Status</option>
            <option value="unpaid" <?= $payment_status == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
            <option value="paid" <?= $payment_status == 'paid' ? 'selected' : '' ?>>Paid</option>
        </select>
    </div>
    <div class="col-auto">
        <label class="form-label small">From Date</label>
        <input type="date" name="date_from" class="form-control form-control-sm" value="<?= htmlspecialchars($date_from) ?>">
    </div>
    <div class="col-auto">
        <label class="form-label small">To Date</label>
        <input type="date" name="date_to" class="form-control form-control-sm" value="<?= htmlspecialchars($date_to) ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        <a href="staffManage.php" class="btn btn-sm btn-secondary">Reset</a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date & Time</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Final Total</th>
                <th>Voucher</th>
                <th>Order Status</th>
                <th>Shipped Date</th>
                <th>Payment Status</th>
                <th>Proof</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($orders) == 0): ?>
            <tr><td colspan="12">No orders found from the table. Sedative
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
                        <span class="badge badge-status bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?= $order['shipped_date'] ? date('d M Y', strtotime($order['shipped_date'])) : '-' ?>
                    </td>
                    <td>
                        <span class="badge badge-status bg-<?= $order['payment_status'] == 'paid' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($order['payment_status']) ?>
                        </span>
                     </div>
                    </td>
                    <td>
                        <?php if ($order['payment_proof']): ?>
                            <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#paymentProofModal" onclick="loadPaymentProof(<?= (int)$order['id'] ?>)">
                                View
                            </button>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                     </div>
                    </td>
                    <td class="action-buttons">
                        <form method="POST" action="updateOrder.php">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-select form-select-sm">
                                <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-primary w-100">Update</button>
                        </form>

                        <?php if ($order['payment_status'] == 'paid' && $order['status'] != 'completed' && $order['status'] != 'cancelled' && $order['status'] != 'shipped'): ?>
                            <button type="button" class="btn btn-sm btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal" 
                                    data-order-id="<?= $order['id'] ?>" data-order-number="<?= htmlspecialchars($order['order_number']) ?>">
                                Cancel Order
                            </button>
                        <?php endif; ?>
                     </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="paymentProofModal" tabindex="-1" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentProofModalLabel">Payment Proof</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="paymentProofContent">
                <div class="text-center">Loading proof...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="../Admin/cancelOrderAdmin.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="cancel_order_id" value="">
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Cancellation Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" id="cancel_reason" rows="3" required></textarea>
                        <div class="form-text">This reason will be sent to the customer via email and notification.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var cancelModal = document.getElementById('cancelModal');
    cancelModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var orderId = button.getAttribute('data-order-id');
        var orderNumber = button.getAttribute('data-order-number');
        var inputId = cancelModal.querySelector('#cancel_order_id');
        var modalTitle = cancelModal.querySelector('.modal-title');
        inputId.value = orderId;
        modalTitle.textContent = 'Cancel Order #' + orderNumber;
    });

    function loadPaymentProof(orderId) {
        const modalBody = document.getElementById('paymentProofContent');
        modalBody.innerHTML = '<div class="text-center">Loading proof...</div>';

        fetch('../Customer/getPaymentProof.php?order_id=' + orderId)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.file) {
                    const imgHtml = `
                        <div class="text-center">
                            <p><strong>Order Number:</strong> ${escapeHtml(data.order_number)}</p>
                            <img src="${escapeHtml(data.file)}" class="img-fluid rounded border" alt="Payment Proof" style="max-height: 500px;">
                            <div class="mt-3">
                                <a href="${escapeHtml(data.file)}" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> Open in new tab
                                </a>
                            </div>
                        </div>
                    `;
                    modalBody.innerHTML = imgHtml;
                } else {
                    modalBody.innerHTML = '<div class="alert alert-warning">Payment proof is not available.</div>';
                }
            })
            .catch(error => {
                console.error('Error fetching payment proof:', error);
                modalBody.innerHTML = '<div class="alert alert-danger">Failed to load payment proof.</div>';
            });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
</script>

<?php include '../Customize&Database/footer.php'; ?>