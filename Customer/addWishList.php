<?php
session_start();
require_once '../Customize&Database/access.php';
requireLogin(); 
require_once '../Customize&Database/setDatabase.php';

$book_id = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = (int)$_POST['book_id'];
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO user_wishlist (user_id, book_id) VALUES (?, ?)");
    $stmt->execute([$userId, $book_id]);
}
header("Location: bookDetail.php?id=" . $book_id . "&wishlist_success=1");
exit;