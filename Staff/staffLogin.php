<?php
header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Thu, 01 Jan 1970 00:00:00 GMT");

session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';

$alreadyLoggedIn = isset($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        if (!$user['email_verified']) {
            $error = "Please verify your email first.";
        } else {
            if ($user['role'] !== 'staff') {
                $error = "This login page is for staff only. Please use the <a href='/finalproject/booknestonlinebookstoresystem/Customer/login.php'>Customer Login</a> or <a href='/finalproject/booknestonlinebookstoresystem/Admin/adminLogin.php'>Admin Login</a>.";
            } else {
                if (isset($user['must_change_password']) && $user['must_change_password'] == 1) {
                    $_SESSION['force_password_change'] = true;
                    $_SESSION['temp_user_id'] = $user['id'];
                    header("Location: ../Customize&Database/changePassword.php?force=1");
                    exit;
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: staffMainPage.php");
                exit;
            }
        }
    } else {
        $error = "Invalid email or password.";
    }
}
include '../Customize&Database/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div style="background: #28a745; color: white; border-radius: 28px 28px 0 0; padding: 1.5rem; text-align: center;">
                <h4 class="mb-0">Staff Login</h4>
            </div>
            <div class="card-body">
                <?php if ($alreadyLoggedIn): ?>
                    <div class="alert alert-info">You are already logged in as <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>. If you wish to switch account, please <a href="../Customer/logout.php">logout</a> first.</div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">Show Password</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-3">Login</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="../Customer/forgotPassword.php" style="color: #28a745;">Forgot Password?</a>
                </div>
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