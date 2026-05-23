<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
include '../Customize&Database/header.php';

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

$wishlistBooks = [];
if (!empty($_SESSION['wishlist'])) {
    $ids = implode(',', $_SESSION['wishlist']);
    $stmt = $pdo->query("SELECT * FROM books WHERE id IN ($ids)");
    $wishlistBooks = $stmt->fetchAll();
}
?>
<h2>My Wishlist</h2>
<?php if (empty($wishlistBooks)): ?>
    <div class="alert alert-info">Your wishlist is empty. <a href="selectBook.php">Browse books</a></div>
<?php else: ?>
    <div class="row">
        <?php foreach ($wishlistBooks as $book): ?>
        <div class="col-md-3 mb-4">
            <div class="card h-100 book-card">
                <img src="<?= htmlspecialchars($book['image']) ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                <div class="card-body d-flex flex-column">
                    <h5><?= htmlspecialchars($book['title']) ?></h5>
                    <p>RM <?= number_format($book['price'],2) ?></p>
                    <div class="mt-auto d-flex justify-content-between gap-2">
                        <a href="bookDetail.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary flex-fill">View Details</a>
                        <a href="removeWishList.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-danger flex-fill confirm-delete">Remove</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include '../Customize&Database/footer.php'; ?>