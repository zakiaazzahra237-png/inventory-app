<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
check_auth();

$totalProducts = $pdo->query("SELECT COUNT(id) FROM products")->fetchColumn();
$totalValue = $pdo->query("SELECT SUM(stock * price) FROM products")->fetchColumn() ?? 0;
$lowStock = $pdo->query("SELECT COUNT(id) FROM products WHERE stock <= minimum_stock")->fetchColumn();

$movements = $pdo->query("
    SELECT 
        SUM(CASE WHEN type = 'IN' THEN quantity ELSE 0 END) AS total_in,
        SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END) AS total_out
    FROM stock_movements
")->fetch();

require_once __DIR__ . '/../views/header.php';
?>
<h2 class="mb-4">Dashboard Inventory</h2>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white p-3">
            <h5>Total Produk</h5>
            <h3><?= e($totalProducts) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white p-3">
            <h5>Nilai Inventory</h5>
            <h3>Rp <?= number_format($totalValue, 0, ',', '.') ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark p-3">
            <h5>Stok Rendah</h5>
            <h3><?= e($lowStock) ?></h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white p-3">
            <h5>Barang Masuk / Keluar</h5>
            <h3>+<?= e($movements['total_in'] ?? 0) ?> / -<?= e($movements['total_out'] ?? 0) ?></h3>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../views/footer.php'; ?>