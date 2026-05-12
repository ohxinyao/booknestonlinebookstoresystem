<?php
require_once '../Customize&Database/setDatabase.php';
$token = $_GET['token'] ?? '';
if ($token) {
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if ($user) {
        $update = $pdo->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = ?");
        $update->execute([$user['id']]);
        if ($user['role'] == 'admin') {
            $loginLink = "../Admin/admin_login.php";
        } elseif ($user['role'] == 'staff') {
            $loginLink = "../Staff/staff_login.php";
        } else {
            $loginLink = "login.php";
        }
        echo "<div class='container mt-5'><div class='alert alert-success'>Email verified successfully! You can now <a href='$loginLink'>login</a>.</div></div>";
    } else {
        echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid or expired token.</div></div>";
    }
} else {
    echo "<div class='container mt-5'><div class='alert alert-danger'>No token provided.</div></div>";
}
?>