-- ===================================================
-- SETUP BUKU & FAVORIT — buat Sesi 7
-- ===================================================
-- Jalanin di phpMyAdmin (tab SQL). Masih database toko_belajar yang sama,
-- table users dari Sesi 5/6 dipake lagi, gak dibikin ulang.

USE toko_belajar;

DROP TABLE IF EXISTS favorit;
DROP TABLE IF EXISTS buku;

-- ---------------------------------------------------
-- TABLE: buku (katalog, dikelola admin)
-- ---------------------------------------------------
CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    tahun_terbit INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    -- Nullable -- boleh kosong kalau admin belum pasang gambar.
    -- Isinya URL doang (link dari Unsplash/Google/dll), BUKAN file
    -- yang di-upload. Upload file beneran (validasi ekstensi/mime type)
    -- baru diajarin di sesi Security Hardening.
    gambar_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO buku (judul, penulis, tahun_terbit, stok, gambar_url) VALUES
    ('Laskar Pelangi', 'Andrea Hirata', 2005, 12, 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400'),
    ('Bumi Manusia', 'Pramoedya Ananta Toer', 1980, 5, 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400'),
    ('Filosofi Teras', 'Henry Manampiring', 2018, 20, 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400'),
    ('Negeri 5 Menara', 'Ahmad Fuadi', 2009, 8, NULL),
    ('Perahu Kertas', 'Dee Lestari', 2009, 10, NULL),
    ('Cantik Itu Luka', 'Eka Kurniawan', 2002, 6, NULL);

-- ---------------------------------------------------
-- TABLE: favorit (many-to-many antara users & buku)
-- ---------------------------------------------------
-- Kenapa butuh table sendiri, bukan kolom di users atau buku?
-- Karena 1 user bisa favoritin BANYAK buku, dan 1 buku bisa
-- difavoritin BANYAK user -- relasi many-to-many gak bisa
-- disimpen sebagai 1 kolom di salah satu table.
CREATE TABLE favorit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE,
    -- 1 user cuma boleh favoritin 1 buku yang sama sekali (gak boleh dobel)
    UNIQUE KEY unik_favorit (user_id, buku_id)
);

-- Cek hasilnya
SELECT * FROM buku;
