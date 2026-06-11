<?php
session_start();
require_once '../Customize&Database/setDatabase.php';
require_once '../Customize&Database/access.php';
require_once '../Customize&Database/function.php';
requireLogin();

$order_id = $_GET['order_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();
if (!$order) {
    die("Invalid order.");
}
if ($order['payment_status'] == 'paid') {
    die("Payment already processed.");
}

$originalTotal = $order['total_amount'] + ($order['discount_amount'] ?? 0);
$discount = $order['discount_amount'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['payment_proof'])) {
    $uploadDir = '../assets/uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $fileName = uploadFile($_FILES['payment_proof'], $uploadDir, ['jpg', 'jpeg', 'png', 'pdf']);
    if ($fileName) {
        $update = $pdo->prepare("UPDATE orders SET payment_proof = ?, payment_status = 'paid', status = 'paid' WHERE id = ?");
        $update->execute([$fileName, $order_id]);
        $success = "Payment proof uploaded. Your order is now marked as paid. Staff will process it soon.";
    } else {
        $error = "Upload failed. Only JPG, PNG, PDF allowed.";
    }
}
include '../Customize&Database/header.php';
?>

<style>
    .file-preview {
        margin-top: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #dee2e6;
        display: none;
    }
    .file-preview img {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .file-preview iframe {
        width: 100%;
        height: 400px;
        border: none;
        border-radius: 8px;
    }
    .file-preview .preview-actions {
        margin-top: 10px;
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    .file-info {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 8px;
    }
</style>

<h2>Upload Payment Proof</h2>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Order #<?= htmlspecialchars($order['order_number']) ?></strong></p>
        <p>Subtotal: RM <?= number_format($originalTotal, 2) ?></p>
        <?php if ($discount > 0): ?>
            <p>Discount: - RM <?= number_format($discount, 2) ?></p>
        <?php endif; ?>
        <p><strong>Final Total: RM <?= number_format($order['total_amount'], 2) ?></strong></p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-7">
        <div class="alert alert-info h-100">
            <h5><i class="fas fa-university"></i> Bank Transfer Instructions (Option 1)</h5>
            <p>Please transfer the exact amount to the following bank account:</p>
            <ul class="mb-0">
                <li><strong>Bank Name:</strong> Maybank</li>
                <li><strong>Account Name:</strong> BookNest Sdn Bhd</li>
                <li><strong>Account Number:</strong> 5123-4567-8901</li>
                <li><strong>Reference:</strong> Your Order #<?= htmlspecialchars($order['order_number']) ?></li>
            </ul>
            <p class="mt-2 mb-0">After transferring, upload your payment receipt (screenshot or PDF) below.</p>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="fas fa-qrcode fa-3x text-secondary mb-2"></i>
                <h5 class="card-title">Scan QR Code (Option 2)</h5>
                <img src="/finalproject/booknestonlinebookstoresystem/Image/paymentPic.jpg" alt="Payment QR Code" class="img-fluid rounded border" style="width: 80%; max-width: 220px;">
                <p class="text-muted small mt-2">Use your banking app or TNG eWallet to scan</p>
            </div>
        </div>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= $success ?> <a href="orderHistory.php">Back to Orders</a></div>
<?php else: ?>
    <form method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="mb-3">
            <label>Payment Receipt (Screenshot or PDF file of payment transfer)</label>
            <input type="file" name="payment_proof" id="paymentProofFile" class="form-control" accept="image/jpeg,image/png,application/pdf" required>
            <div class="file-info" id="fileInfo"></div>
        </div>

        <div class="file-preview" id="filePreview">
            <div id="previewContent"></div>
            <div class="preview-actions">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearPreviewBtn">Clear & Select Again</button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" id="submitBtn">Upload Proof</button>
        <a href="orderHistory.php" class="btn btn-secondary">Cancel</a>
    </form>
    <?php if (isset($error)) echo "<div class='alert alert-danger mt-2'>$error</div>"; ?>
<?php endif; ?>

<script>
    const fileInput = document.getElementById('paymentProofFile');
    const previewContainer = document.getElementById('filePreview');
    const previewContent = document.getElementById('previewContent');
    const fileInfo = document.getElementById('fileInfo');
    const clearBtn = document.getElementById('clearPreviewBtn');
    const submitBtn = document.getElementById('submitBtn');

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function previewFile(file) {
        if (!file) {
            previewContainer.style.display = 'none';
            previewContent.innerHTML = '';
            fileInfo.innerHTML = '';
            return;
        }

        const fileType = file.type;
        const fileName = file.name;
        const fileSize = formatFileSize(file.size);
        fileInfo.innerHTML = `<i class="fas fa-file"></i> Selected: <strong>${escapeHtml(fileName)}</strong> (${fileSize})`;

        const reader = new FileReader();

        if (fileType.startsWith('image/')) {
            reader.onload = function(e) {
                previewContent.innerHTML = `
                    <div class="text-center">
                        <img src="${e.target.result}" alt="Payment Proof Preview">
                        <p class="mt-2 text-muted small">This is your uploaded file preview. Please verify it's correct before submitting.</p>
                    </div>
                `;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else if (fileType === 'application/pdf') {
            reader.onload = function(e) {
                previewContent.innerHTML = `
                    <div class="text-center">
                        <iframe src="${e.target.result}" title="PDF Preview"></iframe>
                        <p class="mt-2 text-muted small">PDF preview. Please verify it's correct before submitting.</p>
                    </div>
                `;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContent.innerHTML = `
                <div class="text-center text-warning">
                    <i class="fas fa-file fa-3x"></i>
                    <p>Preview not available for this file type.<br>File name: ${escapeHtml(fileName)}</p>
                </div>
            `;
            previewContainer.style.display = 'block';
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            previewFile(file);
        } else {
            previewContainer.style.display = 'none';
            previewContent.innerHTML = '';
            fileInfo.innerHTML = '';
        }
    });

    clearBtn.addEventListener('click', function() {
        fileInput.value = '';
        previewContainer.style.display = 'none';
        previewContent.innerHTML = '';
        fileInfo.innerHTML = '';
        fileInput.focus();
    });
</script>

<?php include '../Customize&Database/footer.php'; ?>