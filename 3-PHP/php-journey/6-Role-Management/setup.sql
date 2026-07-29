-- ===================================================
-- SETUP ROLE — buat Sesi 6 (Role Management)
-- ===================================================
-- Jalanin di phpMyAdmin (tab SQL). Nambah kolom "role" ke table users
-- yang udah dibikin di Sesi 5, gak bikin table baru.

USE toko_belajar;

-- Kalau kolom "role" udah pernah ditambahin sebelumnya dan mau reset,
-- jalanin dulu baris ini (hapus tanda komentar):
-- ALTER TABLE users DROP COLUMN role;

ALTER TABLE users ADD COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user';

-- Semua user yang udah register di Sesi 5 otomatis jadi role 'user'
-- (default di atas). Buat jadiin salah satu akun sebagai admin,
-- ganti 'username_kamu' di bawah sama username yang udah kamu daftarin:
--
-- UPDATE users SET role = 'admin' WHERE username = 'username_kamu';

-- Cek hasilnya
SELECT id, username, role FROM users;
