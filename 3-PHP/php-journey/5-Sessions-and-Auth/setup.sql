-- ===================================================
-- SETUP TABLE USERS — buat Sesi 5 (Sessions & Auth)
-- ===================================================
-- Jalanin di phpMyAdmin (tab SQL) sebelum buka register.php

USE toko_belajar;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Gak perlu INSERT manual di sini — password harus di-hash pakai
-- password_hash() dari PHP, bukan ditulis plaintext lewat SQL.
-- Bikin akunnya lewat form register.php aja.
