-- Database: mvc_stokbarang
CREATE DATABASE IF NOT EXISTS mvc_stokbarang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mvc_stokbarang;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS stocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(50) NOT NULL UNIQUE,
    nama_barang VARCHAR(150) NOT NULL,
    kategori VARCHAR(100),
    satuan VARCHAR(30) NOT NULL DEFAULT 'pcs',
    stok INT NOT NULL DEFAULT 0,
    harga DECIMAL(15,2) NOT NULL DEFAULT 0,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed: admin / admin123
INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@stokbarang.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE id=id;
-- password above = "password" (bcrypt). Change on first login.

-- Sample stock data
INSERT INTO stocks (kode_barang, nama_barang, kategori, satuan, stok, harga, keterangan) VALUES
('BRG-001', 'Besi Hollow 4x4', 'Bahan Baku', 'batang', 150, 85000, 'Besi hollow ukuran 4x4 panjang 6m'),
('BRG-002', 'Cat Besi Merah 1kg', 'Bahan Pendukung', 'kaleng', 80, 45000, 'Cat anti karat warna merah'),
('BRG-003', 'Baut M10 x 50mm', 'Komponen', 'pcs', 500, 1500, 'Baut hex head galvanis'),
('BRG-004', 'Plat Besi 3mm', 'Bahan Baku', 'lembar', 60, 320000, 'Plat besi 120x240cm tebal 3mm'),
('BRG-005', 'Elektroda Las RD-260', 'Bahan Pendukung', 'kotak', 40, 75000, 'Elektroda las 2.6mm isi 20 batang')
ON DUPLICATE KEY UPDATE id=id;
