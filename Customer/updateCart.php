<?php
session_start();
require_once '../Customize&Database/setDatabase.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = (int)($_POST['book_id'] ?? 0);
    $new_qty = (int)($_POST['quantity'] ?? 0);
    $action = $_POST['action'] ?? 'update';

    if ($book_id > 0 && isset($_SESSION['cart'][$book_id])) {
        if ($action == 'increment') {
            $new_qty = (int)$_SESSION['cart'][$book_id] + 1;
        } elseif ($action == 'decrement') {
            $new_qty = (int)$_SESSION['cart'][$book_id] - 1;
        }

        if ($new_qty > 0) {
            $stmt = $pdo->prepare("SELECT stock FROM books WHERE id = ?");
            $stmt->execute([$book_id]);
            $stock = (int)$stmt->fetchColumn();

            if ($stock > 0) {
                $new_qty = min($new_qty, $stock);
            }

            $_SESSION['cart'][$book_id] = $new_qty;
        } else {
            unset($_SESSION['cart'][$book_id]);
        }

        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];

            if ($new_qty > 0) {
                $stmt = $pdo->prepare("INSERT INTO user_cart (user_id, book_id, quantity) VALUES (?, ?, ?) 
                                        ON DUPLICATE KEY UPDATE quantity = ?");
                $stmt->execute([$userId, $book_id, $new_qty, $new_qty]);
            } else {
                $stmt = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ? AND book_id = ?");
                $stmt->execute([$userId, $book_id]);
            }
        }
    }
}

header("Location: shoppingCart.php");
exit;