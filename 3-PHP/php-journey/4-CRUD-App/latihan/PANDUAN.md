# Latihan Closed-Book — CRUD "Buku"

Ini checkpoint sebelum lanjut ke Sesi 5. Dikerjain closed-book: **tanpa AI, tanpa nyontek `index.php`/`create.php`/`edit.php`/`delete.php` yang di folder induk** — tapi boleh liat struktur foldernya doang buat ingetin pola.

## Tugas

Bikin CRUD baru dari nol buat entity **"Buku"**, struktur & pola sama kayak contoh `barang` (folder induk `4-CRUD-App/`).

1. Bikin table `buku` di database `toko_belajar` (lewat phpMyAdmin), kolom minimal:
   - `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
   - `judul` (VARCHAR)
   - `penulis` (VARCHAR)
   - `tahun_terbit` (INT)
   - `stok` (INT)

2. Bikin file-file berikut di folder ini (`latihan/`):
   - `index-buku.php` — tampilin semua buku dalam table
   - `create-buku.php` — form tambah buku baru
   - `edit-buku.php` — form edit buku (ambil data by id dulu)
   - `delete-buku.php` — hapus buku (WAJIB via POST, bukan link GET)

3. Syarat wajib (sama kayak contoh `barang`):
   - Semua query pakai **prepared statement** (`mysqli_prepare` + `mysqli_stmt_bind_param`). Gak boleh nyambung `$_POST`/`$_GET` langsung ke string SQL.
   - Semua output ke HTML pakai `htmlspecialchars()`.
   - Delete cuma bisa lewat form POST, bukan link `<a href="delete-buku.php?id=...">`.
   - Redirect setelah POST (create/update/delete) balik ke `index-buku.php`.

## Checkpoint

Kalau udah selesai, cek ke diri sendiri:
- Bisa jelasin kenapa prepared statement mencegah SQL Injection (bukan cuma "soalnya disuruh pakai").
- Coba isi form nama buku dengan `<script>alert(1)</script>` — pastiin gak ke-eksekusi pas ditampilin di `index-buku.php`.
- Coba akses `delete-buku.php?id=1` langsung lewat URL (GET) — pastiin gak ngefek/gak ada hapus.
