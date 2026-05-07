<?php
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

$token = $_GET['token'] ?? '';
$token=trim($token);
$error = '';
$success = '';

if (empty($token)) {
    $error = "No reset token provided.";
} else {
    // 检查 token 是否有效且未过期
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE BINARY reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Invalid or expired token. Please request a new password reset link.";
    } else {
        // 处理新密码提交
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newPassword = $_POST['password'];
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $update->execute([$hashedPassword, $user['id']]);

            $success = "Your password has been reset successfully. <a href='login.php'>Login here</a>";
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
                            <input type="password" name="password" class="form-control" minlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../Customize&Database/footer.php'; ?>