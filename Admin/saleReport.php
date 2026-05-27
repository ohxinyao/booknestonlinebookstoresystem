<?php
session_start();
require_once '../Customize&Database/access.php';
requireRole('admin');
require_once '../Customize&Database/setDatabase.php';
include '../Customize&Database/header.php';

$filter = $_GET['filter'] ?? 'month';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$where = "1=1";
if ($filter === 'custom' && $startDate && $endDate) {
    $where = "order_date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
} elseif ($filter === 'today') {
    $where = "DATE(order_date) = CURDATE()";
} elseif ($filter === 'week') {
    $where = "YEARWEEK(order_date) = YEARWEEK(CURDATE())";
} elseif ($filter === 'month') {
    $where = "MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
} elseif ($filter === 'year') {
    $where = "YEAR(order_date) = YEAR(CURDATE())";
}

$orderStats = $pdo->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_revenue,
        SUM(discount_amount) as total_discount,
        AVG(total_amount) as avg_order_value
    FROM orders 
    WHERE $where AND payment_status = 'paid'
")->fetch();

$topBooks = $pdo->query("
    SELECT b.title, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue
    FROM order_items oi
    JOIN books b ON oi.book_id = b.id
    JOIN orders o ON oi.order_id = o.id
    WHERE $where AND o.payment_status = 'paid'
    GROUP BY oi.book_id
    ORDER BY total_sold DESC
    LIMIT 10
")->fetchAll();

$reportPeriod = '';
if ($filter === 'custom' && $startDate && $endDate) {
    $reportPeriod = date('d M Y', strtotime($startDate)) . ' – ' . date('d M Y', strtotime($endDate));
} elseif ($filter === 'today') {
    $reportPeriod = date('d M Y');
} elseif ($filter === 'week') {
    $reportPeriod = 'Week of ' . date('d M Y', strtotime('monday this week')) . ' – ' . date('d M Y', strtotime('sunday this week'));
} elseif ($filter === 'month') {
    $reportPeriod = date('F Y');
} elseif ($filter === 'year') {
    $reportPeriod = date('Y');
}
?>

<h2>Sales Report</h2>
<tr>
<form method="GET" class="row g-3 mb-4 align-items-end">
    <div class="col-auto">
        <select name="filter" class="form-select" onchange="this.form.submit()">
            <option value="today" <?= $filter=='today' ? 'selected' : '' ?>>Today</option>
            <option value="week" <?= $filter=='week' ? 'selected' : '' ?>>This Week</option>
            <option value="month" <?= $filter=='month' ? 'selected' : '' ?>>This Month</option>
            <option value="year" <?= $filter=='year' ? 'selected' : '' ?>>This Year</option>
            <option value="custom" <?= $filter=='custom' ? 'selected' : '' ?>>Custom Range</option>
        </select>
    </div>
    <div class="col-auto" id="dateRange" style="display: <?= $filter=='custom' ? 'flex' : 'none' ?>; gap: 10px;">
        <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
        <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
        <button type="submit" class="btn btn-primary">Apply</button>
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-primary" onclick="window.print();">
            <i class="fas fa-file-pdf"></i> Export to PDF
        </button>
    </div>
</form>

<div id="reportContent">
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5><?= number_format($orderStats['total_orders']) ?></h5>
                    <p>Paid Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5>RM <?= number_format($orderStats['total_revenue'] ?? 0, 2) ?></h5>
                    <p>Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5>RM <?= number_format($orderStats['total_discount'] ?? 0, 2) ?></h5>
                    <p>Discount Given</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <h5>RM <?= number_format($orderStats['avg_order_value'] ?? 0, 2) ?></h5>
                    <p>Avg Order Value</p>
                </div>
            </div>
        </div>
    </div>

    <h4>Top 10 Best Selling Books</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr><th>NO.</th><th>Book Title</th><th>Quantity Sold</th><th>Revenue</th></tr>
        </thead>
        <tbody>
        <?php if (empty($topBooks)): ?>
            <tr><td colspan="4" class="text-center">No data for selected period.</td></tr>
        <?php else: 
            $rank = 1;
            foreach ($topBooks as $book): ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><?= htmlspecialchars($book['title']) ?></td>
                <td><?= $book['total_sold'] ?></td>
                <td>RM <?= number_format($book['revenue'], 2) ?></td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<script>
    document.querySelector('select[name="filter"]').addEventListener('change', function() {
        const dr = document.getElementById('dateRange');
        dr.style.display = this.value === 'custom' ? 'flex' : 'none';
    });
</script>

<style media="print">
    nav.navbar, footer.footer, form, .btn, h2, .modal, .toast-container {
        display: none !important;
    }
    body {
        background: white;
        margin: 0;
        padding: 0;
        font-size: 12pt;
    }
    .container {
        max-width: 100%;
        margin: 0;
        padding: 0.5cm;
    }
    .print-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .print-header h1 {
        font-size: 24pt;
        margin-bottom: 5px;
        color: #2c3e50;
    }
    .print-header h2 {
        font-size: 18pt;
        color: #34495e;
    }
    .print-header p {
        font-size: 10pt;
        color: #7f8c8d;
        margin-top: 5px;
    }
    .card {
        break-inside: avoid;
        border: 1px solid #ddd;
        background: #fff !important;
        color: #000 !important;
        margin-bottom: 15px;
        box-shadow: none;
    }
    .card.text-white {
        color: #000 !important;
    }
    .card.bg-primary, .card.bg-success, .card.bg-warning, .card.bg-info {
        background: #f8f9fa !important;
        border-left: 4px solid;
    }
    .card.bg-primary { border-left-color: #007bff; }
    .card.bg-success { border-left-color: #28a745; }
    .card.bg-warning { border-left-color: #ffc107; }
    .card.bg-info { border-left-color: #17a2b8; }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    .table th {
        background-color: #343a40 !important;
        color: white !important;
    }
    .table-striped tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    h4 {
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }
    @page {
        size: A4;
        margin: 1.5cm;
    }
</style>

<?php include '../Customize&Database/footer.php'; ?>