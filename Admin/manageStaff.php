<?php
session_start(); 
require_once '../Customize&Database/access.php';
requireRole('admin'); 
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'staff'");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = "Staff deleted successfully.";
    header("Location: manageStaff.php");
    exit;
}

if (isset($_GET['reset'])) {
    $id = (int)$_GET['reset'];
    $infoStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ? AND role = 'staff'");
    $infoStmt->execute([$id]);
    $staff = $infoStmt->fetch();
    if ($staff) {
        $staffName = $staff['name'];
        $staffEmail = $staff['email'];
        
        $newPassword = bin2hex(random_bytes(4)); 
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET password = ?, must_change_password = 1 WHERE id = ? AND role = 'staff'");
        $updateStmt->execute([$hashed, $id]);
        
        $subject = "Your BookNest staff password has been reset";
        $body = "Dear $staffName,<br><br>Your new password is: <strong>$newPassword</strong><br><br>Please login and change your password immediately.<br><br>Best regards,<br>BookNest Admin";
        sendEmail($staffEmail, $subject, $body);
        
        $_SESSION['flash_success'] = "Password for <strong>$staffName</strong> has been reset and sent to their email.";
    } else {
        $_SESSION['flash_success'] = "Staff not found.";
    }
    header("Location: manageStaff.php");
    exit;
}

$editStaff = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'staff'");
    $stmt->execute([(int)$_GET['edit']]);
    $editStaff = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = 'staff'; 
    if (isset($_POST['id']) && $_POST['id'] > 0) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ? AND role = 'staff'");
        $stmt->execute([$name, $email, $_POST['id']]);
        $_SESSION['flash_success'] = "Staff updated.";
    } 
    else{
        $defaultPassword = '0123456789';
        $hashed = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, email_verified, must_change_password) VALUES (?, ?, ?, ?, 1, 1)");
        $stmt->execute([$name, $email, $hashed, $role]);
        $subject = "Welcome to BookNest Staff";
        $body = "Dear $name,<br><br>Your staff account has been created.<br>Default password: <strong>$defaultPassword</strong><br><br>Please login and change your password immediately.<br><br>Login URL: <a href='http://localhost/finalproject/booknestonlinebookstoresystem/Staff/staffLogin.php'>http://localhost/finalproject/booknestonlinebookstoresystem/Staff/staffLogin.php</a><br><br>Best regards,<br>BookNest Admin";
        sendEmail($email, $subject, $body);
        
        $_SESSION['flash_success'] = "Staff added. Default password is: $defaultPassword (sent to staff email)";
    }
    header("Location: manageStaff.php");
    exit;
}

$staffs = $pdo->query("SELECT id, name, email, created_at FROM users WHERE role = 'staff' ORDER BY id")->fetchAll();
?>
<h2>Manage Staff</h2>
<?php
if (isset($_SESSION['flash_success'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['flash_success'] . "</div>";
    unset($_SESSION['flash_success']);
}
?>
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#staffModal" onclick="clearForm()">Add New Staff</button>
<table class="table table-bordered">
    <thead>
        <tr><th>Staff ID</th><th>Name</th><th>Email</th><th>Created At</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php $sn = 1; ?>
        <?php foreach ($staffs as $staff): ?>
        <tr>
            <td><?= $sn++ ?></td>   
            <td><?= htmlspecialchars($staff['name']) ?></td>
            <td><?= htmlspecialchars($staff['email']) ?></td>
            <td><?= $staff['created_at'] ?></td>
            <td>
                <a href="?edit=<?= $staff['id'] ?>" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#staffModal" onclick="editStaff(<?= htmlspecialchars(json_encode($staff)) ?>)">Edit</a>
                <a href="?reset=<?= $staff['id'] ?>" class="btn btn-sm btn-info" onclick="return confirm('Reset password? A new random password will be generated and emailed to the staff.')">Reset Pwd</a>
                <a href="?delete=<?= $staff['id'] ?>" class="btn btn-sm btn-danger confirm-delete">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="modal fade" id="staffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Staff Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="staffId">
                    <div class="mb-2">
                        <label>Name</label>
                        <input type="text" name="name" id="staffName" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" id="staffEmail" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearForm() {
    document.getElementById('staffId').value = '';
    document.getElementById('staffName').value = '';
    document.getElementById('staffEmail').value = '';
}
function editStaff(staff) {
    document.getElementById('staffId').value = staff.id;
    document.getElementById('staffName').value = staff.name;
    document.getElementById('staffEmail').value = staff.email;
}
</script>
<?php include '../Customize&Database/footer.php'; ?>