<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../Customize&Database/access.php';
requireRole('admin');
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
    header("Location: manageBook.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = "Book deleted successfully.";
    header("Location: manageBook.php");
    exit;
}

$editBook = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editBook = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['quick_update_stock'])) {
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
                header("Location: manageBook.php");
                exit;
            }
        } else {
            $_SESSION['flash_error'] = "Invalid image format. Only JPG, PNG, GIF allowed.";
            header("Location: manageBook.php");
            exit;
        }
    }

    if (empty($title) || empty($author) || $price <= 0) {
        $_SESSION['flash_error'] = "Title, author and positive price are required.";
        header("Location: manageBook.php");
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
    header("Location: manageBook.php");
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

$allCategories = $pdo->query("SELECT name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
if (empty($allCategories)) {
    $defaultCategories = ['Fiction', 'Non-fiction', 'Children', 'Education & Reference', 'Science & Technology', 'Business & Finance', 'Lifestyle (Health, Cooking, Arts)'];
    foreach ($defaultCategories as $defCat) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        $stmt->execute([$defCat]);
    }
    $allCategories = $pdo->query("SELECT name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
}

$books = $pdo->query("SELECT * FROM books ORDER BY id ASC")->fetchAll();
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
</style>

<h2>Manage Books</h2>
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#bookModal" onclick="clearForm()">
    <i class="fas fa-plus-circle"></i> Add New Book
</button>

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
                        <th>New Stock (+/−)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn = 1; foreach ($catBooks as $book): $isLowStock = ($book['stock'] <= $book['min_stock']); ?>
                        <tr class="<?= $isLowStock ? 'low-stock' : '' ?>">
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
                                    <a href="?delete=<?= $book['id'] ?>" class="btn btn-sm btn-danger confirm-delete" onclick="return confirm('Delete this book?')">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; ?>

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
</script>
<?php include '../Customize&Database/footer.php'; ?>