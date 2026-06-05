# StokBarang — Admin Panel Manajemen Stok

Aplikasi web admin untuk mengelola stok barang / produksi. Dibangun dengan PHP murni (tanpa framework), MySQL, dan Tailwind CSS menggunakan pola arsitektur **MVC**.

---

## Daftar Isi

- [Apa itu MVC?](#apa-itu-mvc)
- [Cara Kerja MVC di Project Ini](#cara-kerja-mvc-di-project-ini)
- [Struktur Folder](#struktur-folder)
- [Penjelasan Setiap File](#penjelasan-setiap-file)
- [Cara Setup](#cara-setup)
- [Akun Default](#akun-default)
- [Fitur](#fitur)

---

## Apa itu MVC?

**MVC** adalah singkatan dari **Model — View — Controller**, sebuah pola arsitektur perangkat lunak yang memisahkan aplikasi menjadi 3 bagian dengan tanggung jawab masing-masing:

```
┌─────────────────────────────────────────────────────────┐
│                      PENGGUNA (Browser)                 │
└──────────────────────────┬──────────────────────────────┘
                           │ HTTP Request
                           ▼
┌─────────────────────────────────────────────────────────┐
│                    CONTROLLER                           │
│  • Menerima request dari browser                        │
│  • Memutuskan logika apa yang perlu dijalankan          │
│  • Memanggil Model untuk ambil/simpan data              │
│  • Mengirim data ke View untuk ditampilkan              │
└────────────┬────────────────────────┬───────────────────┘
             │ panggil                │ kirim data ke
             ▼                        ▼
┌────────────────────┐    ┌───────────────────────────────┐
│      MODEL         │    │            VIEW               │
│  • Urusan database │    │  • Hanya tampilkan data       │
│  • Query SQL       │    │  • HTML + sedikit PHP         │
│  • Validasi data   │    │  • Tidak ada logika bisnis    │
│  • Return data     │    │  • Return HTML ke browser     │
└────────────────────┘    └───────────────────────────────┘
```

### Analogi Sederhana

Bayangkan sebuah **restoran**:

| Bagian MVC | Analogi Restoran | Tugasnya |
|---|---|---|
| **Controller** | Pelayan | Menerima pesanan, koordinasi dapur & meja |
| **Model** | Dapur | Proses data, masak makanan (olah database) |
| **View** | Penyajian di meja | Tampilkan hasil akhir ke pelanggan |

### Keuntungan MVC

- **Terpisah dan rapi** — perubahan tampilan tidak merusak logika, perubahan database tidak merusak tampilan
- **Mudah dikembangkan** — tiap bagian bisa dikerjakan secara independen
- **Mudah di-debug** — error lebih mudah dilacak karena tugasnya jelas
- **Dapat digunakan ulang** — satu Model bisa dipakai banyak Controller

---

## Cara Kerja MVC di Project Ini

Berikut alur lengkap ketika pengguna mengakses halaman **Daftar Stok Barang**:

```
Browser: GET /stock
       │
       ▼
[.htaccess]
  Semua request diarahkan ke public/index.php
       │
       ▼
[public/index.php]  ← Front Controller
  Inisialisasi session
  Daftarkan semua route
  Panggil Router::dispatch('/stock', 'GET')
       │
       ▼
[core/Router.php]
  Cocokkan URI '/stock' dengan daftar route
  Temukan → StockController@index
       │
       ▼
[app/controllers/StockController.php]
  __construct() → cek session (requireAuth)
  index() dipanggil:
    1. Ambil parameter ?search dari URL
    2. Panggil $stockModel->all($search)
    3. Panggil $stockModel->summary()
       │
       ▼
[app/models/Stock.php]
  all() → jalankan query SQL ke database
  Kembalikan array data stok
       │
       ▼
[StockController kembali]
  Kirim data ke view: $this->view('stock/index', $data)
       │
       ▼
[app/views/stock/index.php]
  Render HTML dengan data yang diterima
  Include layouts/main.php (sidebar, header, dll)
       │
       ▼
Browser: Tampilkan halaman daftar stok ✓
```

---

## Struktur Folder

```
mvc-stokbarang/
│
├── app/                        ← Kode utama aplikasi (MVC)
│   ├── controllers/            ← Controller: logika & koordinasi
│   ├── models/                 ← Model: interaksi database
│   └── views/                  ← View: template HTML
│       ├── auth/               ← Halaman login
│       ├── layouts/            ← Template shared (sidebar, header)
│       └── stock/              ← Halaman CRUD stok barang
│
├── core/                       ← Engine MVC (Router, base class)
├── config/                     ← Konfigurasi (koneksi database)
├── public/                     ← Entry point, satu-satunya folder publik
├── database/                   ← File SQL schema & seeder
└── .htaccess                   ← URL rewriting (semua → public/)
```

---

## Penjelasan Setiap File

### `public/index.php` — Front Controller

**Pintu masuk tunggal seluruh aplikasi.** Semua request HTTP masuk ke sini.

```
Tanggung jawab:
✓ Mulai session PHP
✓ Definisikan konstanta (ROOT_PATH, BASE_URL)
✓ Load semua core class
✓ Daftarkan seluruh route aplikasi
✓ Panggil Router untuk proses request
```

Kenapa hanya satu entry point? Agar semua request melewati proses yang sama (session, keamanan, routing) — tidak ada celah akses langsung ke file controller/model.

---

### `.htaccess` — URL Rewriting

**Pengatur lalu lintas URL.** Tanpa file ini, URL harus `index.php?url=stock`.

```apache
RewriteRule ^(.*)$ public/index.php [QSA,L]
```

Dengan file ini, URL menjadi bersih: `/stock`, `/stock/create`, `/login`.

---

### `core/Router.php` — Router

**Pencocok URL dengan Controller.** Menyimpan daftar route dan mencocokkan URL request.

```
Cara kerja:
1. Simpan daftar: [METHOD, '/path', 'Controller', 'method']
2. Terima URI dari request
3. Loop daftar route, cari yang cocok
4. Instantiate Controller, panggil method-nya
5. Jika tidak ada yang cocok → tampilkan 404
```

Contoh route yang didaftarkan:
```
GET  /stock         → StockController@index
POST /stock/store   → StockController@store
GET  /login         → AuthController@login
POST /login         → AuthController@authenticate
```

---

### `core/Controller.php` — Base Controller

**Kelas induk semua Controller.** Menyediakan method bantuan yang dipakai bersama.

| Method | Fungsi |
|---|---|
| `view($path, $data)` | Render file view dan kirim variabel ke dalamnya |
| `redirect($path)` | Redirect browser ke URL lain |
| `requireAuth()` | Cek session login, redirect ke /login jika belum |
| `isPost()` | Cek apakah request method POST |
| `post($key)` | Ambil data `$_POST` dengan aman |
| `get($key)` | Ambil data `$_GET` dengan aman |

---

### `core/Model.php` — Base Model

**Kelas induk semua Model.** Menyediakan koneksi database (PDO) dan method query.

```php
protected function query(string $sql, array $params = []): PDOStatement
```

Semua query menggunakan **prepared statement** — input pengguna tidak pernah langsung dimasukkan ke SQL, mencegah SQL Injection.

---

### `config/database.php` — Konfigurasi Database

**Pengaturan koneksi MySQL.** Berisi host, nama database, username, dan password.

Gunakan fungsi `getDB()` untuk mendapatkan instance PDO — menggunakan **singleton pattern** sehingga hanya dibuat satu koneksi per request.

```
Edit file ini jika:
- Password MySQL bukan kosong
- Nama database berbeda
- Host berbeda (bukan localhost)
```

---

### `app/models/User.php` — Model User

**Akses data tabel `users`.** Digunakan oleh AuthController untuk proses login.

| Method | Fungsi |
|---|---|
| `findByEmail($email)` | Cari user berdasarkan email untuk verifikasi login |
| `findById($id)` | Ambil data user berdasarkan ID session |

---

### `app/models/Stock.php` — Model Stock

**Akses data tabel `stocks`.** Inti dari aplikasi — semua operasi stok barang ada di sini.

| Method | Fungsi |
|---|---|
| `all($search)` | Ambil semua stok, dengan filter pencarian opsional |
| `find($id)` | Ambil satu data stok berdasarkan ID |
| `create($data)` | Tambah data stok baru ke database |
| `update($id, $data)` | Perbarui data stok yang ada |
| `delete($id)` | Hapus data stok |
| `kodeExists($kode, $excludeId)` | Cek apakah kode barang sudah dipakai (validasi unik) |
| `summary()` | Ambil ringkasan: total item, total stok, total nilai inventori |

---

### `app/controllers/AuthController.php` — Controller Login

**Mengelola proses autentikasi admin.**

| Method | Route | Fungsi |
|---|---|---|
| `login()` | `GET /login` | Tampilkan form login |
| `authenticate()` | `POST /login` | Verifikasi email & password, set session |
| `logout()` | `GET /logout` | Hapus session, redirect ke login |

Keamanan:
- Password diverifikasi dengan `password_verify()` — tidak pernah disimpan plain text
- Menggunakan flash session untuk pesan error antar redirect

---

### `app/controllers/StockController.php` — Controller Stok

**Mengelola seluruh operasi CRUD stok barang.**

| Method | Route | Fungsi |
|---|---|---|
| `index()` | `GET /stock` | Tampilkan daftar semua stok + fitur search |
| `create()` | `GET /stock/create` | Tampilkan form tambah barang |
| `store()` | `POST /stock/store` | Simpan barang baru ke database |
| `edit()` | `GET /stock/edit?id=` | Tampilkan form edit barang |
| `update()` | `POST /stock/update` | Perbarui data barang di database |
| `delete()` | `POST /stock/delete` | Hapus barang dari database |

Semua method CRUD memanggil `requireAuth()` via `__construct()` — tidak bisa diakses tanpa login.

---

### `app/views/layouts/header.php` — Header HTML

**Bagian `<head>` HTML yang dipakai semua halaman.** Berisi:
- Meta tag charset & viewport
- Tag `<title>` dinamis
- Load Tailwind CSS dari CDN
- Konfigurasi warna custom Tailwind

---

### `app/views/layouts/main.php` — Layout Utama

**Kerangka tampilan halaman admin** (setelah login). Berisi:
- Sidebar navigasi kiri (dengan highlight menu aktif)
- Header atas (nama halaman + tanggal)
- Area flash message (notifikasi sukses)
- Area konten utama (`$content`)
- Footer tag HTML

Halaman seperti `stock/index.php` mengisi variabel `$content` lalu me-require layout ini.

---

### `app/views/auth/login.php` — Halaman Login

**Tampilan form login.** Fitur:
- Form email + password
- Tampilkan pesan error dari flash session
- Toggle show/hide password
- Tidak menggunakan layout main.php (halaman standalone)

---

### `app/views/stock/index.php` — Daftar Stok

**Halaman utama manajemen stok.** Menampilkan:
- 3 kartu ringkasan (total item, total stok, nilai inventori)
- Form pencarian barang
- Tabel stok dengan warna merah untuk stok ≤ 10 (peringatan)
- Tombol Edit dan Hapus per baris

---

### `app/views/stock/create.php` & `edit.php` — Form Tambah/Edit

**Halaman form input data barang.** Keduanya menggunakan partial `_form.php` yang sama — tidak ada duplikasi kode.

---

### `app/views/stock/_form.php` — Partial Form (Reusable)

**Komponen form yang dipakai bersama** oleh halaman create dan edit. Berisi field:
- Kode Barang, Nama Barang, Kategori, Satuan
- Stok, Harga, Keterangan
- Tampilkan pesan error per field jika validasi gagal
- Isi ulang nilai lama jika ada error (tidak hilang saat redirect)

---

### `database/schema.sql` — Skema Database

**Definisi struktur database lengkap.** Berisi:
- `CREATE DATABASE` — buat database
- `CREATE TABLE users` — tabel admin
- `CREATE TABLE stocks` — tabel stok barang
- `INSERT` seed data — 1 admin default + 5 contoh barang

---

## Cara Setup

**1. Jalankan Laragon**

**2. Import database**
Buka phpMyAdmin → Import → pilih `database/schema.sql`

**3. Sesuaikan konfigurasi database** *(jika perlu)*
```php
// config/database.php
define('DB_USER', 'root');   // username MySQL kamu
define('DB_PASS', '');       // password MySQL kamu
```

**4. Akses aplikasi**
```
http://localhost/mvc-stokbarang
```

---

## Akun Default

| Field | Value |
|---|---|
| Email | `admin@stokbarang.com` |
| Password | `password` |

> Ganti password setelah pertama kali login.

---

## Fitur

- Login admin dengan autentikasi session
- Daftar stok barang dengan pencarian real-time
- Kartu ringkasan: total item, total stok, nilai inventori
- Tambah barang baru
- Edit data barang
- Hapus barang (dengan konfirmasi)
- Validasi input server-side dengan pesan error per field
- Peringatan visual stok rendah (≤ 10)
- Flash message notifikasi sukses/gagal
- Semua query menggunakan prepared statement (aman dari SQL Injection)

---

## Tech Stack

| Teknologi | Versi | Peran |
|---|---|---|
| PHP | 8.x | Backend, logika aplikasi |
| MySQL | 8.x | Database |
| Tailwind CSS | CDN | Styling UI |
| PDO | - | Koneksi & query database |
| Apache `.htaccess` | - | URL rewriting |
