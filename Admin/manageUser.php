<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('admin');
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

if (isset($_GET['delete_customer'])) {
    $id = (int)$_GET['delete_customer'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = "Customer deleted successfully.";
    header("Location: manageUser.php");
    exit;
}

if (isset($_GET['delete_staff'])) {
    $id = (int)$_GET['delete_staff'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'staff'");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = "Staff deleted successfully.";
    header("Location: manageUser.php");
    exit;
}

if (isset($_GET['reset_staff'])) {
    $id = (int)$_GET['reset_staff'];
    $infoStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ? AND role = 'staff'");
    $infoStmt->execute([$id]);
    $staff = $infoStmt->fetch();
    if ($staff) {
        $staffName = $staff['name'];
        $staffEmail = $staff['email'];

        $newPassword = bin2hex(random_bytes(4)); // 8位随机密码
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET password = ?, must_change_password = 1 WHERE id = ? AND role = 'staff'");
        $updateStmt->execute([$hashed, $id]);

        $subject = "Your BookNest staff password has been reset";
        $body = "Dear $staffName,<br><br>Your new password is: <strong>$newPassword</strong><br><br>Please login and change your password immediately.<br><br>Best regards,<br>BookNest Admin";
        sendEmail($staffEmail, $subject, $body);

        $_SESSION['flash_success'] = "Password for <strong>$staffName</strong> has been reset and sent to their email.";
    } else {
        $_SESSION['flash_error'] = "Staff not found.";
    }
    header("Location: manageUser.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_user'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ? AND role = ?");
        $stmt->execute([$name, $email, $id, $role]);
        $_SESSION['flash_success'] = ucfirst($role) . " updated successfully.";
    } else {
        if ($role == 'staff') {
            $defaultPassword = '0123456789';
            $mustChange = 1;
            $subject = "Welcome to BookNest Staff";
            $body = "Dear $name,<br><br>Your staff account has been created.<br>Default password: <strong>$defaultPassword</strong><br><br>Please login and change your password immediately.<br><br>Login URL: <a href='http://localhost/finalproject/booknestonlinebookstoresystem/Staff/staffLogin.php'>http://localhost/finalproject/booknestonlinebookstoresystem/Staff/staffLogin.php</a><br><br>Best regards,<br>BookNest Admin";
        } else {
            $defaultPassword = '12345678';
            $mustChange = 0;
            $subject = "Welcome to BookNest";
            $body = "Dear $name,<br><br>Your customer account has been created.<br>Default password: <strong>$defaultPassword</strong><br><br>Please login and change your password.<br><br>Best regards,<br>BookNest Admin";
        }
        $hashed = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, email_verified, must_change_password) VALUES (?, ?, ?, ?, 1, ?)");
        $stmt->execute([$name, $email, $hashed, $role, $mustChange]);
        sendEmail($email, $subject, $body);
        $_SESSION['flash_success'] = ucfirst($role) . " added. Default password: $defaultPassword (sent to email)";
    }
    header("Location: manageUser.php");
    exit;
}

$customers = $pdo->query("SELECT id, name, email, created_at FROM users WHERE role = 'customer' ORDER BY id")->fetchAll();
$staffs    = $pdo->query("SELECT id, name, email, created_at FROM users WHERE role = 'staff' ORDER BY id")->fetchAll();
?>

<style>
    .table-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
    }

    .table-actions .btn {
        width: 95px;
        text-align: center;
        white-space: nowrap;
        padding: 6px 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        line-height: 1;
        vertical-align: middle;
        height: auto;
        box-sizing: border-box;
        border: none;
        font-size: 0.875rem;
        font-weight: 500;
        background-clip: padding-box;
    }

    .table-actions .btn.btn-warning,
    .table-actions .btn.btn-info,
    .table-actions .btn.btn-danger {
        line-height: 1;
    }

    .table-actions .btn i {
        font-size: 0.875rem;
        width: 1rem;
        line-height: 1;
        vertical-align: middle;
    }

    .table td,
    .table th {
        text-align: center;
        vertical-align: middle;
    }

    .card-custom {
        background: #ffffff;
        border: 1px solid #e9edf2;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .card-header-custom {
        background: #2c3e50;
        color: white;
        padding: 0.75rem 1rem;
        font-weight: 600;
        border-radius: 16px 16px 0 0;
    }

    .table-actions .btn-warning,
    .table-actions .btn-info {
        transform: translateY(0px);
    }

    .table-actions .btn-danger {
        transform: translateY(-2px);
    }
</style>

<h2>Manage Users</h2>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['flash_success'] ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['flash_error'] ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="card-custom">
    <div class="card-header-custom">
        <i class="fas fa-users me-2"></i> Customers
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No customers found.
    </div>
    </td>
    </tr>
<?php else: ?>
    <?php $customer_row = 1; ?>
    <?php foreach ($customers as $c): ?>
        <tr>
            <td><?= $customer_row++ ?></td>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
            <td class="table-actions">
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#userModal"
                    onclick="openModal('customer', <?= $c['id'] ?>, '<?= htmlspecialchars($c['name']) ?>', '<?= htmlspecialchars($c['email']) ?>')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <a href="?delete_customer=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this customer? This will remove all orders/reviews.')">
                    <i class="fas fa-trash-alt"></i> Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<div class="card-custom">
    <div class="card-header-custom">
        <i class="fas fa-user-tie me-2"></i> Staff
        <button class="btn btn-sm btn-light float-end" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openModal('staff', 0)">
            <i class="fas fa-plus-circle"></i> Add Staff
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($staffs)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No staff found.
    </div>
    </td>
    </tr>
<?php else: ?>
    <?php $staff_row = 1; ?>
    <?php foreach ($staffs as $s): ?>
        <tr>
            <td><?= $staff_row++ ?></td>
            <td><?= htmlspecialchars($s['name']) ?></td>
            <td><?= htmlspecialchars($s['email']) ?></td>
            <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
            <td class="table-actions">
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#userModal"
                    onclick="openModal('staff', <?= $s['id'] ?>, '<?= htmlspecialchars($s['name']) ?>', '<?= htmlspecialchars($s['email']) ?>')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <a href="?reset_staff=<?= $s['id'] ?>" class="btn btn-sm btn-info" onclick="return confirm('Reset password? A new random password will be generated and emailed to the staff.')">
                    <i class="fas fa-key"></i> Reset Pwd
                </a>
                <a href="?delete_staff=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this staff member?')">
                    <i class="fas fa-trash-alt"></i> Delete
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">User Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId">
                    <input type="hidden" name="role" id="userRoleHidden">
                    <div class="mb-2">
                        <label>Name</label>
                        <input type="text" name="name" id="userName" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" id="userEmail" class="form-control" required>
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
    function openModal(role, id, name = '', email = '') {
        document.getElementById('userId').value = id;
        document.getElementById('userName').value = name;
        document.getElementById('userEmail').value = email;
        document.getElementById('userRoleHidden').value = role;
        const title = (id > 0 ? 'Edit ' : 'Add ') + (role === 'staff' ? 'Staff' : 'Customer');
        document.getElementById('userModalTitle').innerText = title;
    }
</script>

<?php include '../Customize&Database/footer.php'; ?>