# Sesi 3 — MySQL Basics

Tujuan: paham dasar SQL (database, table, row, column) lewat phpMyAdmin, sebelum PHP mulai "ngobrol" sama database di Sesi 4.

Sesi ini **belum ada file PHP**. Murni latihan SQL di phpMyAdmin, semua query ditulis/disimpan di `queries.sql`.

## Analogi

Database = pantry/gudang besar tempat semua bahan disimpan.
Table = rak di dalam gudang, tiap rak isinya satu jenis barang (misal: rak "barang", rak "siswa").
Row = satu barang di rak itu.
Column = kategori info tentang barang itu (nama, harga, stok, dst).

## Cara Menjalankan (Laragon + phpMyAdmin)

1. Buka **Laragon**, klik **Start All** (Apache + MySQL nyala).
2. Buka phpMyAdmin: klik kanan icon Laragon di tray -> **phpMyAdmin**, atau akses langsung:
   ```
   http://localhost/phpmyadmin
   ```
3. Klik tab **SQL** di bagian atas.
4. Buka file `queries.sql` di code editor, copy query satu per satu (jangan sekaligus semua), paste ke kotak SQL di phpMyAdmin, klik **Go** / **Kirim**.
5. Lihat hasilnya di bawah, baca sambil jalanin.

## Urutan Belajar

| Urutan | Bagian di `queries.sql` | Yang Dipelajari |
|---|---|---|
| 1 | Bikin Database | `CREATE DATABASE`, `USE` |
| 2 | Bikin Table | `CREATE TABLE`, tipe data (`INT`, `VARCHAR`, `TEXT`, `DATE`, `BOOLEAN`, `DECIMAL`), `PRIMARY KEY`, `AUTO_INCREMENT` |
| 3 | Insert Data | `INSERT INTO ... VALUES` |
| 4 | Ambil Data | `SELECT`, `WHERE`, `ORDER BY`, `LIMIT` |
| 5 | Update Data | `UPDATE ... SET ... WHERE` |
| 6 | Hapus Data | `DELETE FROM ... WHERE` |
| 7 | Preview Foreign Key | konsep relasi antar table (baru kenalan, belum dipakai penuh) |

**Cara belajar:**
1. Jalanin query bikin database & table dulu, cek di sidebar kiri phpMyAdmin table-nya udah muncul.
2. Jalanin insert, cek tab **Browse** buat liat datanya masuk.
3. Coba tiap query SELECT satu-satu, perhatiin bedanya hasil sebelum & sesudah ditambahin `WHERE`/`ORDER BY`/`LIMIT`.
4. Coba ubah kondisi `WHERE` di query UPDATE/DELETE sendiri, liat efeknya ke data.
5. **PENTING**: UPDATE dan DELETE tanpa `WHERE` bakal ngenain SEMUA baris. Selalu double-check kondisi `WHERE`-nya sebelum klik Go.

## File di Folder Ini

```
3-MySQL-Basics/
  README.md          <- panduan ini
  queries.sql         <- semua query contoh + soal latihan di bagian bawah
```

## Latihan — Closed Book

Ada di bagian paling bawah `queries.sql` (section "LATIHAN"). Tutup catatan, jangan tanya AI:
1. Bikin table baru `siswa` (id, nama, nilai, kelas).
2. Insert minimal 4 baris data.
3. Tulis `SELECT` buat nampilin siswa nilai di atas 75, urut dari nilai tertinggi.

## Sambungan ke Sesi 4 (CRUD App)

Database `toko_belajar` dan table `barang` yang dibikin di sesi ini **bakal dipakai lagi** di `4-CRUD-App/` — bukan bikin database baru dari nol. PHP di sesi 4 bakal connect ke database ini lewat `config/db.php` (pakai `mysqli_connect()`), terus CRUD (create/read/update/delete) dikerjain ke table `barang` yang sama.

Jadi sebelum lanjut sesi 4, pastikan:
- MySQL di Laragon nyala.
- Database `toko_belajar` sudah ada (cek sidebar phpMyAdmin).
- Table `barang` sudah ada isinya minimal beberapa baris (dari langkah INSERT di `queries.sql`).

Kalau db/table ini gak ada pas mulai sesi 4, jalanin ulang bagian "Bikin Database" & "Bikin Table" & "Insert Data" di `queries.sql`.

## Checkpoint Sebelum Lanjut ke Sesi 4

Sebelum lanjut ke `4-CRUD-App/`, pastikan bisa jawab/lakuin ini tanpa buka catatan/AI:
- Jelasin beda `WHERE` sama `ORDER BY`.
- Jelasin kenapa `UPDATE`/`DELETE` tanpa `WHERE` itu bahaya.
- Jelasin fungsi `PRIMARY KEY` dan `AUTO_INCREMENT`.
- Bikin `CREATE TABLE` baru dari nol (bukan nyontek), minimal 4 kolom dengan tipe data berbeda-beda.
- Tulis `SELECT` dengan kombinasi `WHERE` + `ORDER BY` + `LIMIT` dari nol.
