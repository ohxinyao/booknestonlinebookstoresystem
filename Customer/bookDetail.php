<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$book_id = $_GET['id'] ?? 0;
$wishlist_success = isset($_GET['wishlist_success']) ? true : false;
$bookStmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$bookStmt->execute([$book_id]);
$book = $bookStmt->fetch();
if (!$book) {
    die("Book not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $qty = intval($_POST['quantity']);
    if ($qty > 0 && $qty <= $book['stock']) {
        $_SESSION['cart'][$book_id] = ($_SESSION['cart'][$book_id] ?? 0) + $qty;
        $added = true;
    } else {
        $error = "Invalid quantity or insufficient stock.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_id, rating, comment, is_anonymous) 
                            VALUES (?, ?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE rating = ?, comment = ?, is_anonymous = ?");
    $stmt->execute([$book_id, $_SESSION['user_id'], $rating, $comment, $is_anonymous, 
                    $rating, $comment, $is_anonymous]);

    $avgStmt = $pdo->prepare("SELECT AVG(rating) as avg, COUNT(*) as cnt FROM reviews WHERE book_id = ?");
    $avgStmt->execute([$book_id]);
    $data = $avgStmt->fetch();
    $pdo->prepare("UPDATE books SET rating_avg = ?, rating_count = ? WHERE id = ?")->execute([$data['avg'], $data['cnt'], $book_id]);
    header("Location: bookDetail.php?id=" . $book_id);
    exit;
}

$reviewsStmt = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.book_id = ? ORDER BY r.created_at DESC");
$reviewsStmt->execute([$book_id]);
$reviews = $reviewsStmt->fetchAll();

$stock = $book['stock'];
if ($stock <= 0) {
    $stockBadge = '<span class="badge bg-danger">Out of Stock</span>';
} elseif ($stock <= 5) {
    $stockBadge = '<span class="badge bg-warning text-dark">Low Stock: ' . $stock . ' book left</span>';
} else {
    $stockBadge = '<span class="badge bg-success">In Stock: ' . $stock . '</span>';
}
?>
<div class="mb-3">
    <a href="selectBook.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Books
    </a>
</div>
<div class="row">
    <div class="col-md-4">
        <img src="<?= htmlspecialchars($book['image']) ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($book['title']) ?>">
    </div>
    
    <div class="col-md-8">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($book['category']) ?></p>
        <p><strong>Price:</strong> RM <?= number_format($book['price'], 2) ?></p>
        <p><strong>Stock:</strong> <?= $stockBadge ?></p>
        <div class="mb-2">
            <span class="star-rating">★ <?= number_format($book['rating_avg'], 1) ?> / 5</span> (<?= $book['rating_count'] ?> reviews)
        </div>
        <div class="description mb-3">
            <?= nl2br(htmlspecialchars($book['description'])) ?>
        </div>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'customer'): ?>
            <form method="POST" class="add-to-cart-form">
                <div class="row g-2">
                    <div class="col-auto">
                        <input type="number" name="quantity" value="1" min="1" max="<?= $book['stock'] ?>" class="form-control" style="width:100px">
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                    </div>
                </div>
                <?php if (isset($added)) echo '<div class="alert alert-success mt-2">Added to cart! <a href="../Customer/shoppingCart.php">View cart</a></div>'; ?>
                <?php if (isset($error)) echo '<div class="alert alert-danger mt-2">' . $error . '</div>'; ?>
            </form>

            <form method="POST" action="addWishList.php" class="mt-2">
                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                <button type="submit" class="btn btn-outline-danger">
                    <i class="far fa-heart"></i> Add to Wishlist
                </button>
            </form>
            <?php if ($wishlist_success): ?>
                <div class="alert alert-success mt-2">
                    Added to wishlist! <a href="wishList.php">View wishlist</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <hr>
        <h4>Rate & Review this book</h4>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST">
                <div class="mb-2">
                    <select name="rating" class="form-select w-auto">
                        <option value="5">★★★★★ (5)</option>
                        <option value="4">★★★★ (4)</option>
                        <option value="3">★★★ (3)</option>
                        <option value="2">★★ (2)</option>
                        <option value="1">★ (1)</option>
                    </select>
                </div>
                <div class="mb-2">
                    <textarea name="comment" rows="3" class="form-control" placeholder="Write your review..."></textarea>
                </div>
                <div class="mb-2">
                    <div class="form-check">
                        <input type="checkbox" name="is_anonymous" value="1" class="form-check-input" id="anonymousCheck">
                        <label class="form-check-label" for="anonymousCheck">Post anonymously (name will not be shown)</label>
                    </div>
                </div>
                <button type="submit" name="submit_review" class="btn btn-warning">Submit Review</button>
            </form>
        <?php else: ?>
            <p><a href="../Customer/login.php">Login</a> to leave a review.</p>
        <?php endif; ?>
        <hr>
        <h4>Customer Reviews</h4>
        <?php if (count($reviews) == 0): ?>
            <p>No reviews yet. Be the first!</p>
        <?php else: ?>
            <?php foreach ($reviews as $rev): ?>
                <div class="border rounded p-2 mb-2 bg-light">
                    <strong><?= $rev['is_anonymous'] ? 'Anonymous' : htmlspecialchars($rev['name']) ?></strong>
                    <span class="star-rating">★ <?= $rev['rating'] ?></span>
                    <small class="text-muted"><?= date('d M Y', strtotime($rev['created_at'])) ?></small>
                    <p class="mt-1"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php include '../Customize&Database/footer.php'; ?>