<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
check_auth();

$search = trim($_GET['search'] ?? '');
$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$offset = ($page - 1) * $perPage;

$allowedSort = ['name' => 'p.name', 'price' => 'p.price', 'stock' => 'p.stock', 'sku' => 'p.sku'];
$sortBy = $_GET['sort'] ?? 'name';
$sortColumn = $allowedSort[$sortBy] ?? 'p.name';

$allowedDir = ['ASC', 'DESC'];
$dir = strtoupper($_GET['dir'] ?? 'ASC');
if (!in_array($dir, $allowedDir)) { $dir = 'ASC'; }

$where = ['1=1'];
$params = [];

if ($search !== '') {
    $where[] = '(p.name LIKE :search OR p.sku LIKE :search)';
    $params['search'] = "%$search%";
}

if ($category_id) {
    $where[] = 'p.category_id = :cat_id';
    $params['cat_id'] = $category_id;
}

$whereClause = implode(' AND ', $where);

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE $whereClause");
$stmtCount->execute($params);
$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $perPage);

$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        JOIN categories c ON c.id = p.category_id 
        WHERE $whereClause 
        ORDER BY $sortColumn $dir 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue(":$k", $v);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

require_once __DIR__ . '/../../views/header.php';
?>
<h2>Daftar Produk</h2>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Cari Nama / SKU..." value="<?= e($search) ?>">
    </div>
    <div class="col-md-3">
        <select name="category_id" class="form-select">
            <option value="">-- Semua Kategori --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <select name="sort" class="form-select">
            <option value="name" <?= $sortBy == 'name' ? 'selected' : '' ?>>Nama</option>
            <option value="price" <?= $sortBy == 'price' ? 'selected' : '' ?>>Harga</option>
            <option value="stock" <?= $sortBy == 'stock' ? 'selected' : '' ?>>Stok</option>
        </select>
    </div>
    <div class="col-md-1">
        <select name="dir" class="form-select">
            <option value="ASC" <?= $dir == 'ASC' ? 'selected' : '' ?>>ASC</option>
            <option value="DESC" <?= $dir == 'DESC' ? 'selected' : '' ?>>DESC</option>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>SKU</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= e($p['sku']) ?></td>
            <td><?= e($p['name']) ?></td>
            <td><?= e($p['category_name']) ?></td>
            <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
            <td><?= e($p['stock']) ?></td>
            <td>
                <?php if ($p['stock'] <= $p['minimum_stock']): ?>
                    <span class="badge bg-danger">Stok Rendah</span>
                <?php else: ?>
                    <span class="badge bg-success">Aman</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category_id=<?= $category_id ?>&sort=<?= $sortBy ?>&dir=<?= $dir ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require_once __DIR__ . '/../../views/footer.php'; ?>