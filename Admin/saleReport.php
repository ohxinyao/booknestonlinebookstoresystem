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

$monthlyStats = $pdo->query("
    SELECT MONTH(order_date) as month, 
           COUNT(*) as orders,
           SUM(total_amount) as revenue
    FROM orders
    WHERE YEAR(order_date) = YEAR(CURDATE()) AND payment_status = 'paid'
    GROUP BY MONTH(order_date)
    ORDER BY month ASC
")->fetchAll();
$monthlyMap = [];
foreach ($monthlyStats as $m) {
    $monthlyMap[$m['month']] = ['orders' => $m['orders'], 'revenue' => $m['revenue']];
}

$periodDesc = '';
if ($filter === 'custom' && $startDate && $endDate) {
    $periodDesc = date('d M Y', strtotime($startDate)) . ' – ' . date('d M Y', strtotime($endDate));
} elseif ($filter === 'today') {
    $periodDesc = date('d M Y');
} elseif ($filter === 'week') {
    $periodDesc = 'Week of ' . date('d M Y', strtotime('monday this week')) . ' – ' . date('d M Y', strtotime('sunday this week'));
} elseif ($filter === 'month') {
    $periodDesc = date('F Y');
} elseif ($filter === 'year') {
    $periodDesc = date('Y');
}
?>

<style>
    .filter-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 1rem 1.2rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e9edf2;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
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
        margin-bottom: 0.25rem;
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
        border-bottom: none;
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
    .btn-primary {
        background: linear-gradient(135deg, #b85c38, #8f3f25);
        border: none;
        border-radius: 30px;
        padding: 0.35rem 1rem;
        font-size: 0.85rem;
    }
    .btn-outline-secondary {
        border-radius: 30px;
        padding: 0.35rem 1rem;
        font-size: 0.85rem;
    }
    .table-dark th {
        background-color: #2c3e50 !important;
        color: white;
    }

    @media print {
        nav.navbar, footer.footer, .filter-card, .btn, h2.mb-4, .toast-container {
            display: none !important;
        }
        body {
            background: white;
            padding: 0;
            margin: 0;
            font-size: 9pt;
        }
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }

        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #aaa;
        }
        .print-header h1 {
            font-size: 14pt;
            margin: 0;
        }
        .print-header p {
            font-size: 7pt;
            margin: 2px 0 0;
        }

        .stat-card {
            background: white !important;
            border: 1px solid #ccc !important;
            box-shadow: none !important;
            padding: 0.4rem !important;
            margin-bottom: 0 !important;
        }
        .stat-card::before {
            display: none !important;
        }
        .stat-number {
            font-size: 11pt !important;
            margin-bottom: 0;
        }
        .stat-label {
            font-size: 6pt !important;
        }

        .card-custom {
            background: white !important;
            border: 1px solid #ccc !important;
            box-shadow: none !important;
            margin-bottom: 6px !important;
        }
        .card-header-custom {
            background: #e9ecef !important;
            color: black !important;
            padding: 3px 6px !important;
            font-size: 8pt;
        }

        .table-custom, .table-custom th, .table-custom td {
            border: 1px solid #ccc !important;
            padding: 2px 4px !important;
            font-size: 7pt;
        }
        .table-custom th {
            background: #f1f1f1 !important;
        }
        .table-dark th {
            background: #e9ecef !important;
            color: black !important;
        }

        .row, .col-md-3, .col-md-6, .card-custom, table, tr, td, th {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .mb-4, .mb-3, .g-3, .g-4, .row {
            margin-bottom: 0 !important;
        }
        .row {
            margin-bottom: 2px !important;
        }

        @page {
            margin: 0.5cm;
            size: A4;
        }
  
        body::after {
            content: "Page " counter(page);
            position: fixed;
            bottom: 3px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #888;
            background: white;
        }
    }
</style>

<div class="print-header" style="display: none;">
    <h1>BookNest Sales Report</h1>
    <p>Period: <?= htmlspecialchars($periodDesc) ?> | Generated: <?= date('d M Y H:i') ?></p>
</div>

<div class="container-fluid px-0">
    <h2 class="mb-4">Sales Report</h2>

    <div class="filter-card">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">Period</label>
                <select name="filter" class="form-select" onchange="this.form.submit()">
                    <option value="today" <?= $filter=='today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= $filter=='week' ? 'selected' : '' ?>>This Week</option>
                    <option value="month" <?= $filter=='month' ? 'selected' : '' ?>>This Month</option>
                    <option value="year" <?= $filter=='year' ? 'selected' : '' ?>>This Year</option>
                    <option value="custom" <?= $filter=='custom' ? 'selected' : '' ?>>Custom Range</option>
                </select>
            </div>
            <div class="col-auto" id="dateRange" style="display: <?= $filter=='custom' ? 'flex' : 'none' ?>; gap: 8px;">
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
            <div class="col-auto ms-auto">
                <button type="button" class="btn btn-outline-secondary" onclick="window.print();">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </button>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?= number_format($orderStats['total_orders']) ?></div>
                <div class="stat-label">Paid Orders</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">RM <?= number_format($orderStats['total_revenue'] ?? 0, 0) ?></div>
                <div class="stat-label">Revenue</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">RM <?= number_format($orderStats['total_discount'] ?? 0, 0) ?></div>
                <div class="stat-label">Discount Given</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">RM <?= number_format($orderStats['avg_order_value'] ?? 0, 0) ?></div>
                <div class="stat-label">Avg Order Value</div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-chart-bar me-2"></i> Monthly Sales (<?= date('Y') ?>)
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-custom mb-0">
                <thead class="table-dark">
                    <tr><th>Month</th><th>Orders</th><th>Revenue (RM)</th></tr>
                </thead>
                <tbody>
                <?php for ($m = 1; $m <= 12; $m++):
                    $monthName = date('F', mktime(0, 0, 0, $m, 1));
                    $orders = $monthlyMap[$m]['orders'] ?? 0;
                    $revenue = $monthlyMap[$m]['revenue'] ?? 0;
                ?>
                    <tr>
                        <td><?= $monthName ?></td>
                        <td><?= number_format($orders) ?></td>
                        <td><?= number_format($revenue, 0) ?></td>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-crown me-2"></i> Top 10 Best Selling Books
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-custom mb-0">
                <thead class="table-dark">
                    <tr><th>Rank</th><th>Book Title</th><th>Quantity Sold</th><th>Revenue</th></tr>
                </thead>
                <tbody>
                <?php if (empty($topBooks)): ?>
                    <tr><td colspan="4" class="text-center">No data for selected period</td></tr>
                <?php else: $rank = 1; foreach ($topBooks as $book): ?>
                    <tr>
                        <td style="width: 70px;"><?= $rank++ ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= number_format($book['total_sold']) ?></td>
                        <td><strong>RM <?= number_format($book['revenue'], 2) ?></strong></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.querySelector('select[name="filter"]').addEventListener('change', function() {
        const dr = document.getElementById('dateRange');
        dr.style.display = this.value === 'custom' ? 'flex' : 'none';
    });
</script>

<?php include '../Customize&Database/footer.php'; ?>