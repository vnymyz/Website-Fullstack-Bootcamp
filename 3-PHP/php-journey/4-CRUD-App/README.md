# Sesi 4 — CRUD App

Tujuan: PHP mulai "ngobrol" sama MySQL. Bikin Create, Read, Update, Delete (CRUD) penuh pakai `mysqli` + **prepared statement** dari awal (bukan diajarin versi rawan SQL Injection dulu baru dibenerin — langsung yang bener).

Ini sesi paling berat sejauh ini, gapapa dicicil lebih dari satu sesi belajar.

## Sambungan dari Sesi 3

Pakai database `toko_belajar` dan table `barang` yang udah dibikin di `3-MySQL-Basics/queries.sql`. Kalau table `barang` kosong/belum ada, buka lagi file itu, jalanin bagian CREATE TABLE + INSERT lewat phpMyAdmin dulu sebelum lanjut ke sini.

Table `barang` sekarang juga punya kolom `kategori_id` (FK ke table `kategori`, dari bagian "Preview Foreign Key" di Sesi 3) — CRUD app di sini udah disesuaikan, tiap form ada dropdown kategori (opsional, boleh "Tanpa Kategori"), dan `index.php` nampilin nama kategorinya lewat `LEFT JOIN`. Pastiin table `kategori` udah keisi datanya (ada di bagian bawah `queries.sql`), soalnya kalau kosong dropdown-nya bakal kosong.

## Cara Menjalankan (Laragon)

1. **Laragon** — Start All (Apache + MySQL nyala).
2. Pastiin database `toko_belajar` + table `barang` udah ada isinya (cek phpMyAdmin).
3. Buka browser:
   ```
   http://localhost/php-journey/4-CRUD-App/index.php
   ```

## Urutan Belajar

| Urutan | File | Yang Dipelajari |
|---|---|---|
| 1 | `config/db.php` | Koneksi ke MySQL pakai `mysqli_connect()`, pola file config terpisah |
| 2 | `index.php` | READ — `mysqli_prepare` + `mysqli_stmt_get_result`, loop tampilin data ke table HTML |
| 3 | `create.php` | CREATE — form POST, validasi, **prepared statement + bind_param**, redirect after POST |
| 4 | `edit.php` | UPDATE — ambil 1 row by id dulu buat isi form, baru update by id |
| 5 | `delete.php` | DELETE — kenapa harus lewat POST, bukan link GET |

**Cara belajar:**
1. Buka `config/db.php` dulu, ngerti kenapa file koneksi dipisah (biar gak nulis ulang di tiap file).
2. Buka `index.php`, perhatiin gimana hasil `SELECT` di-loop jadi baris table HTML, dan kenapa tiap value dibungkus `htmlspecialchars()`.
3. Coba tambah barang lewat `create.php`, refresh `index.php`, cek data beneran masuk ke database (buka juga di phpMyAdmin tab Browse buat ngecek).
4. Coba edit & hapus, perhatiin urutan: ambil data by id -> isi form -> submit -> update/delete by id -> redirect.
5. **Penting:** coba jalanin percobaan "serangan" di form: isi field nama dengan `' OR '1'='1` atau `<script>alert(1)</script>`. Karena pakai prepared statement + `htmlspecialchars()`, dua-duanya gagal nembus. Ini bukti kenapa dua hal ini wajib, bukan opsional.

## Konsep Kunci: Prepared Statement

Query pakai tanda `?` sebagai placeholder, nilai aslinya dikirim terpisah lewat `mysqli_stmt_bind_param()` — bukan digabung langsung ke string SQL. Ini yang mencegah SQL Injection.

```php
// AMAN — prepared statement
$stmt = mysqli_prepare($koneksi, "SELECT * FROM barang WHERE nama = ?");
mysqli_stmt_bind_param($stmt, "s", $nama);
mysqli_stmt_execute($stmt);

// BAHAYA — jangan pernah nulis kayak gini
// $query = "SELECT * FROM barang WHERE nama = '$nama'";
```

Kode contoh bahaya di atas **sengaja gak ada** di file manapun di folder ini — supaya gak ada versi "salah" yang bisa ke-copy gak sengaja.

## File di Folder Ini

```
4-CRUD-App/
  README.md
  config/
    db.php
  index.php          <- Read
  create.php         <- Create
  edit.php           <- Update
  delete.php         <- Delete
  latihan/
    PANDUAN.md        <- checkpoint closed-book: bikin CRUD "Buku" dari nol
```

## Checkpoint Sebelum Lanjut ke Sesi 5

Ini checkpoint closed-book (tanpa AI, tanpa nyontek file contoh) — lihat `latihan/PANDUAN.md` buat tugasnya.

Sebelum lanjut ke `5-Sessions-and-Auth/`, pastikan bisa jawab/lakuin ini tanpa buka catatan/AI:
- Jelasin kenapa prepared statement mencegah SQL Injection, bukan cuma "karena disuruh pakai".
- Jelasin kenapa delete harus lewat POST, bukan link `<a href="delete.php?id=1">`.
- Jelasin pola "redirect after POST" (`header("Location: ...")` + `exit`) dan kenapa penting (cegah submit dobel pas refresh).
- Selesein latihan CRUD "Buku" di `latihan/` sampai create/read/update/delete-nya jalan semua, pakai prepared statement.
