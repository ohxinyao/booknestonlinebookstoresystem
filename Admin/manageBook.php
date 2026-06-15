<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../Customize&Database/access.php';
requireLogin();
$userRole = $_SESSION['user_role'];
if ($userRole != 'admin' && $userRole != 'staff') {
    die("Access denied.");
}
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success'>{$_SESSION['flash_success']}</div>";
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    echo "<div class='alert alert-danger'>{$_SESSION['flash_error']}</div>";
    unset($_SESSION['flash_error']);
}

$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterCategory = isset($_GET['filter_category']) ? trim($_GET['filter_category']) : '';
$restock_id = isset($_GET['restock']) ? (int)$_GET['restock'] : 0;

if (isset($_POST['quick_update_stock']) && isset($_POST['book_id']) && isset($_POST['stock_delta'])) {
    $book_id = (int)$_POST['book_id'];
    $delta = (int)$_POST['stock_delta'];
    if ($delta != 0) {
        $stmt = $pdo->prepare("SELECT stock FROM books WHERE id = ?");
        $stmt->execute([$book_id]);
        $current = (int)$stmt->fetchColumn();
        $new_stock = $current + $delta;
        if ($new_stock < 0) $new_stock = 0;
        $update = $pdo->prepare("UPDATE books SET stock = ? WHERE id = ?");
        $update->execute([$new_stock, $book_id]);
        $_SESSION['flash_success'] = "Stock updated: " . ($delta > 0 ? '+' : '') . "$delta → New stock: $new_stock";
    } else {
        $_SESSION['flash_error'] = "No change (book added = 0).";
    }
    $redirectUrl = "manageBook.php";
    if ($searchKeyword) $redirectUrl .= "?search=" . urlencode($searchKeyword);
    if ($filterCategory) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?filter_category=" : "&filter_category=") . urlencode($filterCategory);
    if ($restock_id) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?restock=" : "&restock=") . $restock_id;
    header("Location: $redirectUrl");
    exit;
}

if (isset($_GET['delete'])) {
    if ($userRole !== 'admin') {
        $_SESSION['flash_error'] = 'Only admin can delete books.';
        header("Location: manageBook.php");
        exit;
    }

    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = "Book deleted successfully.";
    $redirectUrl = "manageBook.php";
    if ($searchKeyword) $redirectUrl .= "?search=" . urlencode($searchKeyword);
    if ($filterCategory) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?filter_category=" : "&filter_category=") . urlencode($filterCategory);
    header("Location: $redirectUrl");
    exit;
}

if (isset($_POST['add_category']) || isset($_POST['edit_category'])) {
    $categoryName = trim($_POST['category_name']);
    $editId = (int)($_POST['edit_category_id'] ?? 0);

    if ($categoryName === '') {
        $_SESSION['flash_error'] = 'Category name cannot be empty.';
    } else {
        if ($editId > 0) {
            $check = $pdo->prepare("SELECT id FROM categories WHERE name = ? AND id != ?");
            $check->execute([$categoryName, $editId]);
            if ($check->fetch()) {
                $_SESSION['flash_error'] = 'Category already exists.';
            } else {
                $oldStmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
                $oldStmt->execute([$editId]);
                $oldName = $oldStmt->fetchColumn();
                $update = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
                if ($update->execute([$categoryName, $editId])) {
                    $syncBooks = $pdo->prepare("UPDATE books SET category = ? WHERE category = ?");
                    $syncBooks->execute([$categoryName, $oldName]);
                    $_SESSION['flash_success'] = "Category renamed to '$categoryName' successfully.";
                } else {
                    $_SESSION['flash_error'] = 'Failed to update category.';
                }
            }
        } else {
            $check = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
            $check->execute([$categoryName]);
            if ($check->fetch()) {
                $_SESSION['flash_error'] = 'Category already exists.';
            } else {
                $insert = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                if ($insert->execute([$categoryName])) {
                    $_SESSION['flash_success'] = "Category '$categoryName' added successfully.";
                } else {
                    $_SESSION['flash_error'] = 'Failed to add category.';
                }
            }
        }
    }
    $redirectUrl = "manageBook.php";
    if ($searchKeyword) $redirectUrl .= "?search=" . urlencode($searchKeyword);
    if ($filterCategory) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?filter_category=" : "&filter_category=") . urlencode($filterCategory);
    header("Location: $redirectUrl");
    exit;
}

if (isset($_GET['delete_category'])) {
    if ($userRole !== 'admin') {
        $_SESSION['flash_error'] = 'Only admin can delete categories.';
        header("Location: manageBook.php");
        exit;
    }

    $catId = (int)$_GET['delete_category'];
    $checkBook = $pdo->prepare("SELECT COUNT(*) FROM books WHERE category = (SELECT name FROM categories WHERE id = ?)");
    $checkBook->execute([$catId]);
    $usedCount = (int)$checkBook->fetchColumn();
    if ($usedCount > 0) {
        $_SESSION['flash_error'] = "Cannot delete this category because it is used by $usedCount book(s). Please reassign those books first.";
    } else {
        $delete = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($delete->execute([$catId])) {
            $_SESSION['flash_success'] = 'Category deleted successfully.';
        } else {
            $_SESSION['flash_error'] = 'Failed to delete category.';
        }
    }
    $redirectUrl = "manageBook.php";
    if ($searchKeyword) $redirectUrl .= "?search=" . urlencode($searchKeyword);
    if ($filterCategory) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?filter_category=" : "&filter_category=") . urlencode($filterCategory);
    header("Location: $redirectUrl");
    exit;
}

$editBook = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editBook = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['quick_update_stock']) && !isset($_POST['add_category']) && !isset($_POST['edit_category'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $price = (float)$_POST['price'];
    $min_stock = isset($_POST['min_stock']) ? (int)$_POST['min_stock'] : 5;
    $stock = 0;
    $book_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $image = 'default.jpg';
    if ($book_id > 0) {
        $stmt = $pdo->prepare("SELECT image, stock FROM books WHERE id = ?");
        $stmt->execute([$book_id]);
        $orig = $stmt->fetch();
        if ($orig) {
            $image = $orig['image'];
            $stock = $orig['stock'];
        }
    } else {
        if (isset($_POST['stock'])) {
            $stock = (int)$_POST['stock'];
            if ($stock < 0) $stock = 0;
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/finalproject/booknestonlinebookstoresystem/Image/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $filename = time() . '_' . rand(100, 999) . '.' . $ext;
            $targetFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                if ($image != 'default.jpg' && file_exists($uploadDir . basename($image))) {
                    @unlink($uploadDir . basename($image));
                }
                $image = '/finalproject/booknestonlinebookstoresystem/Image/' . $filename;
            } else {
                $_SESSION['flash_error'] = "Failed to upload image.";
                $redirectUrl = "manageBook.php";
                if ($searchKeyword) $redirectUrl .= "?search=" . urlencode($searchKeyword);
                if ($filterCategory) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?filter_category=" : "&filter_category=") . urlencode($filterCategory);
                header("Location: $redirectUrl");
                exit;
            }
        } else {
            $_SESSION['flash_error'] = "Invalid image format. Only JPG, PNG, GIF allowed.";
            $redirectUrl = "manageBook.php";
            if ($searchKeyword) $redirectUrl .= "?search=" . urlencode($searchKeyword);
            if ($filterCategory) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?filter_category=" : "&filter_category=") . urlencode($filterCategory);
            header("Location: $redirectUrl");
            exit;
        }
    }

    if (empty($title) || empty($author) || $price <= 0) {
        $_SESSION['flash_error'] = "Title, author and positive price are required.";
        $redirectUrl = "manageBook.php";
        if ($searchKeyword) $redirectUrl .= "?search=" . urlencode($searchKeyword);
        if ($filterCategory) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?filter_category=" : "&filter_category=") . urlencode($filterCategory);
        header("Location: $redirectUrl");
        exit;
    }

    if ($book_id > 0) {
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, description=?, category=?, price=?, stock=?, min_stock=?, image=? WHERE id=?");
        $stmt->execute([$title, $author, $description, $category, $price, $stock, $min_stock, $image, $book_id]);
        $_SESSION['flash_success'] = "Book updated successfully.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, description, category, price, stock, min_stock, image) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$title, $author, $description, $category, $price, $stock, $min_stock, $image]);
        $_SESSION['flash_success'] = "Book added successfully.";
    }
    $redirectUrl = "manageBook.php";
    if ($searchKeyword) $redirectUrl .= "?search=" . urlencode($searchKeyword);
    if ($filterCategory) $redirectUrl .= (strpos($redirectUrl, '?') === false ? "?filter_category=" : "&filter_category=") . urlencode($filterCategory);
    header("Location: $redirectUrl");
    exit;
}

$pdo->exec("CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");

$existingCats = $pdo->query("SELECT DISTINCT category FROM books WHERE category IS NOT NULL AND category != ''")->fetchAll(PDO::FETCH_COLUMN);
foreach ($existingCats as $cat) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
    $stmt->execute([$cat]);
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
$allCategories = $pdo->query("SELECT name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
if (empty($allCategories)) {
    $defaultCategories = ['Fiction', 'Non-fiction', 'Children', 'Education & Reference', 'Science & Technology', 'Business & Finance', 'Lifestyle (Health, Cooking, Arts)'];
    foreach ($defaultCategories as $defCat) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        $stmt->execute([$defCat]);
    }
    $allCategories = $pdo->query("SELECT name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
}

$sql = "SELECT * FROM books WHERE 1=1";
$params = [];
if (!empty($searchKeyword)) {
    $sql .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$searchKeyword%";
    $params[] = "%$searchKeyword%";
}
if (!empty($filterCategory)) {
    $sql .= " AND category = ?";
    $params[] = $filterCategory;
}
$sql .= " ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll();

$booksByCategory = [];
foreach ($books as $book) {
    $cat = $book['category'] ?: 'Uncategorized';
    if (!isset($booksByCategory[$cat])) {
        $booksByCategory[$cat] = [];
    }
    $booksByCategory[$cat][] = $book;
}
?>

<style>
    .page-shell {
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
    }

    .page-head {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: end;
        gap: 0.75rem;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.35rem 0.65rem;
        background: #fff4ea;
        color: #9b5726;
        border: 1px solid #f3d2bf;
        font-size: 0.92rem;
        font-weight: 700;
    }

    .category-card {
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 2px solid #d8e1ea;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 14px 30px rgba(36, 49, 66, 0.08);
        background: linear-gradient(135deg, #ffffff, #f8fbff);
    }

    .category-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        flex: 1;
    }

    .category-card .card-header-custom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        width: 100%;
        background: linear-gradient(135deg, #243142, #3a5166);
        color: #fff;
        padding: 0.95rem 1rem;
        font-weight: 700;
    }

    .category-card .card-header-custom strong {
        flex: 1 1 220px;
    }

    .category-section {
        margin-bottom: 2rem;
    }

    .category-header {
        background: #2c3e50;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 12px 12px 0 0;
        margin-bottom: 0;
        font-size: 1.2rem;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transition: 0.2s;
    }

    .book-thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border: 1px solid #dee2e6;
    }

    .btn i {
        margin-right: 5px;
    }

    .modal .form-control,
    .modal .form-select {
        border-radius: 8px;
    }

    .modal .form-label {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    #imagePreviewContainer {
        margin-top: 10px;
        padding: 8px;
        background: #f1f3f5;
        border-radius: 12px;
        display: inline-block;
    }

    #imagePreview {
        max-width: 120px;
        max-height: 120px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    .low-stock {
        background-color: #fff3cd !important;
    }

    .quick-stock-form {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .quick-stock-form input {
        width: 80px;
    }

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .toolbar-left {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .toolbar-right {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-form {
        margin: 0;
    }

    .filter-select {
        min-width: 220px;
        width: auto;
        height: 40px;
        padding: 0 2rem 0 0.75rem;
        font-size: 0.9rem;
        border-radius: 8px;
        border: 1px solid #ced4da;
        background-color: white;
        cursor: pointer;
    }

    .filter-select:focus {
        border-color: #b85c38;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(184, 92, 56, 0.25);
    }

    .filter-select option {
        white-space: normal;
        word-break: break-word;
        padding: 4px 8px;
    }

    .search-form {
        margin: 0;
    }

    .search-wrapper {
        display: flex;
        align-items: center;
        height: 40px;
        min-width: 220px;
        width: auto;
        border: 1px solid #ced4da;
        border-radius: 8px;
        overflow: hidden;
        background: white;
        transition: all 0.2s ease;
    }

    .search-wrapper:focus-within {
        border-color: #b85c38;
        box-shadow: 0 0 0 0.2rem rgba(184, 92, 56, 0.25);
    }

    .search-input {
        flex: 1;
        min-width: 140px;
        height: 100%;
        padding: 0 0.75rem;
        border: none;
        outline: none;
        font-size: 0.9rem;
        background: transparent;
    }

    .search-btn {
        width: 44px;
        height: 100%;
        border: none;
        background: linear-gradient(135deg, #b85c38, #8f3f25);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .search-btn:hover {
        background: linear-gradient(135deg, #c4663f, #7b351f);
    }

    .search-btn i {
        margin: 0;
        font-size: 1rem;
    }

    .clear-btn {
        width: 44px;
        height: 100%;
        border: none;
        background: #f8f9fa;
        color: #6c757d;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        border-left: 1px solid #ced4da;
        transition: all 0.2s ease;
    }

    .clear-btn:hover {
        background: #e9ecef;
        color: #343a40;
        text-decoration: none;
    }

    .clear-btn i {
        margin: 0;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .toolbar-left,
        .toolbar-right {
            width: 100%;
            justify-content: center;
        }
        .filter-select {
            min-width: 100%;
            width: 100%;
        }
        .search-wrapper {
            min-width: 100%;
            width: 100%;
        }
        .search-input {
            min-width: auto;
        }
    }
</style>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h2 class="mb-1">Manage Books & Categories</h2>
        </div>
        <span class="chip"><i class="fas fa-user-shield me-1"></i><?= htmlspecialchars($userRole) ?> access</span>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="category-card">
                <div class="card-header-custom">
                    <strong id="categoryCardTitle"><i class="fas fa-plus-circle me-2"></i>Add New Category</strong>
                    <span class="chip bg-white text-dark">Quick add</span>
                </div>
                <div class="card-body pt-3 pb-3">
                    <form method="POST" id="categoryForm" class="row g-3 align-items-end mb-0">
                        <input type="hidden" name="edit_category_id" id="editCategoryIdField" value="">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Category Name</label>
                            <input type="text" name="category_name" id="categoryNameInput" class="form-control" placeholder="e.g. Fiction, Non-fiction" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" id="categorySubmitBtn" name="add_category" class="btn btn-primary w-100">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="category-card">
                <div class="card-header-custom">
                    <strong><i class="fas fa-tags me-2"></i>Existing Categories</strong>
                    <span class="chip bg-white text-dark">Live list</span>
                </div>
                <div class="card-body">
                    <?php if (empty($categories)): ?>
                        <p class="text-muted mb-0">No categories yet.</p>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($categories as $cat): ?>
                                <span class="badge rounded-pill bg-light text-dark border d-inline-flex align-items-center gap-2 py-2 px-3">
                                    <i class="fas fa-tag text-secondary"></i><?= htmlspecialchars($cat['name']) ?>
                                    <?php if ($userRole === 'admin'): ?>
                                        <button type="button" class="btn btn-link p-0 text-primary text-decoration-none" data-category='<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>' onclick="openCategoryEdit(this); return false;" title="Edit category">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <a href="?delete_category=<?= (int)$cat['id'] ?><?= $searchKeyword ? '&search=' . urlencode($searchKeyword) : '' ?><?= $filterCategory ? '&filter_category=' . urlencode($filterCategory) : '' ?>" class="text-danger text-decoration-none" onclick="return confirm('Delete this category? It cannot be used by any book.')" title="Delete category">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="toolbar">
        <div class="toolbar-left">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookModal" onclick="clearForm()">
                <i class="fas fa-plus-circle"></i> Add New Book
            </button>
        </div>
        <div class="toolbar-right">
            <form method="GET" class="filter-form">
                <?php if ($searchKeyword): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($searchKeyword) ?>">
                <?php endif; ?>
                <select name="filter_category" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>" <?= $filterCategory == $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <form method="GET" class="search-form">
                <?php if ($filterCategory): ?>
                    <input type="hidden" name="filter_category" value="<?= htmlspecialchars($filterCategory) ?>">
                <?php endif; ?>
                <div class="search-wrapper">
                    <input type="text" name="search" class="search-input" placeholder="Search by title or author..." value="<?= htmlspecialchars($searchKeyword) ?>">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    <?php if ($searchKeyword || $filterCategory): ?>
                        <a href="manageBook.php" class="clear-btn"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if ($searchKeyword): ?>
        <div class="alert alert-info">
            <i class="fas fa-search"></i> Search results for: <strong>"<?= htmlspecialchars($searchKeyword) ?>"</strong>
            <?php if ($filterCategory): ?>
                <span class="ms-2">| Category: <strong><?= htmlspecialchars($filterCategory) ?></strong></span>
            <?php endif; ?>
        </div>
    <?php elseif ($filterCategory): ?>
        <div class="alert alert-info">
            <i class="fas fa-filter"></i> Filtering by category: <strong><?= htmlspecialchars($filterCategory) ?></strong>
            <a href="manageBook.php" class="float-end">Clear filter</a>
        </div>
    <?php endif; ?>

    <?php if (empty($booksByCategory)): ?>
        <div class="alert alert-warning">No books found matching your criteria.</div>
    <?php else: ?>
        <?php foreach ($booksByCategory as $catName => $catBooks): ?>
            <?php if (empty($catBooks)) continue; ?>
            <div class="category-section">
                <div class="category-header">
                    <i class="fas fa-folder-open me-2"></i> <?= htmlspecialchars($catName) ?>
                    <span class="badge bg-light text-dark ms-2"><?= count($catBooks) ?> books</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Price</th>
                                <th>Latest Stock</th>
                                <th>Min Stock</th>
                                <th>New Stock (+/-)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sn = 1;
                            foreach ($catBooks as $book): $isLowStock = ($book['stock'] <= $book['min_stock']); ?>
                                <tr class="<?= $isLowStock ? 'low-stock' : '' ?> book-row" data-book-id="<?= $book['id'] ?>">
                                    <td><?= $sn++ ?></td>
                                    <td><img src="<?= htmlspecialchars($book['image']) ?>" class="book-thumb" onerror="this.src='/finalproject/booknestonlinebookstoresystem/Image/default.jpg'; this.style.opacity='0.7';"></td>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td>RM <?= number_format($book['price'], 2) ?></td>
                                    <td><span class="badge <?= $isLowStock ? 'bg-danger' : 'bg-secondary' ?>"><?= $book['stock'] ?></span></td>
                                    <td><?= $book['min_stock'] ?></td>
                                    <td>
                                        <form method="POST" class="quick-stock-form">
                                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                            <input type="number" name="stock_delta" value="0" placeholder="+/-" class="form-control form-control-sm" required>
                                            <button type="submit" name="quick_update_stock" class="btn btn-sm btn-outline-primary">Update</button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#bookModal" onclick="editBook(<?= htmlspecialchars(json_encode($book)) ?>); return false;">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ($userRole === 'admin'): ?>
                                                <a href="?delete=<?= $book['id'] ?><?= $searchKeyword ? '&search=' . urlencode($searchKeyword) : '' ?><?= $filterCategory ? '&filter_category=' . urlencode($filterCategory) : '' ?>" class="btn btn-sm btn-danger confirm-delete" onclick="return confirm('Delete this book?')">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="modal fade" id="bookModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="fas fa-book"></i> Book Form</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="bookId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Author <span class="text-danger">*</span></label>
                            <input type="text" name="author" id="author" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category" id="category" class="form-select">
                                <?php foreach ($allCategories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price (RM) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Stock Threshold</label>
                            <input type="number" name="min_stock" id="min_stock" class="form-control" value="5">
                        </div>
                        <div class="col-md-4" id="stockFieldRow">
                            <label class="form-label">Initial Stock</label>
                            <input type="number" name="stock" id="stock" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Cover Image</label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/gif" id="imageInput">
                            <div id="imagePreviewContainer" class="mt-2 text-center" style="display: none;">
                                <img id="imagePreview" src="#" class="img-thumbnail" style="max-height: 120px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function clearForm() {
        document.getElementById('bookId').value = '';
        document.getElementById('title').value = '';
        document.getElementById('author').value = '';
        document.getElementById('description').value = '';
        document.getElementById('category').selectedIndex = 0;
        document.getElementById('price').value = '';
        document.getElementById('min_stock').value = '5';
        document.getElementById('stock').value = '0';
        document.getElementById('stockFieldRow').style.display = 'block';
        document.getElementById('imagePreviewContainer').style.display = 'none';
        document.getElementById('imagePreview').src = '';
        resetCategoryForm();
    }

    function resetCategoryForm() {
        document.getElementById('categoryCardTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Add New Category';
        document.getElementById('categorySubmitBtn').innerHTML = 'Add Category';
        document.getElementById('categorySubmitBtn').name = 'add_category';
        document.getElementById('categoryNameInput').value = '';
        document.getElementById('editCategoryIdField').value = '';
    }

    function openCategoryEdit(btn) {
        var category = JSON.parse(btn.getAttribute('data-category'));
        document.getElementById('categoryCardTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Existing Category';
        document.getElementById('categorySubmitBtn').innerHTML = 'Update Category';
        document.getElementById('categorySubmitBtn').name = 'edit_category';
        document.getElementById('categoryNameInput').value = category.name;
        document.getElementById('editCategoryIdField').value = category.id;
        document.querySelector('.category-card').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        document.getElementById('categoryNameInput').focus();
        document.getElementById('categoryNameInput').select();
    }

    function editBook(book) {
        document.getElementById('bookId').value = book.id;
        document.getElementById('title').value = book.title;
        document.getElementById('author').value = book.author;
        document.getElementById('description').value = book.description || '';
        let categorySelect = document.getElementById('category');
        for (let i = 0; i < categorySelect.options.length; i++) {
            if (categorySelect.options[i].value === book.category) {
                categorySelect.selectedIndex = i;
                break;
            }
        }
        document.getElementById('price').value = book.price;
        document.getElementById('min_stock').value = book.min_stock || 5;
        document.getElementById('stockFieldRow').style.display = 'none';
        let imgSrc = (book.image && book.image !== 'default.jpg') ? book.image : '/finalproject/booknestonlinebookstoresystem/Image/default.jpg';
        document.getElementById('imagePreview').src = imgSrc;
        document.getElementById('imagePreviewContainer').style.display = 'block';
    }

    document.getElementById('imageInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('imagePreview').src = ev.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    var restockBookId = <?= $restock_id ?>;

    document.addEventListener('DOMContentLoaded', function() {
        if (restockBookId > 0) {
            setTimeout(function() {
                var rows = document.querySelectorAll('.book-row');
                for (var i = 0; i < rows.length; i++) {
                    if (rows[i].getAttribute('data-book-id') == restockBookId) {
                        rows[i].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        rows[i].style.backgroundColor = '#fff3cd';
                        rows[i].style.transition = 'background-color 0.5s';
                        setTimeout(function(row) {
                            row.style.backgroundColor = '';
                        }, 3000, rows[i]);
                        break;
                    }
                }
            }, 500);
        }
    });
</script>
<?php include '../Customize&Database/footer.php'; ?>