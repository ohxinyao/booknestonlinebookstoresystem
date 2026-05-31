<?php
session_start();
require_once '../Customize&Database/access.php';
requireLogin();
require_once '../Customize&Database/setDatabase.php';

if (isset($_GET['id'])) {
    $book_id = (int)$_GET['id'];
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("DELETE FROM user_wishlist WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$userId, $book_id]);
}
header("Location: wishList.php");
exit;