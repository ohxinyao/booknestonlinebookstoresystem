<?php
session_start();
require_once '../Customize&Database/access.php';
requireLogin();  

$userRole = $_SESSION['user_role'];
if ($userRole != 'admin' && $userRole != 'staff') {
    die("Access denied.");
}

require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $categoryName = trim($_POST['category_name']);
    if (empty($categoryName)) {
        $error = "Category name cannot be empty.";
    } else {
        $check = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $check->execute([$categoryName]);
        if ($check->fetch()) {
            $error = "Category already exists.";
        } else {
            $insert = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            if ($insert->execute([$categoryName])) {
                $success = "Category '$categoryName' added successfully.";
            } else {
                $error = "Failed to add category.";
            }
        }
    }
}

if (isset($_GET['delete']) && $userRole == 'admin') {
    $catId = (int)$_GET['delete'];
    $checkBook = $pdo->prepare("SELECT COUNT(*) FROM books WHERE category = (SELECT name FROM categories WHERE id = ?)");
    $checkBook->execute([$catId]);
    $usedCount = $checkBook->fetchColumn();
    if ($usedCount > 0) {
        $error = "Cannot delete this category because it is used by $usedCount book(s). Please reassign those books first.";
    } else {
        $delete = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($delete->execute([$catId])) {
            $success = "Category deleted successfully.";
        } else {
            $error = "Failed to delete category.";
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
?>

<style>
    .page-shell {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .page-heading {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: #243142;
        margin-bottom: 0.25rem;
    }

    .page-subtitle {
        color: #5f6b7a;
        font-size: 0.98rem;
    }

    .badge-soft {
        background: #fff4ea;
        border: 1px solid #f3d2bf;
        color: #9b5726;
        border-radius: 999px;
        padding: 0.35rem 0.65rem;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 0.25rem;
    }

    .stat-card {
        background: linear-gradient(145deg, #ffffff, #f5f8fc);
        border: 1px solid #e5ebf2;
        border-radius: 18px;
        box-shadow: 0 12px 28px rgba(36, 49, 66, 0.08);
        padding: 1rem 1.1rem;
    }

    .stat-label {
        color: #6b7786;
        font-size: 0.92rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .stat-value {
        color: #243142;
        font-size: 1.45rem;
        font-weight: 800;
        margin-top: 0.35rem;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: linear-gradient(135deg, #b85c38, #d98a59);
        box-shadow: 0 10px 18px rgba(184, 92, 56, 0.25);
        margin-bottom: 0.45rem;
    }

    .card-custom {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(229, 235, 242, 0.95);
        border-radius: 18px;
        box-shadow: 0 16px 32px rgba(36, 49, 66, 0.08);
        margin-bottom: 1.25rem;
        overflow: hidden;
        backdrop-filter: blur(3px);
    }

    .card-header-custom {
        background: linear-gradient(135deg, #243142, #3a5166);
        color: white;
        padding: 1.1rem 1.15rem;
        min-height: 68px;
        font-weight: 700;
        border-radius: 18px 18px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .card-header-custom small {
        color: rgba(255,255,255,0.82);
        font-weight: 500;
    }

    .form-panel {
        background: linear-gradient(180deg, #ffffff, #f8fbff);
    }

    .form-panel .form-control,
    .form-panel .form-select {
        border-radius: 12px;
        border: 1px solid #d8e0ea;
        padding: 0.68rem 0.8rem;
        box-shadow: none;
    }

    .form-panel .form-control:focus,
    .form-panel .form-select:focus {
        border-color: #b85c38;
        box-shadow: 0 0 0 0.18rem rgba(184, 92, 56, 0.15);
    }

    .btn-custom-primary {
        background: linear-gradient(135deg, #b85c38, #d58b5b);
        border: none;
        border-radius: 12px;
        color: #fff;
        font-weight: 700;
        padding: 0.68rem 1rem;
        box-shadow: 0 12px 18px rgba(184, 92, 56, 0.22);
    }

    .btn-custom-primary:hover {
        color: #fff;
        filter: brightness(1.03);
    }

    .table-actions {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-modern thead th {
        background: #243142;
        color: #fff;
        font-size: 0.92rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .table-modern tbody tr:hover {
        background: #fffaf5;
    }

    .table-modern td,
    .table-modern th {
        padding: 0.9rem 0.75rem;
        vertical-align: middle;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.3rem 0.55rem;
        background: #edf4ff;
        color: #304c73;
        font-size: 0.88rem;
        font-weight: 700;
    }
</style>

<div class="page-shell">
    <div class="page-heading">
        <div>
            <h2 class="page-title mb-1"><i class="fas fa-tags me-2"></i>Manage Book Categories</h2>
        </div>
        <span class="badge-soft"><i class="fas fa-shield-alt me-1"></i><?= htmlspecialchars($userRole) ?> access</span>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-tags"></i></div>
            <div class="stat-label">Total Categories</div>
            <div class="stat-value"><?= count($categories) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
            <div class="stat-label">Current Role</div>
            <div class="stat-value text-capitalize"><?= htmlspecialchars($userRole) ?></div>
        </div>
    </div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card-custom mb-4">
    <div class="card-header-custom">
        <div>
            <i class="fas fa-plus-circle me-2"></i> Add New Category
        </div>
        <span class="chip"><i class="fas fa-sparkles"></i> Quick Add</span>
    </div>
    <div class="card-body form-panel">
        <form method="POST" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Category Name</label>
                <input type="text" name="category_name" class="form-control" placeholder="e.g. Fiction, Science, Business" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="add_category" class="btn btn-custom-primary w-100">
                    <i class="fas fa-plus me-2"></i>Add Category
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <div>
            <i class="fas fa-list me-2"></i> Existing Categories
        </div>
        <span class="chip"><i class="fas fa-database"></i> Live List</span>
    </div>
    <div class="table-responsive">
        <table class="table table-modern align-middle text-center mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Created At</th>
                    <?php if ($userRole == 'admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="<?= $userRole == 'admin' ? 4 : 3 ?>">No categories found.</td></tr>
                <?php else: ?>
                    <?php $sn = 1; foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= $sn++ ?></td>
                            <td><span class="chip"><i class="fas fa-tag"></i><?= htmlspecialchars($cat['name']) ?></span></td>
                            <td><?= date('d M Y', strtotime($cat['created_at'])) ?></td>
                            <?php if ($userRole == 'admin'): ?>
                                <td class="table-actions">
                                    <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Delete this category? It cannot be used by any book.')">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<?php include '../Customize&Database/footer.php'; ?>