<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

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
            $syncStmt = $pdo->prepare("INSERT INTO user_cart (user_id, book_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = ?");
            $syncStmt->execute([$userId, $book_id, $newQty, $newQty]);
        }
        $_SESSION['flash_success'] = "Book added to cart!";
    } else {
        $_SESSION['flash_error'] = "Insufficient stock.";
    }
    $queryString = http_build_query(array_merge($_GET, ['page' => $_GET['page'] ?? 1]));
    header("Location: selectBook.php?" . $queryString);
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 8;
$offset = ($page - 1) * $perPage;

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'title_asc';

$baseSql = "SELECT * FROM books WHERE 1=1";
$countSql = "SELECT COUNT(*) FROM books WHERE 1=1";
$params = [];

if (!empty($search)) {
    $baseSql .= " AND (title LIKE ? OR author LIKE ?)";
    $countSql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($category)) {
    $baseSql .= " AND category = ?";
    $countSql .= " AND category = ?";
    $params[] = $category;
}

switch ($sort) {
    case 'price_asc':
        $orderBy = " ORDER BY price ASC";
        break;
    case 'price_desc':
        $orderBy = " ORDER BY price DESC";
        break;
    case 'rating_desc':
        $orderBy = " ORDER BY rating_avg DESC";
        break;
    default:
        $orderBy = " ORDER BY title ASC";
}

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalBooks = $countStmt->fetchColumn();
$totalPages = ceil($totalBooks / $perPage);

$dataSql = $baseSql . $orderBy . " LIMIT $offset, $perPage";
$dataStmt = $pdo->prepare($dataSql);
$dataStmt->execute($params);
$books = $dataStmt->fetchAll();

$catStmt = $pdo->query("SELECT DISTINCT category FROM books WHERE category IS NOT NULL");
$categories = $catStmt->fetchAll();

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
    .custom-pagination {
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .custom-pagination .pagination {
        justify-content: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .custom-pagination .page-item {
        margin: 0;
    }

    .custom-pagination .page-link {
        border: none;
        background: #f8fafc;
        color: #2c3e50;
        font-weight: 500;
        padding: 0.6rem 1rem;
        border-radius: 40px !important;
        transition: all 0.25s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        min-width: 44px;
        text-align: center;
    }

    .custom-pagination .page-link:hover {
        background: linear-gradient(135deg, var(--bn-primary), var(--bn-primary-dark));
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(184, 92, 56, 0.35);
    }

    .custom-pagination .active .page-link {
        background: linear-gradient(135deg, var(--bn-primary), var(--bn-primary-dark));
        color: white;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(184, 92, 56, 0.4);
        cursor: default;
    }

    .custom-pagination .disabled .page-link {
        background: #eef2f6;
        color: #95a5a6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .custom-pagination .page-item:first-child .page-link,
    .custom-pagination .page-item:last-child .page-link {
        background: white;
        border: 1px solid #dee2e6;
        padding: 0.6rem 1.2rem;
    }

    .custom-pagination .page-item:first-child .page-link:hover,
    .custom-pagination .page-item:last-child .page-link:hover {
        background: var(--bn-primary-dark);
        border-color: var(--bn-primary-dark);
        color: white;
    }

    @media (max-width: 576px) {
        .custom-pagination .page-link {
            padding: 0.4rem 0.8rem;
            min-width: 36px;
            font-size: 0.85rem;
        }
    }

    .book-card-clickable {
        cursor: pointer;
    }

    .card-buttons {
        display: flex;
        gap: 10px;
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
</style>

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
        <div class="col-12">
            <div class="alert alert-info">No books found.</div>
        </div>
    <?php else: ?>
        <?php foreach ($books as $book):
            $stock = $book['stock'];
            if ($stock <= 0) {
                $stockHtml = '<p class="text-danger fw-bold mb-0"><i class="fas fa-times-circle"></i> Out of Stock</p>';
                $disableCart = true;
            } elseif ($stock <= 5) {
                $stockHtml = '<p class="text-warning fw-bold mb-0"><i class="fas fa-exclamation-triangle"></i> Only ' . $stock . ' left!</p>';
                $disableCart = false;
            } else {
                $stockHtml = '<p class="text-muted mb-0"><i class="fas fa-box"></i> In Stock: ' . $stock . '</p>';
                $disableCart = false;
            }
        ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100 book-card book-card-clickable" onclick="location.href='bookDetail.php?id=<?= $book['id'] ?>';">
                    <img src="<?= htmlspecialchars($book['image']) ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <p class="card-text">by <?= htmlspecialchars($book['author']) ?></p>
                        <p class="card-text"><strong>RM <?= number_format($book['price'], 2) ?></strong></p>
                        <?= $stockHtml ?>
                        <p class="card-text mt-2"><small class="star-rating"><i class="fas fa-star"></i> <?= number_format($book['rating_avg'], 1) ?></small> (<?= $book['rating_count'] ?> reviews)</p>
                        <div class="card-buttons" onclick="event.stopPropagation();">
                            <a href="bookDetail.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'customer'): ?>
                                <form method="POST" class="d-inline w-100">
                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="btn btn-sm btn-primary w-100" <?= $disableCart ? 'disabled' : '' ?>>
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled title="Please login to add to cart">Add to Cart</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <div class="custom-pagination">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Previous</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>
                <?php endif; ?>

                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                if ($startPage > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a></li>';
                    if ($startPage > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor;
                if ($endPage < $totalPages) {
                    if ($endPage < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '">' . $totalPages . '</a></li>';
                }
                ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" aria-label="Next">
                            <span aria-hidden="true">Next &raquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
<?php endif; ?>

<?php include '../Customize&Database/footer.php'; ?>