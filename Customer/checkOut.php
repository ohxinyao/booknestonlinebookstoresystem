<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

if (empty($_SESSION['cart'])) {
    header("Location: shoppingCart.php");
    exit;
}

$cartItems = [];
$total = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
$stmt = $pdo->query("SELECT * FROM books WHERE id IN ($ids)");
$books = $stmt->fetchAll();
foreach ($books as $book) {
    $qty = $_SESSION['cart'][$book['id']];
    $subtotal = $book['price'] * $qty;
    $cartItems[] = ['book' => $book, 'qty' => $qty, 'subtotal' => $subtotal];
    $total += $subtotal;
}

if (isset($_GET['check_voucher'])) {
    header('Content-Type: application/json');
    $code = trim($_GET['code']);
    $response = ['valid' => false, 'message' => '', 'discount' => 0, 'new_total' => $total];
    
    if (empty($code)) {
        $response['message'] = 'Please enter a voucher code.';
        echo json_encode($response);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE code = ? AND active = 1 AND (valid_to IS NULL OR valid_to > NOW()) AND (usage_limit IS NULL OR used_count < usage_limit)");
        $stmt->execute([$code]);
        $voucher = $stmt->fetch();
    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
        echo json_encode($response);
        exit;
    }
    
    if (!$voucher) {
        $response['message'] = 'Invalid or expired voucher code.';
    } elseif ($total < $voucher['min_order_amount']) {
        $response['message'] = "Minimum order amount of RM " . number_format($voucher['min_order_amount'],2) . " required.";
    } else {
        $validConditions = true;
        $conditionMessage = '';

        if ($code === 'SAVE10') {
            $validConditions = true;
        } else {
            $conditions = json_decode($voucher['conditions'], true);
            if ($conditions) {
                if (isset($conditions['new_member_only']) && $conditions['new_member_only'] === true) {
                    $userId = $_SESSION['user_id'];
                    $orderCheck = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND (payment_status = 'paid' OR status = 'completed')");
                    $orderCheck->execute([$userId]);
                    $hasOrder = $orderCheck->fetchColumn() > 0;
                    if ($hasOrder) {
                        $validConditions = false;
                        $conditionMessage = 'This voucher is for new members only. You have already placed an order.';
                    }
                }
    
                if ($validConditions && isset($conditions['categories']) && is_array($conditions['categories']) && !empty($conditions['categories'])) {
                    $allowedCategories = $conditions['categories'];
                    $cartCategories = [];
                    foreach ($cartItems as $item) {
                        $cartCategories[] = $item['book']['category'];
                    }
                    $invalidCategories = array_diff($cartCategories, $allowedCategories);
                    if (!empty($invalidCategories)) {
                        $validConditions = false;
                        $categoryList = implode(', ', $allowedCategories);
                        $conditionMessage = "This voucher only applies to books in categories: $categoryList. Your cart contains items from other categories.";
                    }
                }
            }
        }
        
        if (!$validConditions) {
            $response['message'] = $conditionMessage;
            echo json_encode($response);
            exit;
        }
        
        $discount = 0;
        if ($voucher['discount_type'] == 'percentage') {
            $discount = $total * ($voucher['discount_value'] / 100);
        } else {
            $discount = $voucher['discount_value'];
        }
        $discount = min($discount, $total);
        $response['valid'] = true;
        $response['discount'] = $discount;
        $response['new_total'] = $total - $discount;
        $response['message'] = 'Voucher applied! You saved RM ' . number_format($discount,2);
        $_SESSION['temp_voucher'] = ['code' => $code, 'discount' => $discount];
    }
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderNumber = 'ORD' . time() . rand(100, 999);
    $discount = 0;
    $voucherCode = null;
    
    if (isset($_SESSION['temp_voucher'])) {
        $discount = $_SESSION['temp_voucher']['discount'];
        $voucherCode = $_SESSION['temp_voucher']['code'];
        unset($_SESSION['temp_voucher']);
        
        if ($voucherCode) {
            $updateVoucher = $pdo->prepare("UPDATE vouchers SET used_count = used_count + 1 WHERE code = ?");
            $updateVoucher->execute([$voucherCode]);
        }
    }
    
    $finalTotal = $total - $discount;
    
    $pdo->beginTransaction();
    try {
        $orderStmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total_amount, discount_amount, voucher_code, status, payment_status) VALUES (?, ?, ?, ?, ?, 'pending', 'unpaid')");
        $orderStmt->execute([$_SESSION['user_id'], $orderNumber, $finalTotal, $discount, $voucherCode]);
        $orderId = $pdo->lastInsertId();
        
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
        $updateStock = $pdo->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
        foreach ($cartItems as $item) {
            $itemStmt->execute([$orderId, $item['book']['id'], $item['qty'], $item['book']['price']]);
            $updateStock->execute([$item['qty'], $item['book']['id']]);
        }
        
        $pdo->commit();
        unset($_SESSION['cart']);
        header("Location: orderConfirmation.php?order_number=" . $orderNumber);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Order failed: " . $e->getMessage();
    }
}

include '../Customize&Database/header.php';
?>
<style>
    .checkout-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    .order-summary-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        background: #fff;
        margin-bottom: 1.5rem;
    }
    .order-summary-card .card-header {
        background: linear-gradient(135deg, #2c3e50, #1a2632);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        border-bottom: none;
    }
    .order-summary-card .table {
        margin-bottom: 0;
    }
    .order-summary-card .table th {
        border-top: none;
        background: #f8f9fa;
    }
    .total-row {
        background: #f8f9fa;
        font-weight: bold;
    }
    .payment-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .payment-card .card-header {
        background: linear-gradient(135deg, #2c3e50, #1a2632);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
        border-bottom: none;
        border-radius: 20px 20px 0 0;
    }
    .voucher-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    .voucher-card .card-header {
        background: linear-gradient(135deg, #2c3e50, #1a2632);
        color: white;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        border-bottom: none;
        border-radius: 20px 20px 0 0;
    }
    .btn-checkout {
        background: linear-gradient(135deg, #2c3e50, #1a2632);
        border: none;
        padding: 0.8rem;
        font-weight: 600;
        font-size: 1.1rem;
        border-radius: 50px;
        transition: transform 0.2s, box-shadow 0.2s;
        color: white;
    }
    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(44,62,80,0.3);
        background: linear-gradient(135deg, #3e5a6f, #2a3a4a);
    }
    .text-muted i {
        margin-right: 5px;
    }
    @media (max-width: 768px) {
        .order-summary-card .table th, .order-summary-card .table td {
            padding: 0.5rem;
        }
    }
</style>

<div class="container checkout-container">
    <h2 class="mb-4">Checkout</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="order-summary-card card">
                <div class="card-header">
                    <i class="fas fa-shopping-cart me-2"></i> Order Summary
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['book']['title']) ?></td>
                                    <td class="text-center"><?= $item['qty'] ?></td>
                                    <td class="text-end">RM <?= number_format($item['book']['price'],2) ?></td>
                                    <td class="text-end">RM <?= number_format($item['subtotal'],2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr id="subtotal-row">
                                    <th colspan="3" class="text-end">Subtotal</th>
                                    <th class="text-end" id="subtotal-amount">RM <?= number_format($total,2) ?></th>
                                </tr>
                                <tr id="discount-row" style="display: none;">
                                    <th colspan="3" class="text-end text-success">Discount</th>
                                    <th class="text-end text-success" id="discount-amount">- RM 0.00</th>
                                </tr>
                                <tr class="total-row">
                                    <th colspan="3" class="text-end">Final Total</th>
                                    <th class="text-end" id="cart-total">RM <?= number_format($total,2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="voucher-card card">
                <div class="card-header">
                    <i class="fas fa-ticket-alt me-2"></i> Voucher Code
                </div>
                <div class="card-body">
                    <div class="input-group mb-2">
                        <input type="text" id="voucher_code" class="form-control" placeholder="Enter voucher code">
                        <button class="btn btn-outline-primary" type="button" id="apply_voucher">Apply</button>
                    </div>
                    <div id="voucher_message"></div>
                    <div id="discount_info" style="display: none;">
                        <hr>
                        <p><strong>Discount:</strong> -<span id="discount_amount">0.00</span></p>
                        <p><strong>Final Total:</strong> RM <span id="final_total"><?= number_format($total,2) ?></span></p>
                    </div>
                </div>
            </div>
            
            <div class="payment-card card">
                <div class="card-header">
                    <i class="fas fa-credit-card me-2"></i> Payment Method
                </div>
                <div class="card-body">
                    <form method="POST" id="checkout_form">
                        <p class="mb-3">After placing order, please upload payment proof from your order history.</p>
                        <button type="submit" class="btn btn-checkout w-100">
                            <i class="fas fa-lock me-2"></i> Place Order
                        </button>
                    </form>
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> You will need to upload your bank transfer receipt after ordering.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('apply_voucher').addEventListener('click', function() {
    let code = document.getElementById('voucher_code').value.trim();
    if (code === '') {
        document.getElementById('voucher_message').innerHTML = '<div class="alert alert-warning">Please enter a code.</div>';
        return;
    }
    fetch('checkOut.php?check_voucher=1&code=' + encodeURIComponent(code))
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                document.getElementById('voucher_message').innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                document.getElementById('discount-row').style.display = '';
                document.getElementById('discount-amount').innerText = '- RM ' + data.discount.toFixed(2);
                document.getElementById('cart-total').innerText = 'RM ' + data.new_total.toFixed(2);
                document.getElementById('discount_info').style.display = 'block';
                document.getElementById('discount_amount').innerText = data.discount.toFixed(2);
                document.getElementById('final_total').innerText = data.new_total.toFixed(2);
            } else {
                document.getElementById('voucher_message').innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                document.getElementById('discount-row').style.display = 'none';
                document.getElementById('cart-total').innerText = 'RM <?= number_format($total,2) ?>';
                document.getElementById('discount_info').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errMsg = 'An error occurred. Please try again.';
            if (error.message) errMsg += ' (' + error.message + ')';
            document.getElementById('voucher_message').innerHTML = '<div class="alert alert-danger">' + errMsg + '</div>';
        });
});
</script>

<?php include '../Customize&Database/footer.php'; ?>