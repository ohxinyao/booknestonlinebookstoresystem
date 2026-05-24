<?php
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
requireRole('admin');
include '../Customize&Database/header.php';

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingPayments = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'unpaid'")->fetchColumn();
$revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
?>
<h2>Admin Dashboard</h2>
<div class="row">
    <div class="col-md-3"><div class="card text-white bg-primary mb-3"><div class="card-body"><h5><?= $totalUsers ?></h5><p>Users</p></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-success mb-3"><div class="card-body"><h5><?= $totalBooks ?></h5><p>Books</p></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-warning mb-3"><div class="card-body"><h5><?= $totalOrders ?></h5><p>Orders</p></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-danger mb-3"><div class="card-body"><h5><?= $pendingPayments ?></h5><p>Pending Payments</p></div></div></div>
    <div class="col-md-3"><div class="card text-white bg-info mb-3"><div class="card-body"><h5>RM <?= number_format($revenue,2) ?></h5><p>Revenue</p></div></div></div>
</div>
<?php include '../Customize&Database/footer.php'; ?>