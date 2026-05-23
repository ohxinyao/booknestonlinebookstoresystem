<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove'])) {
    unset($_SESSION['cart'][$_POST['remove']]);
    header("Location: shoppingCart.php");
    exit;
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$cartItems = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM books WHERE id IN ($ids)");
    $books = $stmt->fetchAll();
    foreach ($books as $book) {
        $qty = $_SESSION['cart'][$book['id']];
        $subtotal = $book['price'] * $qty;
        $cartItems[] = ['book' => $book, 'qty' => $qty, 'subtotal' => $subtotal];
        $total += $subtotal;
    }
}
include '../Customize&Database/header.php';
?>
<h2>Shopping Cart</h2>
<div class="mb-3">
    <a href="selectBook.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Continue Shopping
    </a>
</div>
<?php if (empty($cartItems)): ?>
    <div class="alert alert-info">Your cart is empty. <a href="index.php">Start shopping</a></div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr><th>Book</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['book']['title']) ?></td>
                    <td>RM <?= number_format($item['book']['price'],2) ?></td>
                    <td>
                        <form method="POST" action="updateCart.php" class="d-inline update-quantity-form">
                            <input type="hidden" name="book_id" value="<?= $item['book']['id'] ?>">
                            <div class="input-group" style="width: 150px;">
                                <button type="button" class="btn btn-outline-secondary btn-sm qty-decr">-</button>
                                <input type="number" name="quantity" value="<?= $item['qty'] ?>" min="1" class="form-control text-center qty-input" style="width: 60px;">
                                <button type="button" class="btn btn-outline-secondary btn-sm qty-incr">+</button>
                            </div>
                        </form>
                    </td>
                    <td>RM <?= number_format($item['subtotal'],2) ?></td>
                    <td>
                        <form method="POST" class="d-inline" onsubmit="return confirmDelete('Remove this item?')">
                            <input type="hidden" name="remove" value="<?= $item['book']['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><th colspan="3" class="text-end">Total:</th><th>RM <?= number_format($total,2) ?></th><td></tr>
            </tfoot>
        </table>
    </div>
    <a href="checkOut.php" class="btn btn-success">Proceed to Checkout</a>
<?php endif; ?>
<?php include '../Customize&Database/footer.php'; ?>