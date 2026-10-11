<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../helpers/escape.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php if (isset($_SESSION['user'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="/inventory-app/public/dashboard.php">Inventory System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/inventory-app/public/dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/inventory-app/public/products/index.php">Produk</a></li>
        <li class="nav-item"><a class="nav-link" href="/inventory-app/public/stock/movement.php">Transaksi Stok</a></li>
      </ul>
      <div class="d-flex align-items-center text-white">
        <span class="me-3">Halo, <strong><?= e($_SESSION['user']['username']) ?></strong> (<?= e(strtoupper($_SESSION['user']['role'])) ?>)</span>
        <a href="/inventory-app/public/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>
    </div>
  </div>
</nav>
<?php endif; ?>
<div class="container">