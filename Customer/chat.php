<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../Customize&Database/access.php';
requireLogin();
include '../Customize&Database/header.php';

$userId = $_SESSION['user_id'];
$staffs = $pdo->query("
    SELECT u.id, u.name, 
           TIMESTAMPDIFF(SECOND, u.last_activity, NOW()) < 120 AS is_online,
           (SELECT COUNT(*) FROM chat_messages WHERE receiver_id = $userId AND sender_id = u.id AND is_read = 0) AS unread_count
    FROM users u
    WHERE u.role = 'staff'
    ORDER BY u.name
")->fetchAll();

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

$staffStatus = [];
$staffUnread = [];
foreach ($staffs as $s) {
    $staffStatus[$s['id']] = $s['is_online'];
    $staffUnread[$s['id']] = $s['unread_count'];
}
?>

<style>
    :root {
        --chat-primary: #b85c38;
        --chat-primary-dark: #8f3f25;
        --chat-primary-light: #e6a17e;
    }
    .chat-sidebar {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        height: 100%;
    }
    .chat-sidebar .list-group-item {
        border: none;
        border-left: 3px solid transparent;
        padding: 0.8rem 1.2rem;
        transition: all 0.2s;
        position: relative;
    }
    .chat-sidebar .list-group-item.active {
        background-color: #fef5e8;
        border-left-color: var(--chat-primary);
        color: var(--chat-primary);
    }
    .chat-sidebar .list-group-item.active a {
        color: var(--chat-primary) !important;
    }
    .chat-sidebar .list-group-item a {
        font-weight: 500;
        display: block;
        color: #333;
    }
    .unread-badge {
        background-color: #dc3545;
        color: white;
        border-radius: 50px;
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        font-weight: bold;
        margin-left: 8px;
    }
    .chat-main {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 500px;
    }
    .chat-header {
        background: linear-gradient(135deg, var(--chat-primary), var(--chat-primary-dark));
        color: white;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .chat-messages {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
        background: #f8fafc;
    }
    .chat-bubble {
        max-width: 75%;
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
    }
    .chat-bubble.sent {
        align-items: flex-end;
        margin-left: auto;
    }
    .chat-bubble.received {
        align-items: flex-start;
    }
    .bubble {
        padding: 0.6rem 1rem;
        border-radius: 18px;
        font-size: 0.95rem;
        line-height: 1.4;
        word-wrap: break-word;
        position: relative;
    }
    .sent .bubble {
        background: var(--chat-primary);
        color: white;
        border-bottom-right-radius: 4px;
    }
    .received .bubble {
        background: #e9ecef;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }
    .chat-sender {
        font-size: 0.7rem;
        margin-bottom: 0.2rem;
        font-weight: 600;
        color: #495057;
    }
    .sent .chat-sender {
        text-align: right;
        color: #f8b400;
    }
    .received .chat-sender {
        text-align: left;
        color: #198754;
    }
    .chat-time {
        font-size: 0.7rem;
        margin-top: 0.25rem;
        margin-left: 0.5rem;
        margin-right: 0.5rem;
        color: #6c757d;
    }
    .chat-footer {
        background: white;
        border-top: 1px solid #e9ecef;
        padding: 1rem;
    }
    .chat-footer .input-group {
        background: #f1f3f5;
        border-radius: 50px;
        padding: 0.2rem 0.2rem 0.2rem 1rem;
    }
    .chat-footer .form-control {
        border: none;
        background: transparent;
        box-shadow: none;
        padding: 0.6rem 0;
    }
    .chat-footer .btn {
        border-radius: 50px;
        padding: 0.4rem 1.2rem;
        background: var(--chat-primary);
        color: white;
    }
    .chat-footer .btn:hover {
        background: var(--chat-primary-dark);
        color: white;
    }
    .staff-avatar {
        width: 36px;
        height: 36px;
        background: #e9ecef;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    .staff-name {
        font-weight: 600;
    }
    @media (max-width: 768px) {
        .chat-bubble {
            max-width: 85%;
        }
    }
</style>

<div class="row g-4">
    <div class="col-md-4 col-lg-3">
        <div class="chat-sidebar">
            <div class="p-3 bg-light border-bottom">
                <i class="fas fa-users me-2"></i> <strong>Staff Members</strong>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($staffs as $staff): ?>
                    <li class="list-group-item <?= $selectedStaff == $staff['id'] ? 'active' : '' ?>">
                        <a href="?staff_id=<?= $staff['id'] ?>" class="text-decoration-none d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="staff-avatar">
                                    <i class="fas fa-user-tie text-secondary"></i>
                                </div>
                                <div>
                                    <div class="staff-name"><?= htmlspecialchars($staff['name']) ?></div>
                                    <small class="text-muted">
                                        <?php if ($staff['is_online']): ?>
                                            <span class="text-success">●</span> Online
                                        <?php else: ?>
                                            <span class="text-secondary">●</span> Offline
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            <?php if ($staff['unread_count'] > 0): ?>
                                <span class="unread-badge"><?= $staff['unread_count'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="col-md-8 col-lg-9">
        <div class="chat-main">
            <div class="chat-header">
                <div class="d-flex align-items-center">
                    <div class="staff-avatar bg-white bg-opacity-25">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <div class="fw-bold">
                            <?php 
                                $staffName = '';
                                foreach ($staffs as $s) {
                                    if ($s['id'] == $selectedStaff) {
                                        $staffName = $s['name'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($staffName ?: 'Select a staff');
                            ?>
                        </div>
                        <small>
                            <?php if ($selectedStaff && isset($staffStatus[$selectedStaff]) && $staffStatus[$selectedStaff]): ?>
                                <i class="fas fa-circle text-success" style="font-size: 0.5rem;"></i> Online now
                            <?php else: ?>
                                <i class="fas fa-circle text-secondary" style="font-size: 0.5rem;"></i> Offline
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="chatBox">
                <?php foreach ($messages as $msg): ?>
                    <?php 
                        $isSent = ($msg['sender_id'] == $userId);
                        $senderName = $isSent ? 'You' : htmlspecialchars($staffName);
                    ?>
                    <div class="chat-bubble <?= $isSent ? 'sent' : 'received' ?>">
                        <div class="chat-sender"><?= $senderName ?></div>
                        <div class="bubble">
                            <?= nl2br(htmlspecialchars($msg['message'])) ?>
                        </div>
                        <div class="chat-time">
                            <?= date('H:i', strtotime($msg['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="chat-footer">
                <form method="POST" id="chatForm">
                    <input type="hidden" name="receiver_id" value="<?= $selectedStaff ?>">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Type your message..." required autocomplete="off">
                        <button type="submit" class="btn">
                            <i class="fas fa-paper-plane"></i> Send
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chatBox');
    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
    scrollToBottom();

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
                        scrollToBottom();
                    }
                }
            });
    }, 3000);
</script>

<?php include '../Customize&Database/footer.php'; ?>