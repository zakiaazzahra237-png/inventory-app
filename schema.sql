CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Categories
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Products
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Suppliers
CREATE TABLE IF NOT EXISTS suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Junction Product-Suppliers
CREATE TABLE IF NOT EXISTS product_suppliers (
    product_id INT NOT NULL,
    supplier_id INT NOT NULL,
    supplier_price DECIMAL(12,2),
    PRIMARY KEY (product_id, supplier_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
);

-- Tabel Stock Movements
CREATE TABLE IF NOT EXISTS stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Data Awal Akun (Password: password123)
INSERT INTO users (username, password_hash, role) VALUES 
('admin', '$2y$10$4y/S8x1p7B4P8j30yR0Sle5wzO6K08m.qA7eK4QY9pW9h1tL4/gW6', 'admin'),
('staff', '$2y$10$4y/S8x1p7B4P8j30yR0Sle5wzO6K08m.qA7eK4QY9pW9h1tL4/gW6', 'staff')
ON DUPLICATE KEY UPDATE id=id;

-- Data Sampel Kategori & Produk
INSERT INTO categories (name) VALUES ('Elektronik'), ('Aksesori'), ('Komputer')
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO products (category_id, sku, name, price, stock, minimum_stock) VALUES
(1, 'ELEC-001', 'Monitor LED 24 Inch', 1500000.00, 10, 3),
(2, 'ACC-001', 'Keyboard Mechanical', 450000.00, 5, 2),
(3, 'COMP-001', 'Mouse Wireless', 120000.00, 2, 5)
ON DUPLICATE KEY UPDATE id=id;