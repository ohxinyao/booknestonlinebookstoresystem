<?php
session_start();
require_once __DIR__ . '/../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';
?>

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
                            <h2 class="display-5 fw-bold">🎉 20% OFF on Fiction Books!</h2>
                            <p class="lead">Use code: <span class="badge bg-light text-dark fs-6 px-3 py-2 mt-2">BOOKNEST20</span></p>
                            <p class="mt-3">Exclusively online – limited time offer</p>
                        </div>
                    </div>
                </div>
  
                <div class="carousel-item">
                    <div class="position-relative text-white p-5 text-center voucher-bg-2" style="min-height: 300px; background-size: cover; background-position: center;">
                        <div class="d-flex flex-column justify-content-center h-100" style="background-color: rgba(0,0,0,0.45); border-radius: inherit;">
                            <h2 class="display-5 fw-bold">📦 Free Shipping on Orders > RM50</h2>
                            <p class="lead">Nationwide delivery – no hidden fees</p>
                            <p class="mt-3">Valid until the end of this month</p>
                        </div>
                    </div>
                </div>
          
                <div class="carousel-item">
                    <div class="position-relative text-white p-5 text-center voucher-bg-3" style="min-height: 300px; background-size: cover; background-position: center;">
                        <div class="d-flex flex-column justify-content-center h-100" style="background-color: rgba(0,0,0,0.45); border-radius: inherit;">
                            <h2 class="display-5 fw-bold">⭐ New Member? Get 10% off!</h2>
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
                <a href="bookDetail.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../Customize&Database/footer.php'; ?>