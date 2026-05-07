<?php
session_start();
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if (isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = array_diff($_SESSION['wishlist'], [$id]);
    }
}
header("Location: wishList.php");
exit;