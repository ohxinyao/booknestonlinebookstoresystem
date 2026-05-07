<?php
require_once '../Customize&Database/setDatabase.php';
$token = $_GET['token'] ?? '';
if ($token) {
    $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE verification_token = ?");
    $stmt->execute([$token]);
    if ($stmt->rowCount()) {
        echo "<div class='container mt-5'><div class='alert alert-success'>Email verified successfully! You can now <a href='login.php'>login</a>.</div></div>";
    } else {
        echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid or expired token.</div></div>";
    }
} else {
    echo "<div class='container mt-5'><div class='alert alert-danger'>No token provided.</div></div>";
}
?>