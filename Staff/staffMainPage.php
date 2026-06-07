<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('staff');
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

// 统计数据
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending' OR status = 'paid'")->fetchColumn();
$todayOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()")->fetchColumn();
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$lowStockBooks = $pdo->query("SELECT * FROM books WHERE stock <= min_stock ORDER BY stock ASC")->fetchAll();
$lowStockCount = count($lowStockBooks);

// 最近5条订单（包含顾客信息）
$recentOrders = $pdo->query("
    SELECT o.id, o.order_number, o.total_amount, o.status, o.payment_status, o.order_date, u.name as customer_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
    LIMIT 5
")->fetchAll();

// 当月销售额（已付款订单）
$monthRevenue = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0) as revenue
    FROM orders
    WHERE MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE()) AND payment_status = 'paid'
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
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: #198754;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
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
        margin-bottom: 1.5rem;
    }
    .card-header-custom {
        background: #2c3e50;
        color: white;
        padding: 0.75rem 1rem;
        font-weight: 600;
        border-radius: 16px 16px 0 0;
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
    .badge-status {
        border-radius: 30px;
        padding: 0.25rem 0.6rem;
        font-size: 0.7rem;
        font-weight: 500;
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
        color: white;
    }
    .quick-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .quick-action-btn {
        flex: 1;
        text-align: center;
        padding: 0.8rem;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e9edf2;
        transition: all 0.2s;
        text-decoration: none;
        color: #1e293b;
        font-weight: 500;
    }
    .quick-action-btn:hover {
        background: #ffffff;
        border-color: #198754;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .quick-action-btn i {
        display: block;
        font-size: 1.8rem;
        margin-bottom: 0.4rem;
        color: #198754;
    }
</style>

<div class="container-fluid px-0">
    <h2 class="mb-4">Staff Dashboard</h2>

    <!-- 统计卡片行 -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($pendingOrders) ?></div>
                <div class="stat-label">Pending / Paid Orders</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($todayOrders) ?></div>
                <div class="stat-label">Orders Today</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($totalBooks) ?></div>
                <div class="stat-label">Total Books</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($lowStockCount) ?></div>
                <div class="stat-label">Low Stock Books</div>
            </div>
        </div>
    </div>

    <!-- 快速操作区域 -->
    <div class="quick-actions">
        <a href="../Admin/manageBook.php" class="quick-action-btn">
            <i class="fas fa-book"></i>
            Books & Categories
        </a>
        <a href="staffManage.php" class="quick-action-btn">
            <i class="fas fa-box"></i>
            View All Orders
        </a>
        <a href="chat.php" class="quick-action-btn">
            <i class="fas fa-comments"></i>
            Customer Chats
        </a>
    </div>

    <!-- 低库存警告（若存在） -->
    <?php if ($lowStockCount > 0): ?>
        <div class="card-custom mb-4">
            <div class="card-header-custom">
                <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Alert
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-custom mb-0">
                    <thead>
                        <tr><th>ID</th><th>Title</th><th>Current Stock</th><th>Min Threshold</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lowStockBooks as $book): ?>
                        <tr class="table-warning">
                            <td><?= $book['id'] ?></td>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><span class="badge bg-danger"><?= $book['stock'] ?></span></td>
                            <td><?= $book['min_stock'] ?></td>
                            <td><a href="../Admin/manageBook.php?edit=<?= $book['id'] ?>" class="btn btn-sm btn-primary">Restock</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-success mb-4">
            <i class="fas fa-check-circle"></i> ✅ All books are sufficiently stocked.
        </div>
    <?php endif; ?>

    <!-- 最近订单卡片 + 当月销售额 -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-clock me-2"></i> Recent Orders
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="6" class="text-center">No orders yet</td></tr>
                        <?php else: foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_number']) ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td><?= date('d M H:i', strtotime($order['order_date'])) ?></td>
                                <td>RM <?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <span class="badge-status bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                 </div>
                                </td>
                                <td><?= ucfirst($order['payment_status']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-2 text-end border-top">
                    <a href="staffManage.php" class="btn btn-sm-primary">View All Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-chart-line me-2"></i> This Month's Revenue
                </div>
                <div class="card-body text-center">
                    <h2 class="mt-2">RM <?= number_format($monthRevenue, 2) ?></h2>
                    <p class="text-muted"><?= date('F Y') ?></p>
                    <hr>
                    <small><i class="fas fa-info-circle"></i> Based on paid orders only.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../Customize&Database/footer.php'; ?>