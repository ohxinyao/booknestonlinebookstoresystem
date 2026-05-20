<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireLogin();

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT n.*, o.order_number FROM notifications n JOIN orders o ON n.order_id = o.id WHERE n.user_id = ? ORDER BY n.created_at DESC");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll();
$update = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
$update->execute([$userId]);

include '../Customize&Database/header.php';
?>

<h2>My Notifications</h2>
<?php if (count($notifications) == 0): ?>
    <div class="alert alert-info">You have no notifications.</div>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($notifications as $notif): ?>
            <div class="list-group-item">
                <strong>Order #<?= htmlspecialchars($notif['order_number']) ?></strong><br>
                <?= htmlspecialchars($notif['message']) ?><br>
                <small class="text-muted"><?= date('d M Y H:i', strtotime($notif['created_at'])) ?></small>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include '../Customize&Database/footer.php'; ?>