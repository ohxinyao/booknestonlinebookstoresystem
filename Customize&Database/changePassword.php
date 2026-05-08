<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $forceChange = isset($_GET['force']) || isset($_SESSION['force_password_change']);
} elseif (isset($_SESSION['temp_user_id'])) {
    $userId = $_SESSION['temp_user_id'];
    $forceChange = true;
} else {
    header("Location: ../Customer/login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $currentHash = $stmt->fetchColumn();

    if (!$forceChange) {
        $current = $_POST['current_password'];
        if (!password_verify($current, $currentHash)) {
            $error = "Current password is incorrect.";
        }
    }

    if (empty($error)) {
        if (password_verify($new, $currentHash)) {
            $error = "New password cannot be the same as the current password.";
        } elseif ($new !== $confirm) {
            $error = "New passwords do not match.";
        } elseif (strlen($new) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?");
            $update->execute([$hashed, $userId]);
            unset($_SESSION['force_password_change']);
            unset($_SESSION['temp_user_id']);
            $success = "Password changed successfully. Please login again.";
            session_destroy();
            header("Refresh: 2; url=../Customer/login.php");
            exit;
        }
    }
}

include '../Customize&Database/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h4 class="mb-0"><?= $forceChange ? 'Set Your New Password' : 'Change Password' ?></h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if (!$success): ?>
                    <form method="POST">
                        <?php if (!$forceChange): ?>
                            <div class="mb-3">
                                <label>Current Password</label>
                                <div class="input-group">
                                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">Show Password</button>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label>New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">Show Password</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">Show Password</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
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