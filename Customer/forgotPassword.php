<?php
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $token = generateToken();
        $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $update->execute([$token, $email]);
        $resetLink = "http://localhost/finalproject/booknestonlinebookstoresystem/Customer/resetPassword.php?token=$token";
        $subject = "Reset your BookNest password";
        $body = "Click <a href='$resetLink'>here</a> to reset your password. This link expires in 1 hour.";
        sendEmail($email, $subject, $body);
        $success = "Password reset link sent to your email.";
    } else {
        $error = "Email not found.";
    }
}
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h4 class="mb-0">Forgot Password</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                </form>
                <div class="mt-3 text-center"><a href="login.php">Back to Login</a></div>
            </div>
        </div>
    </div>
</div>
<?php include '../Customize&Database/footer.php'; ?>