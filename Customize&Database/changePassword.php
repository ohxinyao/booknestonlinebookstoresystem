<?php
session_start();
require_once 'setDatabase.php';
require_once 'access.php';

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

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userRole = $stmt->fetchColumn();

if ($userRole == 'admin' && !$forceChange) {
    $error = '';
    $success = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        $current = $_POST['current_password'];

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $currentHash = $stmt->fetchColumn();

        if (!password_verify($current, $currentHash)) {
            $error = "Current password is incorrect.";
        } elseif ($new !== $confirm) {
            $error = "New passwords do not match.";
        } elseif (strlen($new) < 6) {
            $error = "Password must be at least 6 characters.";
        } elseif (password_verify($new, $currentHash)) {
            $error = "New password cannot be the same as the current password.";
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$hashed, $userId]);
            $success = "Password changed successfully.";
        }
    }
    include 'header.php';
    ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">Change Password (Admin)</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label>Current Password</label>
                                <div class="input-group">
                                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">Show Password</button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>New Password</label>
                                <div class="input-group">
                                    <input type="password" name="new_password" id="new_password" class="form-control" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">Show Password  </button>
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
    <?php include 'footer.php';
    exit;
}

if ($forceChange) {
    $error = '';
    $success = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        if ($new !== $confirm) {
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
    include 'header.php';
    ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h4 class="mb-0">Set Your New Password</h4>
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
    <?php include 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check = $pdo->prepare("SELECT id FROM password_change_requests WHERE user_id = ? AND status = 'pending'");
    $check->execute([$userId]);
    if ($check->fetch()) {
        $error = "You already have a pending request. Please wait for admin approval.";
    } else {
        $token = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare("INSERT INTO password_change_requests (user_id, token) VALUES (?, ?)");
        $stmt->execute([$userId, $token]);
        $success = "Your request has been submitted. An admin will review it and send you a password reset link if approved.";
    }
}
include 'header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h4 class="mb-0">Request Password Change</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php elseif (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php else: ?>
                    <form method="POST">
                        <p>To change your password, you must request approval from an administrator. After approval, you will receive an email with a link to set a new password.</p>
                        <button type="submit" class="btn btn-primary w-100">Submit Request</button>
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
<?php include 'footer.php'; ?>