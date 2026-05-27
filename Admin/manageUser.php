<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('admin');
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = "User deleted successfully.";
    header("Location: manageUser.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_user'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'] ?? 'active';

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$name, $email, $role, $id]);
        $_SESSION['flash_success'] = "User updated.";
    } 
    
    else {
        $defaultPassword = '12345678';
        $hashed = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, email_verified) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$name, $email, $hashed, $role]);
        $_SESSION['flash_success'] = "User added. Default password: $defaultPassword (user should change after login)";
    }
    header("Location: manageUser.php");
    exit;
}

$users = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id")->fetchAll();
?>

<h2>Manage Users</h2>
<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['flash_success'] ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#userModal" onclick="clearUserForm()">Add New User</button>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Registered</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= ucfirst($u['role']) ?></td>
                <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#userModal" onclick="editUser(<?= htmlspecialchars(json_encode($u)) ?>)">Edit</button>
                    <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user? This will remove all orders/reviews.')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">User Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId">
                    <div class="mb-2">
                        <label>Name</label>
                        <input type="text" name="name" id="userName" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" id="userEmail" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Role</label>
                        <select name="role" id="userRole" class="form-select">
                            <option value="customer">Customer</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save_user" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearUserForm() {
    document.getElementById('userId').value = '';
    document.getElementById('userName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userRole').value = 'customer';
}
function editUser(user) {
    document.getElementById('userId').value = user.id;
    document.getElementById('userName').value = user.name;
    document.getElementById('userEmail').value = user.email;
    document.getElementById('userRole').value = user.role;
}
</script>

<?php include '../Customize&Database/footer.php'; ?>