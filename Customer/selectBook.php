<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'title_asc';

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'rating_desc':
        $sql .= " ORDER BY rating_avg DESC";
        break;
    default:
        $sql .= " ORDER BY title ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

$catStmt = $pdo->query("SELECT DISTINCT category FROM books WHERE category IS NOT NULL");
$categories = $catStmt->fetchAll();
?>

<h2>Book Catalog</h2>
<form method="GET" class="row g-3 mb-4">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search by title or author" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
        <select name="category" class="form-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['category']) ?>" <?= $category == $cat['category'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['category']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="sort" class="form-select">
            <option value="title_asc" <?= $sort == 'title_asc' ? 'selected' : '' ?>>Sort by Title (A-Z)</option>
            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="rating_desc" <?= $sort == 'rating_desc' ? 'selected' : '' ?>>Highest Rated</option>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Search</button>
    </div>
</form>

<div class="row">
    <?php if (count($books) == 0): ?>
        <div class="col-12"><div class="alert alert-info">No books found.</div></div>
    <?php else: ?>
        <?php foreach ($books as $book):
            $stock = $book['stock'];
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
                <img src="<?= htmlspecialchars($book['image']) ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                    <p class="card-text">by <?= htmlspecialchars($book['author']) ?></p>
                    <p class="card-text"><strong>RM <?= number_format($book['price'], 2) ?></strong></p>
                    <?= $stockHtml ?>
                    <p class="card-text mt-2"><small class="star-rating"><i class="fas fa-star"></i> <?= number_format($book['rating_avg'],1) ?></small> (<?= $book['rating_count'] ?> reviews)</p>
                    <a href="bookDetail.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../Customize&Database/footer.php'; ?>