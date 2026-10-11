<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/validation.php';
check_auth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = validate_positive_int($_POST['product_id'] ?? null);
    $type = $_POST['type'] ?? '';
    $quantity = validate_positive_int($_POST['quantity'] ?? null);
    $notes = trim($_POST['notes'] ?? '');

    if ($product_id && in_array($type, ['IN', 'OUT']) && $quantity) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $product_id]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception('Produk tidak ditemukan.');
            }

            $currentStock = $product['stock'];

            if ($type === 'OUT' && $quantity > $currentStock) {
                throw new Exception("Stok tidak mencukupi! Stok saat ini: $currentStock");
            }

            $newStock = ($type === 'IN') ? ($currentStock + $quantity) : ($currentStock - $quantity);

            $updateStmt = $pdo->prepare('UPDATE products SET stock = :stock WHERE id = :id');
            $updateStmt->execute(['stock' => $newStock, 'id' => $product_id]);

            $logStmt = $pdo->prepare('
                INSERT INTO stock_movements (product_id, user_id, type, quantity, notes) 
                VALUES (:product_id, :user_id, :type, :quantity, :notes)
            ');
            $logStmt->execute([
                'product_id' => $product_id,
                'user_id'    => $_SESSION['user']['id'],
                'type'       => $type,
                'quantity'   => $quantity,
                'notes'      => $notes
            ]);

            $pdo->commit();
            $success = "Transaksi berhasil dicatat! Stok baru: $newStock";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    } else {
        $error = 'Input tidak valid. Pastikan jumlah bernilai positif.';
    }
}

$products = $pdo->query("SELECT id, name, sku, stock FROM products")->fetchAll();
$history = $pdo->query("
    SELECT sm.*, p.name AS product_name, u.username 
    FROM stock_movements sm 
    JOIN products p ON p.id = sm.product_id 
    JOIN users u ON u.id = sm.user_id 
    ORDER BY sm.created_at DESC LIMIT 10
")->fetchAll();

require_once __DIR__ . '/../../views/header.php';
?>
<h2>Transaksi Barang Masuk / Keluar</h2>

<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>

<div class="row">
    <div class="col-md-5">
        <div class="card p-3 mb-4">
            <h5>Input Stok</h5>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Pilih Produk</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= e($p['name']) ?> (Stok: <?= $p['stock'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipe Transaksi</label>
                    <select name="type" class="form-select" required>
                        <option value="IN">Barang Masuk (IN)</option>
                        <option value="OUT">Barang Keluar (OUT)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah (Quantity)</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100">Simpan Transaksi</button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <h5>10 Riwayat Transaksi Terakhir</h5>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>Produk</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $h): ?>
                <tr>
                    <td><?= e($h['created_at']) ?></td>
                    <td><?= e($h['product_name']) ?></td>
                    <td>
                        <span class="badge bg-<?= $h['type'] === 'IN' ? 'success' : 'danger' ?>">
                            <?= e($h['type']) ?>
                        </span>
                    </td>
                    <td><?= e($h['quantity']) ?></td>
                    <td><?= e($h['username']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>