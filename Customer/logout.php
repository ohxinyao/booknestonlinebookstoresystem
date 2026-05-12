<?php
session_start();
$role = $_SESSION['user_role'] ?? 'customer';
session_destroy();

if ($role == 'admin') {
    header("Location: ../Admin/adminLogin.php");
} 

elseif ($role == 'staff') {
    header("Location: ../Staff/staffLogin.php");
} 

else {
    header("Location: login.php");
}
exit;
?>