-- ===================================================
-- SESI 3 — MYSQL BASICS
-- ===================================================
-- Cara pakai: buka phpMyAdmin (lewat Laragon, klik kanan tray icon ->
-- "phpMyAdmin", atau akses http://localhost/phpmyadmin), masuk tab "SQL",
-- copy-paste query di bawah satu-satu, klik "Go" / "Kirim", lihat hasilnya.
--
-- Jangan copy-paste semua sekaligus di awal. Jalanin pelan-pelan sambil
-- baca komentarnya, biar ngerti tiap baris ngapain.

-- ---------------------------------------------------
-- 1. BIKIN DATABASE
-- ---------------------------------------------------
-- Database itu kayak "lemari besar" tempat semua tabel disimpan.

CREATE DATABASE IF NOT EXISTS toko_belajar;
USE toko_belajar;

-- ---------------------------------------------------
-- 2. BIKIN TABLE (CREATE TABLE)
-- ---------------------------------------------------
-- Table itu kayak "rak" di dalam lemari. Tiap kolom = jenis data,
-- tiap baris (row) = satu data/record.
--
-- Tipe data yang sering dipakai:
--   INT          -> angka bulat
--   VARCHAR(n)   -> teks pendek, max n karakter (nama, email, dll)
--   TEXT         -> teks panjang (deskripsi, isi artikel)
--   DATE         -> tanggal (format: YYYY-MM-DD)
--   BOOLEAN      -> true/false (sebenernya disimpan sebagai 0/1)
--   DECIMAL(m,d) -> angka desimal presisi (buat duga/harga, lebih akurat dari FLOAT)
--
-- PRIMARY KEY     -> kolom unik yang jadi "identitas" tiap baris, gak boleh kembar
-- AUTO_INCREMENT  -> nilainya nambah otomatis tiap ada baris baru (biasanya buat id)

CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10, 2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    tanggal_masuk DATE,
    tersedia BOOLEAN DEFAULT TRUE
);

-- Kenapa "deskripsi" pakai TEXT bukan VARCHAR? Karena isinya bisa panjang
-- (beberapa kalimat), gak dibatasin jumlah karakter kayak VARCHAR(n).

-- ---------------------------------------------------
-- 3. MASUKIN DATA (INSERT)
-- ---------------------------------------------------

INSERT INTO barang (nama, deskripsi, harga, stok, tanggal_masuk, tersedia)
VALUES
    ('Beras 5kg', 'Beras putih kualitas premium, pulen dan wangi.', 65000, 20, '2026-01-10', TRUE),
    ('Minyak Goreng 2L', 'Minyak goreng kelapa sawit, kemasan botol 2 liter.', 32000, 15, '2026-01-12', TRUE),
    ('Telur 1kg', 'Telur ayam negeri segar, ukuran sedang.', 28000, 0, '2026-02-01', FALSE),
    ('Gula 1kg', 'Gula pasir putih, kemasan plastik 1kg.', 15000, 30, '2026-02-05', TRUE),
    ('Kopi Bubuk', 'Kopi bubuk robusta asli, tanpa campuran.', 12000, 8, '2026-02-10', TRUE);

-- ---------------------------------------------------
-- 4. AMBIL DATA (SELECT)
-- ---------------------------------------------------

-- Ambil semua kolom, semua baris
SELECT * FROM barang;

-- Ambil kolom tertentu aja
SELECT nama, harga FROM barang;

-- WHERE -> filter baris berdasarkan kondisi
SELECT * FROM barang WHERE harga > 20000;
SELECT * FROM barang WHERE tersedia = TRUE;
SELECT * FROM barang WHERE stok = 0;

-- ORDER BY -> urutin hasil (ASC = kecil ke besar, DESC = besar ke kecil)
SELECT * FROM barang ORDER BY harga DESC;
SELECT * FROM barang ORDER BY nama ASC;

-- LIMIT -> batasin jumlah baris yang muncul
SELECT * FROM barang ORDER BY harga DESC LIMIT 3;

-- Gabungan WHERE + ORDER BY + LIMIT
SELECT * FROM barang WHERE tersedia = TRUE ORDER BY harga ASC LIMIT 2;

-- ---------------------------------------------------
-- 5. UPDATE DATA
-- ---------------------------------------------------
-- WAJIB pakai WHERE, kalau lupa WHERE, SEMUA baris ke-update!

UPDATE barang SET stok = 25 WHERE nama = 'Telur 1kg';
UPDATE barang SET tersedia = TRUE WHERE nama = 'Telur 1kg';

-- ---------------------------------------------------
-- 6. HAPUS DATA (DELETE)
-- ---------------------------------------------------
-- Sama kayak UPDATE, WAJIB pakai WHERE. Lupa WHERE = semua baris kehapus.

-- Contoh (jangan dijalanin dulu kalau masih mau dipakai datanya):
-- DELETE FROM barang WHERE nama = 'Kopi Bubuk';

-- ---------------------------------------------------
-- 7. PREVIEW: FOREIGN KEY (belum dipakai penuh, baru kenalan)
-- ---------------------------------------------------
-- Foreign key itu cara "menyambungkan" satu tabel ke tabel lain.
-- Contoh: tiap barang punya "kategori", kategori disimpan di tabel terpisah.

CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
);

-- kategori_id di tabel barang "nunjuk" ke id di tabel kategori.
-- Ini baru preview struktur doang, cara gabungin datanya (JOIN)
-- dipelajari nanti pas udah butuh, bukan di sesi ini.
--
-- ALTER TABLE barang ADD COLUMN kategori_id INT,
--   ADD FOREIGN KEY (kategori_id) REFERENCES kategori(id);

-- ===================================================
-- LATIHAN — CLOSED BOOK
-- ===================================================
-- Tutup catatan & jangan tanya AI. Tulis query kamu sendiri di bawah ini.
--
-- 1. Bikin table baru namanya "siswa" dengan kolom:
--    id (auto increment, primary key), nama (varchar), nilai (int), kelas (varchar)
--
-- 2. Insert minimal 4 baris data siswa bebas.
--
-- 3. Tulis SELECT buat nampilin siswa yang nilainya di atas 75,
--    diurutin dari nilai tertinggi ke terendah.
--
-- Tulis jawaban kamu di bawah baris ini:


