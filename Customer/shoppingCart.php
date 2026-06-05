<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove'])) {
    $removeId = $_POST['remove'];
    unset($_SESSION['cart'][$removeId]);
    
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$_SESSION['user_id'], $removeId]);
    }
    
    header("Location: shoppingCart.php");
    exit;
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$checkoutError = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);

$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $cartIds = array_map('intval', array_keys($_SESSION['cart']));
    $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id IN ($placeholders)");
    $stmt->execute($cartIds);
    $books = $stmt->fetchAll();

    foreach ($books as $book) {
        $qty = max(1, min((int)$_SESSION['cart'][$book['id']], (int)$book['stock']));
        $subtotal = $book['price'] * $qty;
        $cartItems[] = ['book' => $book, 'qty' => $qty, 'subtotal' => $subtotal];
        $total += $subtotal;
    }
}

include '../Customize&Database/header.php';
?>

<div class="d-flex flex-column align-items-start mb-3">
    <a href="selectBook.php" class="btn btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left"></i> Continue Shopping
    </a>
    <h2 class="mb-0">Shopping Cart</h2>
</div>

<?php if (!empty($checkoutError)): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($checkoutError) ?></div>
<?php endif; ?>

<?php if (empty($cartItems)): ?>
    <div class="alert alert-info">Your cart is empty. <a href="index.php">Start shopping</a></div>
<?php else: ?>
    <form method="POST" action="checkOut.php" id="checkout-selection-form">
        <input type="hidden" name="start_checkout" value="1">
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th style="width: 50px;">
                        <input type="checkbox" class="form-check-input" id="select-all-cart-items">
                    </th>
                    <th>Book</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td class="text-center align-middle">
                        <input
                            type="checkbox"
                            name="selected_books[]"
                            value="<?= $item['book']['id'] ?>"
                            class="form-check-input cart-select-item"
                            data-subtotal="<?= number_format($item['subtotal'], 2, '.', '') ?>"
                            form="checkout-selection-form"
                        >
                    </td>

                    <td><?= htmlspecialchars($item['book']['title']) ?></td>
                    <td>RM <?= number_format($item['book']['price'], 2) ?></td>

                    <td>
                        <form method="POST" action="updateCart.php" class="d-inline update-quantity-form">
                            <input type="hidden" name="book_id" value="<?= $item['book']['id'] ?>">

                            <div class="input-group" style="width: 150px;">
                                <button type="submit" name="action" value="decrement" class="btn btn-outline-secondary btn-sm">-</button>

                                <input
                                    type="number"
                                    name="quantity"
                                    value="<?= $item['qty'] ?>"
                                    min="1"
                                    max="<?= $item['book']['stock'] ?>"
                                    class="form-control text-center cart-qty-input"
                                    style="width: 60px;"
                                    onchange="this.form.submit()"
                                >

                                <button type="submit" name="action" value="increment" class="btn btn-outline-secondary btn-sm">+</button>
                            </div>
                        </form>
                    </td>

                    <td>RM <?= number_format($item['subtotal'], 2) ?></td>

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
                <tr>
                    <th colspan="4" class="text-end">Cart Total:</th>
                    <th>RM <?= number_format($total, 2) ?></th>
                    <td></td>
                </tr>
                <tr>
                    <th colspan="4" class="text-end">Selected Total:</th>
                    <th id="selected-total">RM 0.00</th>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <button type="submit" form="checkout-selection-form" class="btn btn-success">Check Out Selected</button>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all-cart-items');
    const itemCheckboxes = document.querySelectorAll('.cart-select-item');
    const selectedTotal = document.getElementById('selected-total');
    const checkoutForm = document.getElementById('checkout-selection-form');
    const selectedStorageKey = 'booknest_selected_cart_books';

    function getSelectedBookIds() {
        try {
            return JSON.parse(localStorage.getItem(selectedStorageKey)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveSelectedBookIds() {
        const selectedBookIds = Array.from(itemCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        localStorage.setItem(selectedStorageKey, JSON.stringify(selectedBookIds));
    }

    function restoreSelectedBookIds() {
        const selectedBookIds = getSelectedBookIds();

        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = selectedBookIds.includes(checkbox.value);
        });
    }

    function updateSelectedTotal() {
        let total = 0;
        let selectedCount = 0;

        itemCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                total += parseFloat(checkbox.dataset.subtotal || '0');
                selectedCount++;
            }
        });

        selectedTotal.textContent = 'RM ' + total.toFixed(2);

        if (selectAll) {
            selectAll.checked = selectedCount > 0 && selectedCount == itemCheckboxes.length;
        }

        saveSelectedBookIds();
    }

    restoreSelectedBookIds();
    updateSelectedTotal();

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateSelectedTotal();
        });
    }

    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedTotal);
    });

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const hasSelectedItem = Array.from(itemCheckboxes).some(checkbox => checkbox.checked);
            if (!hasSelectedItem) {
                e.preventDefault();
                alert('Please select at least one book to checkout.');
            }
        });
    }
});
</script>

<?php include '../Customize&Database/footer.php'; ?>