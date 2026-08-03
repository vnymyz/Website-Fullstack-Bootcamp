# Sesi 7 — Buku & Favorite

Tujuan: fitur nyata pertama yang gabungin semua yang udah dipelajari — CRUD (Sesi 4), Auth (Sesi 5), Role (Sesi 6) — plus konsep baru: **relasi many-to-many** lewat table `favorit`. Buku ditampilin ke publik, user yang login bisa nge-like/favoritin buku, admin kelola katalog + liat statistik.

**Bukan Bootstrap dulu** — itu baru Sesi 8. Sesi ini masih CSS polos, fokusnya di fitur & relasi database, bukan styling framework.

## Sambungan dari Sesi 5 & 6

Masih database `toko_belajar`, table `users` dari Sesi 5/6 dipake lagi (gak dibikin ulang). Jalanin `setup.sql` di folder ini buat nambah 2 table baru: `buku` dan `favorit`.

## Cara Menjalankan

1. Laragon — Start All.
2. Jalanin `setup.sql` (bikin table `buku` + `favorit`, isi 6 buku contoh).
3. Buka browser:
   ```
   http://localhost/php-journey/7-Buku-dan-Favorite/index.php
   ```

## Kenapa Butuh Table `favorit` Sendiri

Ini konsep baru yang gak ada di sesi-sesi sebelumnya: **relasi many-to-many**.

- 1 user bisa favoritin BANYAK buku.
- 1 buku bisa difavoritin BANYAK user.

Gak bisa disimpen sebagai 1 kolom di `users` (user cuma bisa nyimpen 1 nilai per kolom) atau di `buku` (sama masalahnya). Makanya dibikin table ketiga, `favorit`, yang isinya cuma "pasangan" `user_id` + `buku_id`. Tiap baris di `favorit` artinya "user ini nge-like buku ini".

```sql
CREATE TABLE favorit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE,
    UNIQUE KEY unik_favorit (user_id, buku_id)
);
```

- `ON DELETE CASCADE` — kalau user atau buku-nya dihapus, baris favorit yang nunjuk ke situ ikut kehapus otomatis. Gak nyisain data "sampah" yang nunjuk ke sesuatu yang udah gak ada.
- `UNIQUE KEY (user_id, buku_id)` — 1 user cuma bisa favoritin 1 buku yang sama sekali doang, gak bisa nge-like dobel.

## Urutan Belajar

| Urutan | File | Yang Dipelajari |
|---|---|---|
| 1 | `setup.sql` | Table `buku` + `favorit`, foreign key `ON DELETE CASCADE`, `UNIQUE KEY` gabungan |
| 2 | `katalog.php` | `LEFT JOIN` buat tau buku mana yang udah difavoritin user yang lagi login |
| 3 | `favorit-toggle.php` | Like/unlike (insert/delete baris `favorit`), wajib login |
| 4 | `dashboard.php` | User liat daftar favoritnya sendiri, `JOIN favorit` ke `buku` |
| 5 | `admin/buku.php`, `buku-tambah.php`, `buku-edit.php` | CRUD admin buat buku (3 halaman terpisah: list, tambah, edit), plus `COUNT()` + `GROUP BY` buat itung jumlah favorit tiap buku |
| 6 | `admin/dashboard.php` | Statistik nambah: total buku, total favorit |
| 7 | `index.php` | Homepage baca dari database (buku terbaru), bukan statis lagi |

**Cara belajar:**
1. Buka `index.php` tanpa login — ada navbar Home/Buku/About, dan preview 3 buku terbaru dari database.
2. Klik "Buku" di navbar, buka `katalog.php` — coba klik tombol like, harusnya ketendang ke `login.php` (belum login).
3. Login/register, balik ke `katalog.php`, coba like beberapa buku — tombolnya berubah jadi "❤ Favorit" (warna solid).
4. Buka `dashboard.php`, buku yang tadi di-like harusnya nongol di "Buku Favorit Saya".
5. Klik like lagi di buku yang sama (di katalog) — harusnya ke-unlike (toggle), ilang dari dashboard.
6. Login sebagai admin, buka `admin/buku.php` — klik "+ Tambah Buku" (halaman sendiri), atau "Edit" di salah satu baris (halaman sendiri juga). Perhatiin kolom "Disukai" nunjukin angka jumlah favorit (bukan tombol like — admin cuma liat angkanya, gak ikutan nge-like).
7. Hapus buku yang pernah di-favoritin, cek table `favorit` di phpMyAdmin — baris yang nunjuk ke buku itu ikut ilang otomatis (`ON DELETE CASCADE`).

## Konsep Kunci

- **Many-to-many butuh table pivot/junction** (`favorit`) — bukan kolom tambahan di salah satu table yang udah ada.
- **Toggle like/unlike** — 1 tombol, 1 handler (`favorit-toggle.php`), logic-nya cek dulu "udah ada row favorit apa belom" baru mutusin INSERT atau DELETE.
- **"Harus login buat like" itu dicek di server** (`favorit-toggle.php` pakai `auth-check.php`), bukan cuma nyembunyiin tombolnya di `katalog.php`. Kalau yang login-nya dicek cuma di tampilan doang, orang bisa POST langsung ke `favorit-toggle.php` walau belum login — sama persis pelajaran "server-side gate" dari Sesi 6.
- **`ON DELETE CASCADE`** — cara database "beresin sendiri" data anak yang nunjuk ke data induk yang dihapus, daripada nulis manual `DELETE FROM favorit WHERE buku_id = ...` tiap kali mau hapus buku.

## Gambar Buku — Link URL, Bukan Upload File

Buku punya kolom `gambar_url` (nullable). Admin nambah/edit buku tinggal paste link gambar (dari Unsplash, Google Images, dll) ke field "URL Gambar", bukan upload file dari komputer.

Kenapa link doang, bukan upload file beneran (`$_FILES`)? Karena upload file butuh validasi ekstra (cek ekstensi file, cek MIME type, cek ukuran, jangan percaya nama file dari user) yang sengaja ditunda ke sesi **Security Hardening** nanti — biar gak keburu diajarin sebelum konsep keamanannya dijelasin lengkap. Untuk sekarang:

- Validasi di `admin/buku.php` cuma `filter_var($url, FILTER_VALIDATE_URL)` — mastiin itu bentuknya URL yang valid, bukan sembarang teks.
- Kolom boleh `NULL` (buku boleh gak punya gambar) — di `katalog.php`/`index.php`, buku tanpa gambar nampilin kotak abu-abu "Tanpa Gambar", gak error.

## File di Folder Ini

```
7-Buku-dan-Favorite/
  README.md
  setup.sql
  style.css
  config/
    db.php
  includes/
    auth-check.php
    admin-check.php
    admin-sidebar.php
  index.php           <- homepage, navbar Home/Buku/About + preview buku terbaru
  katalog.php          <- semua buku, tombol like (wajib login)
  about.php             <- halaman dummy
  favorit-toggle.php    <- handler like/unlike
  register.php / login.php / logout.php
  dashboard.php         <- user: daftar buku favoritnya
  admin/
    dashboard.php        <- statistik: users, buku, favorit
    buku.php              <- list buku + kolom jumlah favorit + hapus
    buku-tambah.php        <- form tambah buku (halaman sendiri)
    buku-edit.php           <- form edit buku (halaman sendiri)
    users.php
```

---

## Kodingan Lengkap (buat murid ngikutin)

### `setup.sql`
```sql
USE toko_belajar;

DROP TABLE IF EXISTS favorit;
DROP TABLE IF EXISTS buku;

CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    tahun_terbit INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO buku (judul, penulis, tahun_terbit, stok) VALUES
    ('Laskar Pelangi', 'Andrea Hirata', 2005, 12),
    ('Bumi Manusia', 'Pramoedya Ananta Toer', 1980, 5),
    ('Filosofi Teras', 'Henry Manampiring', 2018, 20),
    ('Negeri 5 Menara', 'Ahmad Fuadi', 2009, 8),
    ('Perahu Kertas', 'Dee Lestari', 2009, 10),
    ('Cantik Itu Luka', 'Eka Kurniawan', 2002, 6);

CREATE TABLE favorit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE,
    UNIQUE KEY unik_favorit (user_id, buku_id)
);
```

### `favorit-toggle.php` (baru total — logic like/unlike)
```php
<?php
require_once "includes/auth-check.php";
require_once "config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: katalog.php");
    exit;
}

$bukuId = isset($_POST['buku_id']) ? (int) $_POST['buku_id'] : 0;
$userId = (int) $_SESSION['user_id'];

if ($bukuId > 0) {
    $query = "SELECT id FROM favorit WHERE user_id = ? AND buku_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $sudahFavorit = mysqli_fetch_assoc($result);

    if ($sudahFavorit) {
        $query = "DELETE FROM favorit WHERE user_id = ? AND buku_id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
        mysqli_stmt_execute($stmt);
    } else {
        $query = "INSERT INTO favorit (user_id, buku_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
        mysqli_stmt_execute($stmt);
    }
}

$kembali = $_POST['kembali'] ?? 'katalog.php';
if (strpos($kembali, '://') !== false) {
    $kembali = 'katalog.php';
}
header("Location: $kembali");
exit;
```

### `katalog.php` (baru total — daftar buku publik + tombol like)
```php
<?php
session_start();

require_once "config/db.php";

$sudahLogin = isset($_SESSION['user_id']);
$userId = $sudahLogin ? (int) $_SESSION['user_id'] : 0;

$query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, b.stok, f.id AS favorit_id
          FROM buku b
          LEFT JOIN favorit f ON f.buku_id = b.id AND f.user_id = ?
          ORDER BY b.judul ASC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku - Toko Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-body">
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Toko Buku</a>
        <div class="navbar-links">
            <a href="index.php">Home</a>
            <a href="katalog.php" class="active">Buku</a>
            <a href="about.php">About</a>
        </div>
        <div class="navbar-right">
            <?php if ($sudahLogin): ?>
                <span class="navbar-user">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="dashboard.php" class="btn-dark">Dashboard</a>
                <a href="logout.php" class="btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-dark">Login</a>
                <a href="register.php" class="btn-outline">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="page-header">
        <h1>Daftar Buku</h1>
        <p>Klik ikon hati buat nandain buku favorit kamu.</p>
    </section>

    <section class="book-grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="book-card">
                <div class="book-title"><?= htmlspecialchars($row['judul']) ?></div>
                <div class="book-meta"><?= htmlspecialchars($row['penulis']) ?> &middot; <?= htmlspecialchars($row['tahun_terbit']) ?></div>
                <div class="book-meta">Stok: <?= (int) $row['stok'] ?></div>

                <?php if ($sudahLogin): ?>
                    <form action="favorit-toggle.php" method="POST" class="like-form">
                        <input type="hidden" name="buku_id" value="<?= (int) $row['id'] ?>">
                        <input type="hidden" name="kembali" value="katalog.php">
                        <?php if ($row['favorit_id']): ?>
                            <button type="submit" class="btn-like btn-like-active">&hearts; Favorit</button>
                        <?php else: ?>
                            <button type="submit" class="btn-like">&hearts; Favoritkan</button>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn-like">&hearts; Login buat Like</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </section>

    <footer class="footer">
        <p>Toko Buku &mdash; project latihan PHP Sesi 7.</p>
        <p>&copy; <?= date("Y") ?></p>
    </footer>
</body>
</html>
```

### `dashboard.php` (diubah dari Sesi 6 — nambah daftar buku favorit)
```php
<?php
require_once "includes/auth-check.php";

if ($_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}

require_once "config/db.php";

// <-- BARU: query buku favorit user ini, JOIN favorit ke buku
$query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit
          FROM favorit f
          JOIN buku b ON b.id = f.buku_id
          WHERE f.user_id = ?
          ORDER BY f.created_at DESC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User - Toko Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-wrap">
        <div class="card dashboard-info">
            <h1>Dashboard User</h1>
            <p>Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! Role kamu: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>

            <p>
                <a href="index.php" class="btn-dark">Home</a>
                <a href="katalog.php" class="btn-outline">Lihat Buku</a>
            </p>
            <p>
                <a href="logout.php" class="btn-outline">Logout</a>
            </p>
        </div>

        <!-- <-- BARU: daftar buku favorit, gak ada sama sekali di Sesi 6 -->
        <div class="card" style="max-width: 500px;">
            <h1>Buku Favorit Saya</h1>

            <?php if (mysqli_num_rows($result) === 0): ?>
                <p>Belum ada buku yang di-favoritin. <a href="katalog.php">Cari buku di sini</a>.</p>
            <?php else: ?>
                <ul class="favorit-list">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <li>
                            <strong><?= htmlspecialchars($row['judul']) ?></strong>
                            — <?= htmlspecialchars($row['penulis']) ?> (<?= htmlspecialchars($row['tahun_terbit']) ?>)
                            <form action="favorit-toggle.php" method="POST" style="display:inline;">
                                <input type="hidden" name="buku_id" value="<?= (int) $row['id'] ?>">
                                <input type="hidden" name="kembali" value="dashboard.php">
                                <button type="submit" class="btn-unfavorit">Hapus</button>
                            </form>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
```

### `admin/buku.php` versi awal (baru total — CRUD buku, admin only)
> **Catatan:** kode di bawah ini versi PERTAMA `admin/buku.php`, waktu form tambah/edit masih digabung 1 halaman sama table-nya. Sekarang formnya udah dipisah jadi `buku-tambah.php` dan `buku-edit.php` — lihat section "Update: Halaman Tambah/Edit Buku Dipisah" di bawah buat versi terbarunya. Blok ini dibiarin buat ngeliat perbandingan sebelum vs sesudah.
```php
<?php
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$errors = [];
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahunTerbit = trim($_POST['tahun_terbit'] ?? '');
    $stok = trim($_POST['stok'] ?? '');

    if (empty($judul)) { $errors[] = "Judul wajib diisi."; }
    if (empty($penulis)) { $errors[] = "Penulis wajib diisi."; }
    if (!is_numeric($tahunTerbit) || $tahunTerbit < 1000) { $errors[] = "Tahun terbit gak valid."; }
    if (!is_numeric($stok) || $stok < 0) { $errors[] = "Stok harus angka, gak boleh negatif."; }

    if (empty($errors)) {
        $query = "INSERT INTO buku (judul, penulis, tahun_terbit, stok) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssii", $judul, $penulis, $tahunTerbit, $stok);
        mysqli_stmt_execute($stmt);
        header("Location: buku.php?msg=created");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int) $_POST['id'];
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahunTerbit = trim($_POST['tahun_terbit'] ?? '');
    $stok = trim($_POST['stok'] ?? '');

    if (empty($judul)) { $errors[] = "Judul wajib diisi."; }
    if (empty($penulis)) { $errors[] = "Penulis wajib diisi."; }
    if (!is_numeric($tahunTerbit) || $tahunTerbit < 1000) { $errors[] = "Tahun terbit gak valid."; }
    if (!is_numeric($stok) || $stok < 0) { $errors[] = "Stok harus angka, gak boleh negatif."; }

    if (empty($errors)) {
        $query = "UPDATE buku SET judul = ?, penulis = ?, tahun_terbit = ?, stok = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssiii", $judul, $penulis, $tahunTerbit, $stok, $id);
        mysqli_stmt_execute($stmt);
        header("Location: buku.php?msg=updated");
        exit;
    } else {
        $editData = ['id' => $id, 'judul' => $judul, 'penulis' => $penulis, 'tahun_terbit' => $tahunTerbit, 'stok' => $stok];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus') {
    $id = (int) $_POST['id'];
    // Baris favorit yang nunjuk ke buku ini ikut kehapus otomatis (ON DELETE CASCADE)
    $query = "DELETE FROM buku WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: buku.php?msg=deleted");
    exit;
}

if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $query = "SELECT * FROM buku WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $editId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($result);
}

// <-- BARU: COUNT(f.id) + GROUP BY buat itung jumlah favorit tiap buku,
// admin cuma liat angkanya doang, gak ada tombol like buat admin.
$query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, b.stok, COUNT(f.id) AS jumlah_favorit
          FROM buku b
          LEFT JOIN favorit f ON f.buku_id = b.id
          GROUP BY b.id
          ORDER BY b.id ASC";
$result = mysqli_query($koneksi, $query);

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
$activePage = 'buku';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Buku - Toko Buku</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="content">
            <h1>Kelola Buku</h1>

            <?php if ($msg === 'created'): ?>
                <div class="alert">Buku berhasil ditambahkan.</div>
            <?php elseif ($msg === 'updated'): ?>
                <div class="alert">Buku berhasil diupdate.</div>
            <?php elseif ($msg === 'deleted'): ?>
                <div class="alert">Buku berhasil dihapus.</div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <ul class="alert-error">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" action="buku.php" class="form-buku">
                <input type="hidden" name="action" value="<?= $editData ? 'update' : 'tambah' ?>">
                <?php if ($editData): ?>
                    <input type="hidden" name="id" value="<?= (int) $editData['id'] ?>">
                <?php endif; ?>

                <label>Judul:</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($editData['judul'] ?? '') ?>">

                <label>Penulis:</label>
                <input type="text" name="penulis" value="<?= htmlspecialchars($editData['penulis'] ?? '') ?>">

                <label>Tahun Terbit:</label>
                <input type="number" name="tahun_terbit" value="<?= htmlspecialchars($editData['tahun_terbit'] ?? '') ?>">

                <label>Stok:</label>
                <input type="number" name="stok" value="<?= htmlspecialchars($editData['stok'] ?? '') ?>">

                <button type="submit"><?= $editData ? 'Update Buku' : 'Tambah Buku' ?></button>
                <?php if ($editData): ?>
                    <a href="buku.php">Batal Edit</a>
                <?php endif; ?>
            </form>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Disukai</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= htmlspecialchars($row['penulis']) ?></td>
                    <td><?= htmlspecialchars($row['tahun_terbit']) ?></td>
                    <td><?= (int) $row['stok'] ?></td>
                    <td><span class="favorit-count">&hearts; <?= (int) $row['jumlah_favorit'] ?></span></td>
                    <td>
                        <div class="table-actions">
                            <a href="buku.php?edit=<?= (int) $row['id'] ?>" class="btn btn-edit">Edit</a>
                            <form action="buku.php" method="POST" onsubmit="return confirm('Yakin hapus buku ini? Semua favorit yang nunjuk ke buku ini juga ikut kehapus.');">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button type="submit" class="btn btn-hapus">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </main>
    </div>
</body>
</html>
```

### `admin/dashboard.php` (diubah dari Sesi 6 — nambah stat buku & favorit)
```php
<?php
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$totalUsers = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM users"))['jumlah'];
// <-- BARU: 2 query ini gak ada di Sesi 6
$totalBuku = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM buku"))['jumlah'];
$totalFavorit = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM favorit"))['jumlah'];

$activePage = 'dashboard';
?>
<!-- ...HTML sama kayak Sesi 6, .stat-grid nambah 2 kartu: Total Buku & Total Favorit... -->
```

### `includes/admin-sidebar.php` (diubah dari Sesi 6 — nambah link Kelola Buku)
```php
<nav class="sidebar">
    <div class="sidebar-brand">Toko Buku</div>
    <a href="../index.php">Home</a>
    <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <!-- <-- BARU -->
    <a href="buku.php" class="<?= $activePage === 'buku' ? 'active' : '' ?>">Kelola Buku</a>
    <a href="users.php" class="<?= $activePage === 'users' ? 'active' : '' ?>">Kelola Users</a>
    <a href="../logout.php" class="sidebar-logout">Logout</a>
</nav>
```

### `index.php` (diubah dari Sesi 6 — navbar 3 menu + baca database)
```php
<?php
session_start();
require_once "config/db.php";

$sudahLogin = isset($_SESSION['user_id']);

// <-- BARU: baca 3 buku terbaru dari database, index.php Sesi 6 statis doang
$query = "SELECT id, judul, penulis, tahun_terbit FROM buku ORDER BY id DESC LIMIT 3";
$result = mysqli_query($koneksi, $query);
?>
<!-- navbar sekarang ada 3 link tengah: Home, Buku, About -->
<div class="navbar-links">
    <a href="index.php" class="active">Home</a>
    <a href="katalog.php">Buku</a>
    <a href="about.php">About</a>
</div>
<!-- ...hero sama kayak Sesi 6, di bawahnya section baru "Buku Terbaru" nge-loop $result... -->
```

File lain (`config/db.php`, `includes/auth-check.php`, `includes/admin-check.php`, `register.php`, `login.php`, `logout.php`, `admin/users.php`) sama persis kayak Sesi 6, cuma path redirect di `auth-check.php` disesuaikan ke folder Sesi 7. Semua file lengkapnya udah ada fisik di folder ini, tinggal buka aja.

## Update: Halaman Tambah/Edit Buku Dipisah

`admin/buku.php` awalnya 1 halaman isinya form (tambah/edit) DAN table sekaligus. Sekarang dipecah jadi 3 halaman:

- **`admin/buku.php`** — cuma list buku + tombol "+ Tambah Buku" + tombol Edit/Hapus per baris. Gak ada form di halaman ini lagi.
- **`admin/buku-tambah.php`** — halaman sendiri, form kosong buat nambah buku baru. Sukses simpen -> redirect ke `buku.php?msg=created`.
- **`admin/buku-edit.php`** — halaman sendiri, nerima `?id=...` dari URL, form-nya keisi data buku yang mau diedit. Sukses update -> redirect ke `buku.php?msg=updated`.

Pesan sukses (nambah/edit/hapus) tetep pola yang sama kayak sesi-sesi sebelumnya — flash message sederhana lewat `?msg=...` di URL, dibaca & ditampilin di `admin/buku.php`:
```php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
```
```php
<?php if ($msg === 'created'): ?>
    <div class="alert">Buku berhasil ditambah.</div>
<?php elseif ($msg === 'updated'): ?>
    <div class="alert">Buku berhasil diedit.</div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="alert">Buku berhasil dihapus.</div>
<?php endif; ?>
```

## Update: Search & Pagination di Katalog

`katalog.php` sekarang ada form pencarian (judul/penulis) dan pagination (3 buku per halaman), gak nampilin semua buku sekaligus.

**Search** — form GET, kata kunci nyantol di URL (`katalog.php?q=laskar`), jadi bisa di-bookmark/share:
```php
$search = trim($_GET['q'] ?? '');
```
Kalau `$search` gak kosong, query nambah `WHERE judul LIKE ? OR penulis LIKE ?`, parameternya `"%$search%"` di-bind lewat prepared statement (bukan digabung langsung ke string SQL) — tetep aman dari SQL Injection walau ada input bebas dari user.

**Pagination** — `LIMIT`/`OFFSET`:
```php
$perPage = 3;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $perPage;
```
Query utama pakai `LIMIT ? OFFSET ?` di paling belakang. Total halaman dihitung dari query `COUNT(*)` terpisah (pakai kondisi search yang sama), dibagi `$perPage`, dibulatkan ke atas (`ceil()`).

Ganti halaman & search bisa dipakai bareng — link pagination ikut bawa `q=...` biar gak reset hasil pencarian pas pindah halaman.

## Update: Scroll Gak Ke-reset Pas Ganti Halaman Pagination

Klik link pagination = reload halaman baru dari server (bukan AJAX), jadi browser defaultnya reset scroll ke paling atas tiap kali. Biar posisi scroll user gak lompat-lompat, ditambahin sedikit JavaScript di `katalog.php`:

```js
// Sebelum pindah halaman: simpen posisi scroll sekarang
document.querySelectorAll('.pagination-link').forEach(function (link) {
    link.addEventListener('click', function () {
        sessionStorage.setItem('katalogScrollY', window.scrollY);
    });
});

// Pas halaman baru kebuka: balikin ke posisi scroll yang disimpen tadi
var scrollTersimpan = sessionStorage.getItem('katalogScrollY');
if (scrollTersimpan !== null) {
    window.scrollTo(0, parseInt(scrollTersimpan, 10));
    sessionStorage.removeItem('katalogScrollY');
}
```

`sessionStorage` dipilih (bukan `localStorage`) karena datanya cuma butuh idup selama 1 sesi browsing, gak perlu nyangkut lama-lama di browser user.

## Update: Gambar Buku (Link URL)

Nyusul abis versi awal folder ini dibikin — buku sekarang bisa punya gambar sampul.

**Kalau udah pernah jalanin `setup.sql` versi lama (sebelum kolom gambar ada)**, jangan run ulang `setup.sql` dari awal (nanti `DROP TABLE`-nya ngehapus data buku/favorit yang udah kamu isi). Cukup tambahin kolomnya aja:
```sql
USE toko_belajar;
ALTER TABLE buku ADD COLUMN gambar_url VARCHAR(500);
```
Data lama gak kesentuh, baris yang udah ada otomatis dapet `gambar_url = NULL` (nampilin kotak "Tanpa Gambar" di katalog).

Perubahan kodenya:

**`setup.sql`** — nambah kolom `gambar_url` (nullable):
```sql
CREATE TABLE buku (
    ...
    gambar_url VARCHAR(500),
    ...
);
```

**`admin/buku.php`** — form nambah field "URL Gambar", divalidasi pakai `filter_var()`:
```php
$gambarUrl = trim($_POST['gambar_url'] ?? '');

if (!empty($gambarUrl) && !filter_var($gambarUrl, FILTER_VALIDATE_URL)) {
    $errors[] = "URL gambar gak valid.";
}

// Simpen NULL kalau kosong, bukan string kosong ""
$gambarUrlValue = $gambarUrl === '' ? null : $gambarUrl;
```
Table admin juga nambah kolom thumbnail kecil buat preview gambarnya.

**`katalog.php` & `index.php`** — book card nampilin gambar kalau ada, kotak abu-abu "Tanpa Gambar" kalau kosong:
```php
<?php if ($row['gambar_url']): ?>
    <img src="<?= htmlspecialchars($row['gambar_url']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>" class="book-cover">
<?php else: ?>
    <div class="book-cover book-cover-kosong">Tanpa Gambar</div>
<?php endif; ?>
```

Sengaja pakai link URL, bukan upload file (`$_FILES`) — alasannya udah dijelasin di bagian "Gambar Buku" di atas.

## Update: Kumpulan Perbaikan Tampilan (UI Fixes)

Nyusul abis fitur-fitur di atas kelar, ada beberapa bug tampilan yang ketauan & dibenerin belakangan. Kalau kamu udah nge-push versi lama ke GitHub, ini daftar lengkap yang perlu di-copas ulang biar sinkron.

### 1. Menu navbar (Home/Buku/About) gak presisi di tengah

**File:** `style.css`. Sebelumnya `.navbar` pakai `display:flex; justify-content:space-between`, posisi menu tengah jadi ikut kegeser tergantung lebar brand vs tombol kanan. Diganti jadi grid 3 kolom:
```css
.navbar {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
}
.navbar-brand { justify-self: start; }
.navbar-links { justify-self: center; }
.navbar-right { justify-self: end; }
```

### 2. Kartu buku (`.book-card`) ukurannya beda-beda

Judul yang panjang bikin card lebih tinggi dari card sebelahnya. Dibenerin: `.book-card` jadi flex column + `min-height` tetap, judul dibatasin 2 baris, tombol like didorong ke bawah:
```css
.book-card {
    min-height: 340px;
    display: flex;
    flex-direction: column;
}
.book-title {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.4em;
}
.like-form { margin-top: auto; }
```

### 3. Tombol like — dari teks jadi icon lingkaran doang

Awalnya tombolnya ada tulisan "Favoritkan"/"Favorit". Diganti jadi lingkaran outline 40x40px isinya icon hati doang (kosong `&#9825;` = belum like, penuh `&#9829;` = udah like). Dua masalah CSS yang ketemu pas ngerjain ini:

- **Tombolnya kejadian jadi kotak gelap full-width** — rule global `form button[type="submit"]` (buat Login/Register) specificity-nya lebih tinggi dari `.btn-like` biasa (2 element-selector vs 1 class). Fix-nya pakai selector lebih spesifik: `.like-form button.btn-like`.
- **Kotak fokus biru bawaan browser** — ditambahin `appearance: none; outline: none;`.

```php
<!-- katalog.php -->
<?php if ($row['favorit_id']): ?>
    <button type="submit" class="btn-like btn-like-active" title="Batal favorit">&#9829;</button>
<?php else: ?>
    <button type="submit" class="btn-like" title="Favoritkan">&#9825;</button>
<?php endif; ?>
```
```css
.like-form button.btn-like {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1.5px solid #4a2c1d;
    appearance: none;
    outline: none;
    font-size: 1.7em;
}
```

### 4. Tombol & input search gak sejajar

Sama akar masalahnya kayak poin 3 — rule global buat form Login/Register (`width:100%` di button, `margin-bottom:16px` di input) ikut nempel ke form search karena sama-sama `<form>`. Fix-nya kasih selector spesifik + override manual:
```css
.search-form button[type="submit"] { width: auto; }
.search-form input[type="text"] { margin-bottom: 0; }
```

### 5. Klik pagination / like bikin halaman lompat ke atas

Klik pagination atau like = reload halaman baru (bukan AJAX), browser defaultnya reset scroll ke paling atas. Ditambahin JavaScript kecil di `katalog.php` — simpen posisi scroll ke `sessionStorage` SEBELUM pindah halaman, balikin lagi abis halaman baru kebuka:
```js
document.querySelectorAll('.pagination-link').forEach(function (link) {
    link.addEventListener('click', function () {
        sessionStorage.setItem('katalogScrollY', window.scrollY);
    });
});
document.querySelectorAll('.like-form').forEach(function (form) {
    form.addEventListener('submit', function () {
        sessionStorage.setItem('katalogScrollY', window.scrollY);
    });
});
var scrollTersimpan = sessionStorage.getItem('katalogScrollY');
if (scrollTersimpan !== null) {
    window.scrollTo(0, parseInt(scrollTersimpan, 10));
    sessionStorage.removeItem('katalogScrollY');
}
```

### 6. Halaman Kelola Buku dipecah — form gak digabung sama table lagi

`admin/buku.php` awalnya 1 halaman isinya form (tambah/edit) DAN table sekaligus. Sekarang dipecah jadi 3 halaman:
- **`admin/buku.php`** — cuma list + tombol "+ Tambah Buku" + Edit/Hapus per baris.
- **`admin/buku-tambah.php`** — halaman sendiri, form kosong.
- **`admin/buku-edit.php`** — halaman sendiri, nerima `?id=...`, form-nya keisi data lama.

Pesan sukses tetep pola `?msg=...` yang udah dipakai dari sesi-sesi sebelumnya (`created`/`updated`/`deleted`), dibaca & ditampilin di `admin/buku.php`.

### 7. Form & judul "Tambah/Edit Buku" ke-tengah, tombol Simpan/Batal sejajar

Form-nya di-center di area konten (`margin: 0 auto`), judul disamain lebar & posisinya biar sejajar sama form:
```css
.form-title {
    max-width: 500px;
    margin: 0 auto 20px auto;
    text-align: center;
}
.form-buku {
    max-width: 500px;
    margin: 20px auto;
}
```

Tombol "Simpan"/"Update" + "Batal" awalnya numpuk ke bawah (bukan sampingan) — penyebabnya sama kayak poin 3 & 4, `width:100%` dari rule global ketiban ke tombol submit. Dibungkus 1 div `.form-actions`, tombol Batal dijadiin tombol merah beneran (bukan link teks doang):
```php
<div class="form-actions">
    <button type="submit">Simpan</button>
    <a href="buku.php" class="btn-batal">Batal</a>
</div>
```
```css
.form-buku button[type="submit"] { width: auto; }
.form-actions {
    display: flex;
    justify-content: flex-start;
    gap: 12px;
}
.btn-batal {
    background-color: #c0392b;
    color: #fff;
    padding: 10px 20px;
    border-radius: 6px;
}
```

**Pola yang kelihatan berulang dari poin 3, 4, dan 7:** rule CSS global yang dipakai buat form Login/Register (`form input[type=text]`, `form button[type=submit]`) gampang "bocor" ke form-form lain yang gak dimaksud, karena selector-nya cuma modal element `form` + attribute, bukan class spesifik. Kalau bikin form baru dan tampilannya keliatan aneh padahal CSS-nya udah bener, curigain dulu ada rule global yang ke-apply gak sengaja — cek lewat DevTools (Inspect Element, tab Styles, lihat rule mana yang ke-coret/menang).

## Checkpoint Sebelum Lanjut ke Sesi 8

Sebelum lanjut ke `8-Bootstrap-Integration/`, pastikan bisa jawab/lakuin ini tanpa buka catatan/AI:
- Jelasin kenapa relasi many-to-many (favorit) butuh table sendiri, bukan kolom tambahan.
- Jelasin apa yang kejadian ke data `favorit` kalau buku-nya dihapus admin (dan kenapa, `ON DELETE CASCADE`).
- Coba akses `favorit-toggle.php` lewat POST manual (misal pakai Postman/curl) tanpa login — pastiin ketolak, bukan cuma tombolnya yang gak keliatan.
- Bikin fitur serupa dari nol: tambah kolom "komentar" (table baru, relasi ke `buku` + `users`) buat user nulis review singkat di tiap buku.
