<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff');
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$staffId = $_SESSION['user_id'];
$customers = $pdo->query("
    SELECT DISTINCT u.id, u.name 
    FROM chat_messages c 
    JOIN users u ON (c.sender_id = u.id OR c.receiver_id = u.id)
    WHERE (c.sender_id = $staffId OR c.receiver_id = $staffId) AND u.role = 'customer'
")->fetchAll();

$selectedCustomer = $_GET['customer_id'] ?? ($customers[0]['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $msg = trim($_POST['message']);
    $receiver = (int)$_POST['receiver_id'];
    if ($msg !== '' && $receiver > 0) {
        $insert = $pdo->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $insert->execute([$staffId, $receiver, $msg]);
    }
    header("Location: chat.php?customer_id=$receiver");
    exit;
}

$messages = [];
if ($selectedCustomer) {
    $stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at ASC");
    $stmt->execute([$staffId, $selectedCustomer, $selectedCustomer, $staffId]);
    $messages = $stmt->fetchAll();
    $update = $pdo->prepare("UPDATE chat_messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?");
    $update->execute([$staffId, $selectedCustomer]);
}
?>

<h2>Customer Live Chat</h2>
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">Customers</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($customers as $c): ?>
                    <li class="list-group-item <?= $selectedCustomer == $c['id'] ? 'active' : '' ?>">
                        <a href="?customer_id=<?= $c['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($c['name']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">Chat with Customer</div>
            <div class="card-body" id="chatBox" style="height: 400px; overflow-y: auto;">
                <?php foreach ($messages as $msg): ?>
                    <div class="mb-2 <?= $msg['sender_id'] == $staffId ? 'text-end' : 'text-start' ?>">
                        <small class="text-muted"><?= date('H:i', strtotime($msg['created_at'])) ?></small>
                        <div class="d-inline-block p-2 rounded <?= $msg['sender_id'] == $staffId ? 'bg-primary text-white' : 'bg-light' ?>">
                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="card-footer">
                <form method="POST" id="chatForm">
                    <input type="hidden" name="receiver_id" value="<?= $selectedCustomer ?>">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Type reply..." required>
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