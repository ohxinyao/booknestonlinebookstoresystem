<?php
session_start();
require_once __DIR__ . '/../Customize&Database/setDatabase.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $book_id = (int)$_POST['book_id'];
    $quantity = (int)($_POST['quantity'] ?? 1);
    $stmt = $pdo->prepare("SELECT stock FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $stock = (int)$stmt->fetchColumn();
    if ($stock >= $quantity && $quantity > 0) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        $_SESSION['cart'][$book_id] = ($_SESSION['cart'][$book_id] ?? 0) + $quantity;
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $newQty = $_SESSION['cart'][$book_id];
            $sync = $pdo->prepare("INSERT INTO user_cart (user_id, book_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = ?");
            $sync->execute([$userId, $book_id, $newQty, $newQty]);
        }
        $_SESSION['flash_success'] = "Book added to cart!";
    } else {
        $_SESSION['flash_error'] = "Insufficient stock.";
    }
    header("Location: index.php");
    exit;
}

include '../Customize&Database/header.php';

if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success'>{$_SESSION['flash_success']}</div>";
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    echo "<div class='alert alert-danger'>{$_SESSION['flash_error']}</div>";
    unset($_SESSION['flash_error']);
}
?>

<style>
    .card-buttons {
        display: flex;
        gap: 8px;
        margin-top: auto;
    }
    .card-buttons .btn {
        flex: 1;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0.5rem 0.25rem;
        font-size: 0.85rem;
        border-radius: 30px;
    }
    .card-buttons form {
        flex: 1;
        margin: 0;
    }
    .card-buttons form button {
        width: 100%;
    }
    .book-card .card-body {
        display: flex;
        flex-direction: column;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div id="voucherCarousel" class="carousel slide mb-5 shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#voucherCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#voucherCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#voucherCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="position-relative text-white p-5 text-center voucher-bg-1" style="min-height: 300px; background-size: cover; background-position: center;">
                        <div class="d-flex flex-column justify-content-center h-100" style="background-color: rgba(0,0,0,0.45); border-radius: inherit;">
                            <h2 class="display-5 fw-bold"><i class="fas fa-tags me-2"></i>20% OFF on Fiction Books!</h2>
                            <p class="lead">Use code: <span class="badge bg-light text-dark fs-6 px-3 py-2 mt-2">BOOKNEST20</span></p>
                            <p class="mt-3">Exclusively online - limited time offer</p>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="position-relative text-white p-5 text-center voucher-bg-2" style="min-height: 300px; background-size: cover; background-position: center;">
                        <div class="d-flex flex-column justify-content-center h-100" style="background-color: rgba(0,0,0,0.45); border-radius: inherit;">
                            <h2 class="display-5 fw-bold"><i class="fas fa-truck-fast me-2"></i>Free Shipping on Orders &gt; RM50</h2>
                            <p class="lead">Nationwide delivery - no hidden fees</p>
                            <p class="mt-3">Valid until the end of this month</p>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="position-relative text-white p-5 text-center voucher-bg-3" style="min-height: 300px; background-size: cover; background-position: center;">
                        <div class="d-flex flex-column justify-content-center h-100" style="background-color: rgba(0,0,0,0.45); border-radius: inherit;">
                            <h2 class="display-5 fw-bold"><i class="fas fa-star me-2"></i>New Member? Get 10% off!</h2>
                            <p class="lead">Welcome voucher for your first purchase</p>
                            <p class="mt-3">Sign up now and save</p>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#voucherCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#voucherCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</div>

<div class="alert alert-info text-center mb-4" role="alert">
    <strong><i class="fas fa-ticket-alt me-1"></i> Available Vouchers:</strong> 
    <span class="badge bg-success mx-1">BOOKNEST20</span> - 20% off on Fiction Books &nbsp;|&nbsp;
    <span class="badge bg-success mx-1">WELCOME10</span> - 10% off (new members) &nbsp;|&nbsp;
    <span class="badge bg-success mx-1">SAVE10</span> - 10% off on any order
    <br><small>Enter the code at checkout to apply discount.</small>
</div>

<h3>Best Selling Books</h3>
<div class="row">
    <?php
    $bestStmt = $pdo->query("SELECT * FROM books ORDER BY sales DESC LIMIT 4");
    while ($bestseller = $bestStmt->fetch(PDO::FETCH_ASSOC)):
        $stock = $bestseller['stock'];
        $disableCart = ($stock <= 0);
        if ($stock <= 0) {
            $stockHtml = '<p class="text-danger fw-bold mb-0"><i class="fas fa-times-circle"></i> Out of Stock</p>';
        } elseif ($stock <= 5) {
            $stockHtml = '<p class="text-warning fw-bold mb-0"><i class="fas fa-exclamation-triangle"></i> Only ' . $stock . ' left!</p>';
        } else {
            $stockHtml = '<p class="text-muted mb-0"><i class="fas fa-box"></i> In Stock: ' . $stock . '</p>';
        }
    ?>
    <div class="col-md-3 mb-4">
        <div class="card h-100 book-card">
            <img src="<?= htmlspecialchars($bestseller['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($bestseller['title']) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($bestseller['title']) ?></h5>
                <p class="card-text">RM <?= number_format($bestseller['price'], 2) ?></p>
                <?= $stockHtml ?>
                <p class="card-text text-muted small mt-2"><i class="fas fa-chart-line"></i> Sold: <?= (int)$bestseller['sales'] ?> books</p>
                <div class="card-buttons">
                    <a href="bookDetail.php?id=<?= $bestseller['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'customer'): ?>
                        <form method="POST">
                            <input type="hidden" name="book_id" value="<?= $bestseller['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="btn btn-sm btn-primary w-100" <?= $disableCart ? 'disabled' : '' ?>>
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-sm btn-secondary w-100" disabled title="Please login to add to cart">Add to Cart</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<h3>Featured Books</h3>
<div class="row">
    <?php
    $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 4");
    while ($book = $stmt->fetch(PDO::FETCH_ASSOC)):
        $stock = $book['stock'];
        $disableCart = ($stock <= 0);
        if ($stock <= 0) {
            $stockHtml = '<p class="text-danger fw-bold mb-0"><i class="fas fa-times-circle"></i> Out of Stock</p>';
        } elseif ($stock <= 5) {
            $stockHtml = '<p class="text-warning fw-bold mb-0"><i class="fas fa-exclamation-triangle"></i> Only ' . $stock . ' left!</p>';
        } else {
            $stockHtml = '<p class="text-muted mb-0"><i class="fas fa-box"></i> In Stock: ' . $stock . '</p>';
        }
    ?>
    <div class="col-md-3 mb-4">
        <div class="card h-100 book-card">
            <img src="<?= htmlspecialchars($book['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                <p class="card-text">RM <?= number_format($book['price'], 2) ?></p>
                <?= $stockHtml ?>
                <div class="card-buttons">
                    <a href="bookDetail.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'customer'): ?>
                        <form method="POST">
                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" name="add_to_cart" class="btn btn-sm btn-primary w-100" <?= $disableCart ? 'disabled' : '' ?>>
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-sm btn-secondary w-100" disabled title="Please login to add to cart">Add to Cart</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../Customize&Database/footer.php'; ?>