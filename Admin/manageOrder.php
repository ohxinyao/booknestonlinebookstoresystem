<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('admin');
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success'>{$_SESSION['flash_success']}</div>";
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    echo "<div class='alert alert-danger'>{$_SESSION['flash_error']}</div>";
    unset($_SESSION['flash_error']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status'];
    $allowed = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
    if (!in_array($newStatus, $allowed)) {
        $_SESSION['flash_error'] = "Invalid status value.";
        header("Location: manageOrder.php");
        exit;
    }

    $pdo->beginTransaction();
    try {
        $orderInfo = $pdo->prepare("SELECT * FROM orders WHERE id = ? FOR UPDATE");
        $orderInfo->execute([$orderId]);
        $order = $orderInfo->fetch();
        if (!$order) {
            throw new Exception("Order not found.");
        }

        $payment_status = $order['payment_status'];
        if ($payment_status !== 'paid' && in_array($newStatus, ['processing', 'shipped', 'completed'])) {
            throw new Exception("Cannot change status to '$newStatus' because order is unpaid. Customer must upload payment proof first.");
        }
        if ($payment_status === 'paid' && $newStatus === 'pending') {
            throw new Exception("Order already paid, cannot revert to pending.");
        }

        $shipped_date = null;
        if ($newStatus == 'shipped' && is_null($order['shipped_date'])) {
            $shipped_date = date('Y-m-d H:i:s');
            $updateStmt = $pdo->prepare("UPDATE orders SET status = ?, shipped_date = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $shipped_date, $orderId]);
        } else {
            $updateStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $orderId]);
        }

        $notifMsg = "Your order #{$order['order_number']} status is now {$newStatus}.";
        if ($newStatus == 'shipped' && $shipped_date) {
            $notifMsg .= " It was shipped on " . date('d M Y', strtotime($shipped_date)) . ".";
        }

        $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
        $notifStmt->execute([$order['user_id'], $orderId, $notifMsg]);

        $userStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
        $userStmt->execute([$order['user_id']]);
        $user = $userStmt->fetch();

        if ($user) {
            $subject = "Your order #{$order['order_number']} status updated to {$newStatus}";
            $body = "Dear {$user['name']},<br><br>Your order status has been updated to: <strong>{$newStatus}</strong>.<br>";
            if ($newStatus == 'shipped' && $shipped_date) {
                $body .= "Your books have been shipped on " . date('d M Y H:i', strtotime($shipped_date)) . ".<br>";
            }
            $body .= "You can track your order in your account.<br><br>Thank you for shopping with BookNest.";
            sendEmail($user['email'], $subject, $body);
        }

        $pdo->commit();
        $_SESSION['flash_success'] = "Order status updated successfully. Customer has been notified.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = "Update failed: " . $e->getMessage();
    }
    header("Location: manageOrder.php");
    exit;
}

$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

$sql = "SELECT o.*, u.name as user_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE 1=1";
$params = [];

if (!empty($status_filter)) {
    $sql .= " AND o.status = ?";
    $params[] = $status_filter;
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
?>

<h2>Manage Orders</h2>
<form method="GET" class="row g-2 mb-3 align-items-end">
    <div class="col-auto">
        <label class="form-label small">Order Status</label>
        <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="paid" <?= $status_filter == 'paid' ? 'selected' : '' ?>>Paid</option>
            <option value="processing" <?= $status_filter == 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="shipped" <?= $status_filter == 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="completed" <?= $status_filter == 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
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
        <a href="manageOrder.php" class="btn btn-sm btn-secondary">Reset</a>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Final Total</th>
                <th>Voucher</th>
                <th>Payment Status</th>
                <th>Order Status</th>
                <th>Proof</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($orders) == 0): ?>
            <tr><td colspan="11">No orders found for the selected criteria. Sedative
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $discount = $order['discount_amount'] ?? 0;
                $subtotal = $order['total_amount'] + $discount;
            ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_number']) ?></td>
                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                    <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                    <td>RM <?= number_format($subtotal, 2) ?></td>
                    <td><?= $discount > 0 ? 'RM ' . number_format($discount, 2) : '-' ?></td>
                    <td><strong>RM <?= number_format($order['total_amount'], 2) ?></strong></td>
                    <td><?= htmlspecialchars($order['voucher_code'] ?? '-') ?></td>
                    <td><?= ucfirst($order['payment_status']) ?></td>
                    <td><?= ucfirst($order['status']) ?></td>
                    <td>
                        <?php if ($order['payment_proof']): ?>
                            <a href="../assets/uploads/payments/<?= $order['payment_proof'] ?>" target="_blank">View</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" class="d-inline-block me-1">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                                <option value="pending" <?= $order['status']=='pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="paid" <?= $order['status']=='paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="processing" <?= $order['status']=='processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $order['status']=='shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="completed" <?= $order['status']=='completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $order['status']=='cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                        </form>

                        <?php if (strtolower($order['payment_status']) == 'paid' && !in_array(strtolower($order['status']), ['completed','cancelled','shipped'])): ?>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal" 
                                data-order-id="<?= $order['id'] ?>" data-order-number="<?= htmlspecialchars($order['order_number']) ?>">
                                Cancel Order
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="cancelOrderAdmin.php">
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
        var modalTitle = cancelModal.querySelector('.modal-title');
        var inputOrderId = cancelModal.querySelector('#cancel_order_id');
        modalTitle.textContent = 'Cancel Order #' + orderNumber;
        inputOrderId.value = orderId;
    });
</script>

<?php include '../Customize&Database/footer.php'; ?>