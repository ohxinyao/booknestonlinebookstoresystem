<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../Customize&Database/access.php';
requireLogin();
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$userId = $_SESSION['user_id'];
$staffs = $pdo->query("SELECT id, name FROM users WHERE role = 'staff' ORDER BY name")->fetchAll();
$selectedStaff = $_GET['staff_id'] ?? ($staffs[0]['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    $receiver = (int)$_POST['receiver_id'];
    if ($msg !== '' && $receiver > 0) {
        $insert = $pdo->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $insert->execute([$userId, $receiver, $msg]);
    }
    header("Location: chat.php?staff_id=$receiver");
    exit;
}

$messages = [];
if ($selectedStaff) {
    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->execute([$userId, $selectedStaff, $selectedStaff, $userId]);
    $messages = $stmt->fetchAll();
    $update = $pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?");
    $update->execute([$userId, $selectedStaff]);
}
?>

<h2>Live Chat with Staff</h2>
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">Staff Members</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($staffs as $staff): ?>
                    <li class="list-group-item <?= $selectedStaff == $staff['id'] ? 'active' : '' ?>">
                        <a href="?staff_id=<?= $staff['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($staff['name']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">Chat with Staff</div>
            <div class="card-body" id="chatBox" style="height: 400px; overflow-y: auto;">
                <?php foreach ($messages as $msg): ?>
                    <div class="mb-2 <?= $msg['sender_id'] == $userId ? 'text-end' : 'text-start' ?>">
                        <small class="text-muted"><?= date('H:i', strtotime($msg['created_at'])) ?></small>
                        <div class="d-inline-block p-2 rounded <?= $msg['sender_id'] == $userId ? 'bg-primary text-white' : 'bg-light' ?>">
                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="card-footer">
                <form method="POST" id="chatForm">
                    <input type="hidden" name="receiver_id" value="<?= $selectedStaff ?>">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chatBox');
    chatBox.scrollTop = chatBox.scrollHeight;
    setInterval(function() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newChatBox = doc.getElementById('chatBox');
                if (newChatBox) {
                    const oldHeight = chatBox.scrollHeight;
                    chatBox.innerHTML = newChatBox.innerHTML;
                    if (chatBox.scrollHeight > oldHeight) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                }
            });
    }, 3000);
</script>

<?php include '../Customize&Database/footer.php'; ?>