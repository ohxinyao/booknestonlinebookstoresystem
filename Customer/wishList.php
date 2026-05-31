<?php
session_start();
require_once '../Customize&Database/access.php';
requireLogin(); 
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT b.* 
    FROM books b 
    JOIN user_wishlist w ON b.id = w.book_id 
    WHERE w.user_id = ?
    ORDER BY w.added_at DESC
");
$stmt->execute([$userId]);
$wishlistBooks = $stmt->fetchAll();
?>

<div class="mb-3">
    <a href="selectBook.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Continue Shopping
    </a>
</div>

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

                    <div class="mt-auto wishlist-actions">
                        <a href="bookDetail.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                        <a href="removeWishList.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-danger confirm-delete">Remove</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../Customize&Database/footer.php'; ?>