<?php
date_default_timezone_set('Asia/Kuala_Lumpur');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/setDatabase.php';

if (isset($_SESSION['user_id'])) {
    $updateStmt = $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
    $updateStmt->execute([$_SESSION['user_id']]);
    if (!isset($_SESSION['cart_loaded'])) {
        $userId = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT book_id, quantity FROM user_cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        $dbCart = [];
        while ($row = $stmt->fetch()) {
            $dbCart[$row['book_id']] = $row['quantity'];
        }
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $book_id => $qty) {
                if (isset($dbCart[$book_id])) {
                    $dbCart[$book_id] += $qty;
                } else {
                    $dbCart[$book_id] = $qty;
                }
            }
        }
        $_SESSION['cart'] = $dbCart;
        $pdo->beginTransaction();
        try {
            $delete = $pdo->prepare("DELETE FROM user_cart WHERE user_id = ?");
            $delete->execute([$userId]);
            $insert = $pdo->prepare("INSERT INTO user_cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
            foreach ($dbCart as $book_id => $qty) {
                $insert->execute([$userId, $book_id, $qty]);
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Failed to sync cart on login: " . $e->getMessage());
        }
        $_SESSION['cart_loaded'] = true;
    }
}

$navbarColor = 'bg-dark';
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] == 'staff') {
        $navbarColor = 'bg-success';   
    } elseif ($_SESSION['user_role'] == 'admin') {
        $navbarColor = 'bg-danger';    
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookNest - Online Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/finalproject/booknestonlinebookstoresystem/Customize&Database/design.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark <?= $navbarColor ?>">
    <div class="container">
        <a class="navbar-brand" href="/finalproject/booknestonlinebookstoresystem/Customer/index.php"><i class="fas fa-book-open me-2"></i>BookNest</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/selectBook.php"><i class="fas fa-book"></i> Books</a>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'customer'): ?>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/shoppingCart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/orderHistory.php"><i class="fas fa-box"></i> My Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/wishList.php"><i class="fas fa-heart"></i> Wishlist</a></li>
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/chat.php">
                            <i class="fas fa-comments"></i> Live Chat
                            <?php
                            if (isset($_SESSION['user_id'])) {
                                $chatUnread = $pdo->prepare("SELECT COUNT(*) FROM chat_messages WHERE receiver_id = ? AND is_read = 0");
                                $chatUnread->execute([$_SESSION['user_id']]);
                                $unreadChat = $chatUnread->fetchColumn();
                                if ($unreadChat > 0) {
                                    echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">' . $unreadChat . '</span>';
                                }
                            }
                            ?>
                        </a>
                    </li>

                    <li class="nav-item position-relative">
                        <a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Customer/notification.php">
                            <i class="fas fa-bell"></i> Notifications
                            <?php
                            if (isset($_SESSION['user_id'])) {
                                $notifCount = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
                                $notifCount->execute([$_SESSION['user_id']]);
                                $unread = $notifCount->fetchColumn();
                                if ($unread > 0) {
                                    echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">' . $unread . '</span>';
                                }
                            }
                            ?>
                        </a>
                    </li>
                <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'staff'): ?>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Staff/staffMainPage.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Staff/bookManage.php"><i class="fas fa-book"></i> Manage Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Staff/staffManage.php"><i class="fas fa-box"></i> Orders</a></li>
                    <li class="nav-item position-relative">
                        <a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Staff/chat.php">
                            <i class="fas fa-comments"></i> Live Chat
                            <?php
                            if (isset($_SESSION['user_id'])) {
                                $chatUnread = $pdo->prepare("SELECT COUNT(*) FROM chat_messages WHERE receiver_id = ? AND is_read = 0");
                                $chatUnread->execute([$_SESSION['user_id']]);
                                $unreadChat = $chatUnread->fetchColumn();
                                if ($unreadChat > 0) {
                                    echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">' . $unreadChat . '</span>';
                                }
                            }
                            ?>
                        </a>
                    </li>
                <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/mainPage.php"><i class="fas fa-user-shield"></i> Admin</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/manageOrder.php"><i class="fas fa-box"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/saleReport.php"><i class="fas fa-chart-line"></i> Sales Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/manageUser.php"><i class="fas fa-users"></i> Manage Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/manageBook.php"><i class="fas fa-book"></i> Update Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="/finalproject/booknestonlinebookstoresystem/Admin/approve_password_changes.php"><i class="fas fa-check-circle"></i> Approve Password Changes</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_name'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_SESSION['user_name']) ?> (<?= $_SESSION['user_role'] ?>)
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/finalproject/booknestonlinebookstoresystem/Customer/profile.php">My Profile</a></li>
                            <li><a class="dropdown-item" href="/finalproject/booknestonlinebookstoresystem/Customize&Database/changePassword.php">Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/finalproject/booknestonlinebookstoresystem/Customer/logout.php">Logout</a></li>
                        </ul>
                    </li>
               <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown">
                            Login
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/finalproject/booknestonlinebookstoresystem/Customer/login.php">Customer Login</a></li>
                            <li><a class="dropdown-item" href="/finalproject/booknestonlinebookstoresystem/Admin/adminLogin.php">Login as admin</a></li>
                            <li><a class="dropdown-item" href="/finalproject/booknestonlinebookstoresystem/Staff/staffLogin.php">Login as staff</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/finalproject/booknestonlinebookstoresystem/Customer/register.php">Register as customer</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-4">
<div class="toast-container"></div>