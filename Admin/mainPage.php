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

$dailyStats = $pdo->query("
    SELECT DATE(order_date) as order_day, 
           COUNT(*) as order_count,
           SUM(total_amount) as daily_revenue
    FROM orders
    WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND payment_status = 'paid'
    GROUP BY DATE(order_date)
    ORDER BY order_day ASC
")->fetchAll();

$recentOrders = $pdo->query("
    SELECT order_number, status, payment_status, order_date
    FROM orders
    ORDER BY order_date DESC
    LIMIT 5
")->fetchAll();

$lowStockBooks = $pdo->query("
    SELECT title, stock, min_stock
    FROM books
    WHERE stock <= min_stock
    ORDER BY stock ASC
    LIMIT 5
")->fetchAll();

$topBooks = $pdo->query("
    SELECT title, sales
    FROM books
    ORDER BY sales DESC
    LIMIT 3
")->fetchAll();

$pendingPasswordRequests = $pdo->query("
    SELECT COUNT(*) FROM password_change_requests WHERE status = 'pending'
")->fetchColumn();
?>

<style>
    .stat-card {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s ease;
        height: 100%;
        border: 1px solid #e9edf2;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: #475569;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        border-color: #dee2e6;
    }
    .stat-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.2rem;
    }
    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #5b6e8c;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-custom {
        background: #ffffff;
        border: 1px solid #e9edf2;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .card-header-custom {
        background: #2c3e50;
        color: white;
        padding: 0.75rem 1rem;
        font-weight: 600;
    }
    .table-custom {
        margin-bottom: 0;
        background: #ffffff;
    }
    .table-custom th {
        background: #f8fafc;
        border-bottom: 1px solid #e9edf2;
        color: #1e293b;
        font-weight: 600;
    }
    .table-dark th {
        background-color: #2c3e50 !important;
        color: white;
    }
    .btn-sm-outline {
        border-radius: 30px;
        padding: 0.2rem 0.8rem;
        font-size: 0.75rem;
    }
    .btn-sm-primary {
        border-radius: 30px;
        padding: 0.2rem 0.8rem;
        font-size: 0.75rem;
        background: linear-gradient(135deg, #b85c38, #8f3f25);
        border: none;
        color: white;
    }
    .btn-sm-primary:hover {
        background: #9a4a2c;
    }
</style>

<div class="container-fluid px-0">
    <h2 class="mb-4">Admin Dashboard</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($totalUsers) ?></div>
                <div class="stat-label">Users</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($totalBooks) ?></div>
                <div class="stat-label">Books</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($totalOrders) ?></div>
                <div class="stat-label">Orders</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($pendingPayments) ?></div>
                <div class="stat-label">Pending Payments</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-number">RM <?= number_format($revenue, 0) ?></div>
                <div class="stat-label">Revenue</div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($pendingPasswordRequests) ?></div>
                <div class="stat-label">Password Req.</div>
                <a href="approve_password_changes.php" class="btn btn-sm-outline mt-2">Review</a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-chart-line me-2"></i> Orders & Revenue (Last 7 Days)
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-custom mb-0">
                        <thead class="table-dark">
                            <tr><th>Date</th><th>Orders</th><th>Revenue (RM)</th></tr>
                        </thead>
                        <tbody>
                        <?php 
                            $dateMap = [];
                            foreach ($dailyStats as $d) {
                                $dateMap[$d['order_day']] = ['count' => $d['order_count'], 'revenue' => $d['daily_revenue']];
                            }
                            for ($i = 6; $i >= 0; $i--):
                                $date = date('Y-m-d', strtotime("-$i days"));
                                $displayDate = date('d M', strtotime($date));
                                $count = $dateMap[$date]['count'] ?? 0;
                                $rev = $dateMap[$date]['revenue'] ?? 0;
                        ?>
                            <tr>
                                <td><?= $displayDate ?></td>
                                <td><?= $count ?></td>
                                <td><?= number_format($rev, 0) ?></td>
                            </tr>
                        <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alerts
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-custom mb-0">
                        <thead class="table-dark">
                            <tr><th>Book Title</th><th>Stock</th><th>Min Threshold</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($lowStockBooks)): ?>
                            <tr><td colspan="3" class="text-center text-success">✅ No low stock items</td></tr>
                        <?php else: foreach ($lowStockBooks as $book): ?>
                            <tr class="table-warning">
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td class="fw-bold text-danger"><?= $book['stock'] ?></td>
                                <td><?= $book['min_stock'] ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!empty($lowStockBooks)): ?>
                <div class="p-2 text-end border-top">
                    <a href="manageBook.php" class="btn btn-sm-primary">Manage Books</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-clock me-2"></i> Recent Orders
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-custom mb-0">
                        <thead class="table-dark">
                            <tr><th>Order #</th><th>Date</th><th>Status</th><th>Payment</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="4" class="text-center">No orders</td></tr>
                        <?php else: foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_number']) ?></td>
                                <td><?= date('d M H:i', strtotime($order['order_date'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                 </td>
                                <td><?= ucfirst($order['payment_status']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-2 text-end border-top">
                    <a href="manageOrder.php" class="btn btn-sm-primary">View All Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-trophy me-2"></i> Top 3 Bestselling Books
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-custom mb-0">
                        <thead class="table-dark">
                            <tr><th>#</th><th>Book Title</th><th>Sales</th></tr>
                        </thead>
                        <tbody>
                        <?php if (empty($topBooks)): ?>
                            <tr><td colspan="3" class="text-center">No sales data</td></tr>
                        <?php else: $rank = 1; foreach ($topBooks as $book): ?>
                            <tr>
                                <td><?= $rank++ ?></td>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td class="fw-semibold"><?= number_format($book['sales']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../Customize&Database/footer.php'; ?>