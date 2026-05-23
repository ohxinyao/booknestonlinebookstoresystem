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

        $shipped_date = null;
        if ($newStatus == 'shipped' && is_null($order['shipped_date'])) {
            $shipped_date = date('Y-m-d H:i:s');
            $updateStmt = $pdo->prepare("UPDATE orders SET status = ?, shipped_date = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $shipped_date, $orderId]);
        } else {
            $updateStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $updateStmt->execute([$newStatus, $orderId]);
        }

        $userStmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
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

            $notifMsg = "Your order #{$order['order_number']} status is now {$newStatus}.";
            if ($newStatus == 'shipped' && $shipped_date) {
                $notifMsg .= " It was shipped on " . date('d M Y', strtotime($shipped_date)) . ".";
            }
            $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, order_id, message) VALUES (?, ?, ?)");
            $notifStmt->execute([$user['id'], $orderId, $notifMsg]);
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

$orders = $pdo->query("
    SELECT o.*, u.name as user_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC
")->fetchAll();
?>

<h2>Manage Orders</h2>
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
            <tr><td colspan="11">No orders found. Sedative
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

                        <?php if (strtolower($order['payment_status']) == 'paid' && !in_array(strtolower($order['status']), ['completed','cancelled'])): ?>
                            <a href="cancelOrderAdmin.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this order? Stock will be restored and customer will be notified.')">Cancel Order</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../Customize&Database/footer.php'; ?>