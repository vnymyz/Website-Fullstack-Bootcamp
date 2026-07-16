# Sesi 2 — Forms & Superglobals

Tujuan: paham cara PHP nerima data dari browser (form GET/POST), superglobals (`$_GET`, `$_POST`, `$_REQUEST`, `$_SERVER`), validasi input, dan bahaya XSS dasar.

## Cara Menjalankan

Sama seperti Sesi 1, lewat Laragon (Apache harus nyala). Akses:
```
http://localhost/php-journey/2-Forms-and-Superglobals/form.php
```
Mulai dari `form.php`, jangan buka `process.php` langsung tanpa submit form dulu (nanti `$_POST`/`$_GET`-nya kosong, itu wajar).

## Urutan Belajar

| Urutan | File | Yang Dipelajari |
|---|---|---|
| 1 | `form.php` | Form HTML method GET vs POST, kapan pakai yang mana |
| 2 | `process.php` | `$_GET`, `$_POST`, `$_REQUEST`, `$_SERVER`, `htmlspecialchars()`, kenalan sama XSS |
| 3 | `validation-demo.php` | Validasi (`empty()`, `trim()`, `filter_var`), pola sticky form, form submit ke diri sendiri |
| 4 | `latihan/kontak.php` | Latihan gabungan: bikin sendiri form kontak lengkap dengan validasi + sticky |

**Cara belajar:**
1. Buka `form.php`, coba submit lewat GET dulu — perhatikan URL berubah. Lalu coba lewat POST — URL gak berubah.
2. Di `process.php`, coba isi form dengan teks biasa dulu, lihat output `print_r()`. Setelah itu coba isi kolom nama dengan `<script>alert(1)</script>` — bandingin sebelum/sesudah paham fungsi `htmlspecialchars()`.
3. Di `validation-demo.php`, coba submit form kosong (lihat error muncul), lalu isi sebagian (lihat sticky value gak hilang), baru isi lengkap & benar.
4. Kerjakan `latihan/kontak.php` sendiri — ini closed-book, jangan minta AI isiin, ini buat ngecek kamu paham polanya bukan cuma nyontek.

## File yang Perlu Dibuat/Ada di Folder Ini

Sudah tersedia (tinggal dipelajari):
```
2-Forms-and-Superglobals/
  README.md
  form.php
  process.php
  validation-demo.php
  latihan/
    kontak.php          <- ada banyak TODO, isi & bangun sendiri
```

## Checkpoint Sebelum Lanjut ke Sesi 3

Sebelum lanjut ke `3-MySQL-Basics/`, pastikan bisa jawab/lakuin ini tanpa buka catatan/AI:
- Jelasin kapan sebaiknya pakai form GET, kapan pakai POST, kasih 1 contoh kasus masing-masing.
- Jelasin kenapa `htmlspecialchars()` penting, kasih contoh input yang berbahaya kalau gak di-escape.
- Jelasin apa itu "sticky form" dan kenapa penting buat UX.
- Selesein `latihan/kontak.php` sampai semua validasi (nama, email, pesan min 10 karakter) jalan bener, dan form tetap sticky pas ada error.
