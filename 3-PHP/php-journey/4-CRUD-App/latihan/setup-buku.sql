-- ===================================================
-- SETUP TABLE BUKU — buat latihan CRUD "Buku"
-- ===================================================
-- Jalanin di phpMyAdmin (tab SQL) sebelum buka index-buku.php

USE toko_belajar;

DROP TABLE IF EXISTS buku;

CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    tahun_terbit INT NOT NULL,
    stok INT NOT NULL DEFAULT 0
);

INSERT INTO buku (judul, penulis, tahun_terbit, stok) VALUES
    ('Laskar Pelangi', 'Andrea Hirata', 2005, 12),
    ('Bumi Manusia', 'Pramoedya Ananta Toer', 1980, 5),
    ('Filosofi Teras', 'Henry Manampiring', 2018, 20),
    ('Negeri 5 Menara', 'Ahmad Fuadi', 2009, 8);
