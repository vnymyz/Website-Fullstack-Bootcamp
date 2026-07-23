-- ===================================================
-- SETUP LENGKAP — DATABASE + TABLE + DATA UNTUK 4-CRUD-APP
-- ===================================================
-- Jalanin sekali di phpMyAdmin (tab SQL), langsung siap dipakai
-- CRUD app di folder ini. Aman dijalanin ulang (pakai IF NOT EXISTS /
-- DROP TABLE dulu) kalau mau reset dari nol.

CREATE DATABASE IF NOT EXISTS toko_belajar;
USE toko_belajar;

-- Urutan drop: barang duluan (yang punya FK) baru kategori,
-- biar gak kena error foreign key constraint.
DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS kategori;

-- ---------------------------------------------------
-- TABLE: kategori
-- ---------------------------------------------------
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
);

INSERT INTO kategori (nama_kategori) VALUES
    ('Sembako'),
    ('Minuman'),
    ('Bumbu Dapur');

-- ---------------------------------------------------
-- TABLE: barang
-- ---------------------------------------------------
CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10, 2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    tanggal_masuk DATE,
    tersedia BOOLEAN DEFAULT TRUE,
    kategori_id INT,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id)
);

INSERT INTO barang (nama, deskripsi, harga, stok, tanggal_masuk, tersedia, kategori_id)
VALUES
    ('Beras 5kg', 'Beras putih kualitas premium, pulen dan wangi.', 65000, 20, '2026-01-10', TRUE, 1),
    ('Minyak Goreng 2L', 'Minyak goreng kelapa sawit, kemasan botol 2 liter.', 32000, 15, '2026-01-12', TRUE, 3),
    ('Telur 1kg', 'Telur ayam negeri segar, ukuran sedang.', 28000, 25, '2026-02-01', TRUE, 1),
    ('Gula 1kg', 'Gula pasir putih, kemasan plastik 1kg.', 15000, 30, '2026-02-05', TRUE, 1),
    ('Kopi Bubuk', 'Kopi bubuk robusta asli, tanpa campuran.', 12000, 8, '2026-02-10', TRUE, NULL);

-- ---------------------------------------------------
-- CEK HASIL
-- ---------------------------------------------------
SELECT b.id, b.nama, b.harga, b.stok, k.nama_kategori
FROM barang b
LEFT JOIN kategori k ON b.kategori_id = k.id
ORDER BY b.id ASC;
