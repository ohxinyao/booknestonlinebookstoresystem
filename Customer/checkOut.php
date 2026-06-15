<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

if (empty($_SESSION['cart'])) {
    header("Location: shoppingCart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_checkout'])) {
    $selectedBooks = $_POST['selected_books'] ?? [];
    $selectedBooks = array_unique(array_map('intval', $selectedBooks));
    $cartIds = array_map('intval', array_keys($_SESSION['cart']));
    $selectedBooks = array_values(array_intersect($selectedBooks, $cartIds));

    if (empty($selectedBooks)) {
        $_SESSION['checkout_error'] = 'Please select at least one book to checkout.';
        header("Location: shoppingCart.php");
        exit;
    }

    $_SESSION['checkout_book_ids'] = $selectedBooks;
    unset($_SESSION['temp_voucher']);
    header("Location: checkOut.php");
    exit;
}

if (empty($_SESSION['checkout_book_ids'])) {
    $_SESSION['checkout_error'] = 'Please select the books you want to checkout first.';
    header("Location: shoppingCart.php");
    exit;
}

$checkoutIds = array_values(array_intersect(
    array_map('intval', $_SESSION['checkout_book_ids']),
    array_map('intval', array_keys($_SESSION['cart']))
));

if (empty($checkoutIds)) {
    unset($_SESSION['checkout_book_ids'], $_SESSION['temp_voucher']);
    $_SESSION['checkout_error'] = 'The selected books are no longer in your cart.';
    header("Location: shoppingCart.php");
    exit;
}

$_SESSION['checkout_book_ids'] = $checkoutIds;

$cartItems = [];
$total = 0;
$placeholders = implode(',', array_fill(0, count($checkoutIds), '?'));
$stmt = $pdo->prepare("SELECT * FROM books WHERE id IN ($placeholders)");
$stmt->execute($checkoutIds);
$books = $stmt->fetchAll();

foreach ($books as $book) {
    $qty = max(1, min((int)$_SESSION['cart'][$book['id']], (int)$book['stock']));
    $subtotal = $book['price'] * $qty;
    $cartItems[] = ['book' => $book, 'qty' => $qty, 'subtotal' => $subtotal];
    $total += $subtotal;
}

if (isset($_GET['check_voucher'])) {
    header('Content-Type: application/json');
    $code = trim($_GET['code']);
    $code = strtoupper($code);
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
        $response['message'] = "Minimum order amount of RM " . number_format($voucher['min_order_amount'], 2) . " required.";
    } else {
        $validConditions = true;
        $conditionMessage = '';
        
        $$conditions = !empty($voucher['conditions']) ? json_decode($voucher['conditions'], true) : [];
 
        if ($validConditions && isset($conditions['new_member_only']) && $conditions['new_member_only'] === true) {
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
            $hasValidCategory = false;
            
            foreach ($cartItems as $item) {
                $bookCategory = $item['book']['category'];
                
                if (in_array($bookCategory, $allowedCategories)) {
                    $hasValidCategory = true;
                    break; 
                }
            }
            
            if (!$hasValidCategory) {
                $validConditions = false;
                $categoryList = implode(', ', $allowedCategories);
                $conditionMessage = "This voucher requires at least one book in categories: $categoryList. Your cart does not contain any eligible books.";
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
        
        if ($voucher['discount_type'] == 'percentage' && isset($voucher['max_discount']) && $voucher['max_discount'] > 0) {
            $discount = min($discount, $voucher['max_discount']);
        }

        $response['valid'] = true;
        $response['discount'] = $discount;
        $response['new_total'] = $total - $discount;
        $response['message'] = 'Voucher applied! You saved RM ' . number_format($discount, 2);

        $_SESSION['temp_voucher'] = ['code' => $voucher['code'], 'discount' => $discount];
    }

    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
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

    $finalTotal = max(0, $total - $discount);

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
            unset($_SESSION['cart'][$item['book']['id']]);
        }

        $pdo->commit();
        unset($_SESSION['checkout_book_ids']);

        if (isset($_SESSION['user_id'])) {
            $deleteCartItem = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ? AND book_id = ?");
            foreach ($cartItems as $item) {
                $deleteCartItem->execute([$_SESSION['user_id'], $item['book']['id']]);
            }
        }

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
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
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
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
    }

    .payment-card .card-header,
    .voucher-card .card-header {
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
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }

    .btn-checkout {
        background: linear-gradient(135deg, #2c3e50, #1a2632);
        border: none;
        padding: 0.8rem;
        font-weight: 600;
        font-size: 1.1rem;
        border-radius: 50px;
        color: white;
    }

    .btn-checkout:hover {
        background: linear-gradient(135deg, #3e5a6f, #2a3a4a);
        color: white;
    }

    .back-to-cart {
        margin-bottom: 1rem;
    }
</style>

<div class="container checkout-container">
    <div class="back-to-cart">
        <a href="shoppingCart.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Cart
        </a>
    </div>

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
                                    <td><?= htmlspecialchars($item['book']['title']) ?> (<?= htmlspecialchars($item['book']['category']) ?>)</div>
                                    <td class="text-center"><?= $item['qty'] ?></td>
                                    <td class="text-end">RM <?= number_format($item['book']['price'], 2) ?></td>
                                    <td class="text-end">RM <?= number_format($item['subtotal'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>

                            <tfoot>
                                <tr id="subtotal-row">
                                    <th colspan="3" class="text-end">Subtotal</th>
                                    <th class="text-end" id="subtotal-amount">RM <?= number_format($total, 2) ?></th>
                                </tr>

                                <tr id="discount-row" style="display: none;">
                                    <th colspan="3" class="text-end text-success">Discount</th>
                                    <th class="text-end text-success" id="discount-amount">- RM 0.00</th>
                                </tr>

                                <tr class="total-row">
                                    <th colspan="3" class="text-end">Final Total</th>
                                    <th class="text-end" id="cart-total">RM <?= number_format($total, 2) ?></th>
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
                        <p><strong>Final Total:</strong> RM <span id="final_total"><?= number_format($total, 2) ?></span></p>
                    </div>
                </div>
            </div>

            <div class="payment-card card">
                <div class="card-header">
                    <i class="fas fa-credit-card me-2"></i> Payment Method
                </div>

                <div class="card-body">
                    <form method="POST" id="checkout_form">
                        <input type="hidden" name="place_order" value="1">

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
document.getElementById('voucher_code').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

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
                document.getElementById('cart-total').innerText = 'RM <?= number_format($total, 2) ?>';
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