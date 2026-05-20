<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff');
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$lowStockBooks = $pdo->query("SELECT * FROM books WHERE stock <= min_stock ORDER BY stock ASC")->fetchAll();
$lowStockCount = count($lowStockBooks);
?>
<h2>Staff Dashboard</h2>

<?php if ($lowStockCount > 0): ?>
    <div class="alert alert-warning">
        <strong>⚠️ Low Stock Alert!</strong> There <?= $lowStockCount == 1 ? 'is' : 'are' ?> <?= $lowStockCount ?> book(s) with stock below the minimum threshold.
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr><th>ID</th><th>Title</th><th>Current Stock</th><th>Min Threshold</th><th>Action</th></tr>
            </thead>
            <tbody>
            <?php foreach ($lowStockBooks as $book): ?>
                <tr>
                    <td><?= $book['id'] ?></td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><span class="badge bg-danger"><?= $book['stock'] ?></span></td>
                    <td><?= $book['min_stock'] ?></td>
                    <td><a href="staffManage.php?edit=<?= $book['id'] ?>" class="btn btn-sm btn-primary">Restock</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-success">✅ All books are sufficiently stocked.</div>
<?php endif; ?>
<?php include '../Customize&Database/footer.php'; ?>