<?php
session_start();
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}
$book_id = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = (int)$_POST['book_id'];
    if (!in_array($book_id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $book_id;
    }
}
header("Location: bookDetail.php?id=" . $book_id . "&wishlist_success=1");
exit;