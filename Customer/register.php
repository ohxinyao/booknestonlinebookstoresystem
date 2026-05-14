<?php
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password_raw = $_POST['password'];
    
    $strength = validatePasswordStrength($password_raw);
    if ($strength !== true) {
        $error = $strength;
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        $token = generateToken();

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            $sql = "INSERT INTO users (name, email, password, verification_token) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $email, $password, $token])) {
                $verifyLink = "http://localhost/finalproject/booknestonlinebookstoresystem/Customer/emailVerify.php?token=$token";
                $subject = "Verify your BookNest account";
                $body = "Hello $name,<br>Click <a href='$verifyLink'>here</a> to verify your email address.";
                sendEmail($email, $subject, $body);
                $success = "Registration successful! Please check your email to verify your account.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Create an Account</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php else: ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" minlength="8" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">Show Password</button>
                            </div>
                            <small class="text-muted">Password must be at least 8 characters, include uppercase, number, and special character.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                    <div class="mt-3 text-center">
                        Already have an account? <a href="login.php">Login</a>
                    </div>
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