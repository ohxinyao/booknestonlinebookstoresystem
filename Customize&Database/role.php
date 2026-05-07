<?php
session_start();
require_once __DIR__ . '/../Customize&Database/setDatabase.php';

function isLoggedIn() 
{
    return isset($_SESSION['user_id']);
}

function requireLogin() 
{
    if (!isLoggedIn()) 
    {
        header('Location: ../login.php');
        exit;
    }
}

function requireRole($role) 
{
    requireLogin();
    if ($_SESSION['user_role'] !== $role && $_SESSION['user_role'] !== 'admin') 
    {
        die("Access denied.");
    }
}

function redirectIfLoggedIn() 
{
    if (isLoggedIn()) 
    {
        if ($_SESSION['user_role'] === 'admin') header('Location: ../admin/mainPage.php');
        elseif ($_SESSION['user_role'] === 'staff') header('Location: ../staff/staffMainPage.php');
        else header('Location: ../Customer/index.php');
        exit;
    }
}
?>