<?php
session_start();
require_once __DIR__ . '/../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div id="voucherCarousel" class="carousel slide mb-5 shadow rounded" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="bg-primary text-white p-5 text-center rounded">
                        <h2>🎉 20% OFF on Fiction Books!</h2>
                        <p>Use code: FICTION20 (UI Demo - No backend)</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="bg-success text-white p-5 text-center rounded">
                        <h2>📦 Free Shipping on Orders > RM50</h2>
                        <p>Promo valid until end of month</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="bg-warning text-dar k p-5 text-center rounded">
                        <h2>⭐ New Member? Get 10% off!</h2>
                        <p>Welcome voucher for first purchase</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#voucherCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#voucherCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</div>

<h3>Best Selling Books</h3>
<div class="row">
    <?php
    $bestStmt = $pdo->query("SELECT * FROM books ORDER BY sales DESC LIMIT 4");
    while ($bestseller = $bestStmt->fetch(PDO::FETCH_ASSOC)):
    ?>
    <div class="col-md-3 mb-4">
        <div class="card h-100 book-card">
            <img src="<?= htmlspecialchars($bestseller['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($bestseller['title']) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($bestseller['title']) ?></h5>
                <p class="card-text">RM <?= number_format($bestseller['price'], 2) ?></p>
                <a href="bookDetail.php?id=<?= $bestseller['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
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
    ?>
    <div class="col-md-3 mb-4">
        <div class="card h-100 book-card">
            <img src="<?= htmlspecialchars($book['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                <p class="card-text">RM <?= number_format($book['price'], 2) ?></p>
                <a href="../Customer/bookDetail.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../Customize&Database/footer.php'; ?>