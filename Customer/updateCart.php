<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'] ?? 0;
    $new_qty = (int)($_POST['quantity'] ?? 0);
    if ($book_id > 0 && isset($_SESSION['cart'][$book_id])) {
        if ($new_qty > 0) {
            $_SESSION['cart'][$book_id] = $new_qty;
        } else {
            unset($_SESSION['cart'][$book_id]);
        }
    }
}
header("Location:shoppingCart.php");
exit;