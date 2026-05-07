<?php
require_once '../Customize&Database/access.php';
requireRole('admin');
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manageBook.php");
    exit;
}

// Handle add/edit
$editBook = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editBook = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $editBook['image'] ?? 'default.jpg';

   if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/finalproject/booknestonlinebookstoresystem/Image/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $fileName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $image = '/finalproject/booknestonlinebookstoresystem/Image/' . $fileName;
    }
   }

    if (isset($_POST['id']) && $_POST['id'] > 0) {
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, description=?, category=?, price=?, stock=?, image=? WHERE id=?");
        $stmt->execute([$title, $author, $description, $category, $price, $stock, $image, $_POST['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, description, category, price, stock, image) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$title, $author, $description, $category, $price, $stock, $image]);
    }
    header("Location: manageBook.php");
    exit;
}

$books = $pdo->query("SELECT * FROM books ORDER BY id ASC")->fetchAll();
?>
<h2>Manage Books</h2>
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#bookModal" onclick="clearForm()">Add New Book</button>
<table class="table table-bordered">
    <thead>
        <tr><th>ID</th><th>Image</th><th>Title</th><th>Author</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php $sn = 1; ?>
        <?php foreach ($books as $book): ?>
        <tr>
            <td><?= $sn++ ?></td>  
            <td><img src="<?= htmlspecialchars($book['image']) ?>" width="50"></td>
            <td><?= htmlspecialchars($book['title']) ?></td>
            <td><?= htmlspecialchars($book['author']) ?></td>
            <td>RM <?= number_format($book['price'],2) ?></td>
            <td><?= $book['stock'] ?></td>
            <td>
                <a href="?edit=<?= $book['id'] ?>" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#bookModal" onclick="editBook(<?= htmlspecialchars(json_encode($book)) ?>)">Edit</a>
                <a href="?delete=<?= $book['id'] ?>" class="btn btn-sm btn-danger confirm-delete">Delete</a>
            </td>
        </tr>
    <?php endforeach;?>
</tbody>
</table>

<div class="modal fade" id="bookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header"><h5 class="modal-title">Book Form</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="bookId">
                    <div class="mb-2"><label>Title</label><input type="text" name="title" id="title" class="form-control" required></div>
                    <div class="mb-2"><label>Author</label><input type="text" name="author" id="author" class="form-control" required></div>
                    <div class="mb-2"><label>Description</label><textarea name="description" id="description" class="form-control"></textarea></div>
                    <div class="mb-2"><label>Category</label><input type="text" name="category" id="category" class="form-control"></div>
                    <div class="mb-2"><label>Price (RM)</label><input type="number" step="0.01" name="price" id="price" class="form-control" required></div>
                    <div class="mb-2"><label>Stock</label><input type="number" name="stock" id="stock" class="form-control" required></div>
                    <div class="mb-2"><label>Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button></div>
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
    document.getElementById('category').value = '';
    document.getElementById('price').value = '';
    document.getElementById('stock').value = '';
}
function editBook(book) {
    document.getElementById('bookId').value = book.id;
    document.getElementById('title').value = book.title;
    document.getElementById('author').value = book.author;
    document.getElementById('description').value = book.description;
    document.getElementById('category').value = book.category;
    document.getElementById('price').value = book.price;
    document.getElementById('stock').value = book.stock;
}
</script>
<?php include '../Customize&Database/footer.php'; ?>