<?php
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
    
    $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE code = ? AND active = 1 AND (valid_to IS NULL OR valid_to > NOW()) AND (usage_limit IS NULL OR used_count < usage_limit)");
    $stmt->execute([$code]);
    $voucher = $stmt->fetch();
    
    if (!$voucher) {
        $response['message'] = 'Invalid or expired voucher code.';
    } elseif ($total < $voucher['min_order_amount']) {
        $response['message'] = "Minimum order amount of RM " . number_format($voucher['min_order_amount'],2) . " required.";
    } else {
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
<div class="container">
    <h2>Checkout</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <div class="row">
        <div class="col-md-8">
            <h4>Order Summary</h4>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Book</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['book']['title']) ?></td>
                        <td><?= $item['qty'] ?></td>
                        <td>RM <?= number_format($item['book']['price'],2) ?></td>
                        <td>RM <?= number_format($item['subtotal'],2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr id="subtotal-row">
                        <th colspan="3" class="text-end">Subtotal:</th>
                        <th id="subtotal-amount">RM <?= number_format($total,2) ?></th>
                    </tr>
                    
                    <tr id="discount-row" style="display: none;">
                        <th colspan="3" class="text-end">Discount:</th>
                        <th id="discount-amount">- RM 0.00</th>
                    </tr>

                    <tr id="cart-total-row">
                        <th colspan="3" class="text-end">Final Total:</th>
                        <th id="cart-total">RM <?= number_format($total,2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">Voucher Code</div>
                <div class="card-body">
                    <div class="input-group mb-2">
                        <input type="text" id="voucher_code" class="form-control" placeholder="Enter voucher code">
                        <button class="btn btn-outline-secondary" type="button" id="apply_voucher">Apply</button>
                    </div>
                    <div id="voucher_message"></div>
                    <div id="discount_info" style="display: none;">
                        <hr>
                        <p><strong>Discount:</strong> -<span id="discount_amount">0.00</span></p>
                        <p><strong>Final Total:</strong> RM <span id="final_total"><?= number_format($total,2) ?></span></p>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header bg-success text-white">Payment Method</div>
                <div class="card-body">
                    <p>After placing order, please upload payment proof from your order history.</p>
                    <form method="POST" id="checkout_form">
                        <button type="submit" class="btn btn-success w-100">Place Order</button>
                    </form>
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
            document.getElementById('voucher_message').innerHTML = '<div class="alert alert-danger">An error occurred.</div>';
        });
});
</script>
<?php include '../Customize&Database/footer.php'; ?>