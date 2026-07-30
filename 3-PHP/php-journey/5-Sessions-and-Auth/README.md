# Sesi 5 — Sessions & Auth

Tujuan: paham gimana PHP "inget" siapa yang lagi login (session), plus register/login/logout dengan password yang di-hash, bukan plaintext.

**Belum ada role/admin di sesi ini** — itu baru masuk Sesi 6. Di sini semua user yang login sama aja (belum ada bedanya admin vs user biasa).

## Analogi

Cookie session = "nomor meja" yang dikasih ke pengunjung pas pertama masuk resto. Server (`$_SESSION`) nyimpen data "meja nomor 7 pesen apa aja" di dapur, browser cuma bawa-bawa nomor mejanya doang lewat cookie. Selama browser masih buka & cookie-nya valid, server tau siapa yang lagi request.

## Sambungan dari Sesi 3 & 4

Masih database `toko_belajar` yang sama, tambah table baru `users`. Jalanin `setup.sql` di folder ini lewat phpMyAdmin dulu sebelum coba register.

## Cara Menjalankan

1. Laragon — Start All.
2. Jalanin `setup.sql` (bikin table `users`).
3. Buka browser:
   ```
   http://localhost/php-journey/5-Sessions-and-Auth/register.php
   ```

## Urutan Belajar

| Urutan | File | Yang Dipelajari |
|---|---|---|
| 1 | `setup.sql` | Table `users` — kenapa kolom `password` VARCHAR(255) bukan pendek (hash-nya panjang) |
| 2 | `register.php` | `password_hash()`, validasi konfirmasi password, cek username udah dipakai |
| 3 | `login.php` | `session_start()`, `password_verify()`, `$_SESSION['user_id']` |
| 4 | `includes/auth-check.php` | Pola reusable buat ngelindungin halaman |
| 5 | `dashboard.php` | Halaman protected — contoh pakai `auth-check.php` |
| 6 | `logout.php` | `session_unset()` + `session_destroy()` |

**Cara belajar:**
1. Register akun baru, cek di phpMyAdmin tab Browse table `users` — perhatiin kolom `password` isinya hash panjang (`$2y$10$...`), bukan password asli yang diketik.
2. Login pakai akun itu, harusnya masuk ke `dashboard.php`.
3. Coba akses `dashboard.php` di tab/browser lain yang belum login — harusnya ketendang ke `login.php`.
4. **Bug hunt sendiri**: buka `dashboard.php`, komentarin baris `require_once "includes/auth-check.php";`, refresh tanpa login. Lihat apa yang kejadian (halaman kebuka padahal belum login). Ini persis bug yang disebut di checkpoint sesi 6 nanti — kuncinya inget: **hilang 1 baris include aja, proteksi ilang total**.
5. Logout, coba klik tombol Back di browser buat balik ke dashboard — pastiin tetep ketendang ke login (karena session udah dihapus di server, bukan cuma di tampilan).

## Konsep Kunci

- `password_hash()` / `password_verify()` — satu arah, gak bisa dibalikin. Jangan pernah simpan password asli, sekalipun "cuma buat belajar".
- `session_start()` harus di baris paling atas, sebelum ada `echo`/HTML apapun — kalau telat manggil ini pas udah ada output, muncul error "headers already sent".
- Pola proteksi halaman: `require_once "includes/auth-check.php";` di baris pertama tiap file yang mau dilindungi. Manual, belum otomatis — itu alasan kenapa gampang lupa/ke-skip, makanya harus dibiasain dari awal nulis file baru.

### `unset()` vs `session_unset()` — jangan ketuker

Dua fungsi ini beda konteks, walau namanya mirip:

- **`unset($variabel)`** — fungsi PHP umum (bukan khusus session), buat ngehapus 1 variabel biasa dari memory. Contoh:
  ```php
  $nama = "Budi";
  unset($nama);
  echo $nama; // error "undefined variable", soalnya $nama udah gak ada
  ```
  Bisa dipake ke elemen array juga: `unset($_SESSION['username'])` — ini cuma ngehapus SATU key `username` dari `$_SESSION`, key lain (`user_id`, `role`, dst) masih ada.

- **`session_unset()`** — fungsi khusus session, ngosongin **SEMUA isi** `$_SESSION` sekaligus (setara `$_SESSION = []`), tapi session-nya sendiri (cookie, session ID di server) masih hidup/aktif. Beda sama `session_destroy()` yang ngehapus session-nya di server.

Di `logout.php`, dipake `session_unset()` (bukan `unset($_SESSION)`) karena maunya SEMUA data session ke-kosongin sekaligus, gak cuma 1 key doang. Kalau butuh hapus 1 data session spesifik aja (misal cuma mau lupain `role` doang tapi user tetep login), baru pakai `unset($_SESSION['role'])`.

## File di Folder Ini

```
5-Sessions-and-Auth/
  README.md
  setup.sql
  config/
    db.php
  includes/
    auth-check.php
  register.php
  login.php
  logout.php
  dashboard.php
```

## Checkpoint Sebelum Lanjut ke Sesi 6

Sebelum lanjut ke `6-Role-Management/`, pastikan bisa jawab/lakuin ini tanpa buka catatan/AI:
- Jelasin kenapa `password_hash()` dipakai, bukan nyimpen password asli.
- Jelasin apa yang kejadian kalau `auth-check.php` gak di-include di halaman protected (dari latihan bug hunt di atas).
- Jelasin bedanya `session_unset()` sama `session_destroy()`.
- Bikin 1 halaman baru dari nol (misal `profil.php`) yang protected pakai `auth-check.php`, tampilin `$_SESSION['username']`.
