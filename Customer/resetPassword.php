<?php
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

$token = $_GET['token'] ?? '';
$token = trim($token);
$error = '';
$success = '';
$loginLink = '';

if (empty($token)) {
    $error = "No reset token provided.";
} else {
    $stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE BINARY reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Invalid or expired token. Please request a new password reset link.";
    } else {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newPassword = $_POST['password'];

            // 验证密码强度
            $strength = validatePasswordStrength($newPassword);
            if ($strength !== true) {
                $error = $strength;
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                $update->execute([$hashedPassword, $user['id']]);

                if ($user['role'] == 'admin') {
                    $loginLink = "/finalproject/booknestonlinebookstoresystem/Admin/adminLogin.php";
                } elseif ($user['role'] == 'staff') {
                    $loginLink = "/finalproject/booknestonlinebookstoresystem/Staff/staffLogin.php";
                } else {
                    $loginLink = "/finalproject/booknestonlinebookstoresystem/Customer/login.php";
                }
                $success = "Your password has been reset successfully. <a href='$loginLink'>Login here</a>";
            }
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h4 class="mb-0">Reset Password</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php else: ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label>New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" minlength="8" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">Show Password</button>
                            </div>
                            <small class="text-muted">Password must be at least 8 characters, include uppercase, number, and special character.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
    } else {
        field.type = "password";
    }
}
</script>

<?php include '../Customize&Database/footer.php'; ?>