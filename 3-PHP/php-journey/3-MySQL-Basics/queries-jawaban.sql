-- ===================================================
-- JAWABAN LATIHAN — TABLE SISWA
-- ===================================================
-- Jawaban buat soal "LATIHAN" di bagian bawah queries.sql.
-- Tinggal jalanin di phpMyAdmin (tab SQL), langsung jadi.

USE toko_belajar;

DROP TABLE IF EXISTS siswa;

-- 1. Bikin table siswa
CREATE TABLE siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nilai INT NOT NULL,
    kelas VARCHAR(20) NOT NULL
);

-- 2. Insert minimal 4 baris data
INSERT INTO siswa (nama, nilai, kelas) VALUES
    ('Andi Pratama', 88, 'X IPA 1'),
    ('Budi Santoso', 72, 'X IPA 1'),
    ('Citra Dewi', 95, 'X IPA 2'),
    ('Dian Permata', 60, 'X IPA 2');

-- 3. SELECT siswa nilai di atas 75, urut dari tertinggi ke terendah
SELECT * FROM siswa
WHERE nilai > 75
ORDER BY nilai DESC;
