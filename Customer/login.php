<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
redirectIfLoggedIn();

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
            if ($user['role'] !== 'customer') {
                $error = "This login page is for customers only. Please use the <a href='/finalproject/booknestonlinebookstoresystem/Admin/adminLogin.php'>Admin Login</a> or <a href='/finalproject/booknestonlinebookstoresystem/Staff/staffLogin.php'>Staff Login</a>.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: index.php");
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
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Welcome to BookNest!</h4>
            </div>
            <div class="card-body">
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
                    <button type="submit" class="btn btn-primary w-100 rounded-3">Login</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="forgotPassword.php">Forgot Password?</a> |
                    <a href="register.php">Create Account</a>
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