<?php
require_once __DIR__ . '../setDatabase.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}


function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['user_role'] !== $role && $_SESSION['user_role'] !== 'admin') {
        die("Access denied. You do not have permission to view this page.");
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if ($_SESSION['user_role'] === 'admin') {
            header('Location: ../finalProject/booknestonlinebookstoresystem/Admin/mainPage.php');
        } elseif ($_SESSION['user_role'] === 'staff') {
            header('Location: ../finalProject/booknestonlinebookstoresystem/Staff/staffMainPage.php');
        } else {
            header('Location: index.php');
        }
        exit;
    }
}
?>