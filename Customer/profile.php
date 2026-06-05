<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
require_once '../Customize&Database/function.php';

requireLogin();

$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['flash_error'] = 'Your profile could not be found.';
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    if ($name === '') {
        $error = 'Full name is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($phone !== '' && !preg_match('/^\+?[0-9\s-]{7,15}$/', $phone)) {
        $error = 'Please enter a valid phone number.';
    } else {
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $checkStmt->execute([$email, $userId]);

        if ($checkStmt->rowCount() > 0) {
            $error = 'This email address is already used by another account.';
        } else {
            $updateStmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?');
            if ($updateStmt->execute([$name, $email, $phone, $address, $userId])) {
                $_SESSION['user_name'] = $name;
                $success = 'Your profile has been updated successfully.';
                $user['name'] = $name;
                $user['email'] = $email;
                $user['phone'] = $phone;
                $user['address'] = $address;
            } else {
                $error = 'Unable to save your profile changes. Please try again.';
            }
        }
    }
}

include '../Customize&Database/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="e.g. 0123456789">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Your shipping address">
                    </div>
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../Customize&Database/footer.php'; ?>
