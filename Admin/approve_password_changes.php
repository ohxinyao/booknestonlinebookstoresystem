<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('admin');
require_once '../Customize&Database/function.php';
include '../Customize&Database/header.php';

if (isset($_GET['approve'])) {
    $request_id = (int)$_GET['approve'];
    $stmt = $pdo->prepare("SELECT user_id FROM password_change_requests WHERE id = ? AND status = 'pending'");
    $stmt->execute([$request_id]);
    $req = $stmt->fetch();
    if ($req) {
        $reset_token = bin2hex(random_bytes(32));
        $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE id = ?");
        $update->execute([$reset_token, $req['user_id']]);
        $userStmt = $pdo->prepare("SELECT email, name FROM users WHERE id = ?");
        $userStmt->execute([$req['user_id']]);
        $user = $userStmt->fetch();
        $resetLink = "http://localhost/finalproject/booknestonlinebookstoresystem/Customer/resetPassword.php?token=$reset_token";
        $subject = "Password change request approved";
        $body = "Dear {$user['name']},<br><br>Your password change request has been approved. Click <a href='$resetLink'>here</a> to set a new password. This link will expire in 1 hour.<br><br>If you did not request this, please ignore this email.";
        sendEmail($user['email'], $subject, $body);
        $approve = $pdo->prepare("UPDATE password_change_requests SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?");
        $approve->execute([$_SESSION['user_id'], $request_id]);

        $_SESSION['flash_success'] = "Request approved. User has been emailed a reset link.";
    } else {
        $_SESSION['flash_error'] = "Invalid request.";
    }
    header("Location: approve_password_changes.php");
    exit;
}

if (isset($_GET['reject'])) {
    $request_id = (int)$_GET['reject'];
    $stmt = $pdo->prepare("UPDATE password_change_requests SET status = 'rejected' WHERE id = ? AND status = 'pending'");
    $stmt->execute([$request_id]);
    $_SESSION['flash_success'] = "Request rejected.";
    header("Location: approve_password_changes.php");
    exit;
}

$requests = $pdo->query("SELECT pcr.*, u.name, u.email FROM password_change_requests pcr JOIN users u ON pcr.user_id = u.id WHERE pcr.status = 'pending' ORDER BY pcr.requested_at DESC")->fetchAll();
?>
<h2>Password Change Requests</h2>
<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['flash_success'] ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['flash_error'] ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
<?php if (count($requests) == 0): ?>
    <p>No pending requests.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Requested At</th>
                    <th class="text-nowrap">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php $rowNum = 1; ?>
            <?php foreach ($requests as $req): ?>
                <tr>
                    <td><?= $rowNum++ ?>   
                    <td><?= htmlspecialchars($req['name']) ?></td>
                    <td><?= htmlspecialchars($req['email']) ?></td>
                    <td><?= $req['requested_at'] ?></td>
                    <td>
                        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
                            <a href="?approve=<?= $req['id'] ?>" class="btn btn-sm btn-success px-3 py-2" onclick="return confirm('Approve this request?')">Approve</a>
                            <a href="?reject=<?= $req['id'] ?>" class="btn btn-sm btn-danger px-3 py-2" onclick="return confirm('Reject this request?')">Reject</a>
                        </div>
                     </td>
                 </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include '../Customize&Database/footer.php'; ?>