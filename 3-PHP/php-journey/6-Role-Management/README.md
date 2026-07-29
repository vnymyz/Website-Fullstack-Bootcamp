# Sesi 6 — Role Management

Tujuan: bedain "udah login" (authentication, Sesi 5) sama "boleh ngapain aja" (authorization). Bikin role `admin` dan `user`, dan yang paling penting — paham kenapa nyembunyiin tombol di HTML/CSS **bukan** security.

## Sambungan dari Sesi 5

Masih database `toko_belajar`, masih table `users` yang sama. Jalanin `setup.sql` di folder ini buat nambah kolom `role`. **Gak bikin table baru**, cuma nambah 1 kolom ke table yang udah ada.

Akun yang udah kamu register di Sesi 5 masih kepake di sini (satu database yang sama). Kalau mau mulai bersih, boleh register akun baru lewat `register.php` di folder ini.

### File Baru / Diubah dari Sesi 5 — Kodingan Lengkap

Semua file di bawah ini udah ada fisik di folder ini, tinggal buka aja langsung. Ini salinan lengkapnya buat referensi cepet.

**`setup.sql`** (baru — jalanin di phpMyAdmin dulu)
```sql
USE toko_belajar;

ALTER TABLE users ADD COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user';

-- Ganti 'username_kamu' sama username yang mau dijadiin admin:
-- UPDATE users SET role = 'admin' WHERE username = 'username_kamu';

SELECT id, username, role FROM users;
```

**`login.php`** (diubah dari Sesi 5 — ada tambahan `role` di query & session)
```php
<?php
session_start();

require_once "config/db.php";

$errors = [];
$username = "";
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = "Username dan password wajib diisi.";
    }

    if (empty($errors)) {
        $query = "SELECT id, username, password, role FROM users WHERE username = ?"; // <-- BARU: tambah kolom role
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // <-- BARU: simpen role ke session

            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h1>Login</h1>

        <?php if ($msg === 'registered'): ?>
            <div class="alert">Register berhasil, silakan login.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul class="alert-error">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>">

            <label>Password:</label>
            <input type="password" name="password">

            <button type="submit">Login</button>
        </form>

        <p>Belum punya akun? <a href="register.php">Register di sini</a></p>
        <p><a href="index.php">&larr; Kembali ke Home</a></p>
    </div>
</body>
</html>
```

**`includes/auth-check.php`** (diubah dari Sesi 5 — path redirect jadi absolut)
```php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /php-journey/6-Role-Management/login.php"); // <-- DIUBAH: dulu cuma "login.php"
    exit;
}
```

**`includes/admin-check.php`** (baru total)
```php
<?php
require_once __DIR__ . "/auth-check.php";

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("403 Forbidden — kamu bukan admin, gak boleh akses halaman ini.");
}
```

**`includes/admin-sidebar.php`** (baru total — gak ada file ini di Sesi 5)
```php
<?php
// <-- BARU: $activePage dikirim dari file yang include ini,
// dipake buat kasih class "active" ke menu yang lagi dibuka
$activePage = $activePage ?? '';
?>
<!-- <-- BARU: seluruh <nav> sidebar ini, konsepnya gak ada di Sesi 5 sama sekali -->
<nav class="sidebar">
    <div class="sidebar-brand">Toko Buku</div>
    <a href="../index.php">Home</a>
    <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="users.php" class="<?= $activePage === 'users' ? 'active' : '' ?>">Kelola Users</a>
    <a href="../logout.php" class="sidebar-logout">Logout</a>
</nav>
```

**`dashboard.php`** (diubah dari Sesi 5 — nambah pengalihan role admin)
```php
<?php
require_once "includes/auth-check.php";

// <-- BARU: 4 baris ini gak ada di Sesi 5. Kalau role-nya admin,
// lempar ke dashboard admin, jangan render dashboard biasa di bawah.
if ($_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card dashboard-info">
        <h1>Dashboard User</h1>
        <!-- <-- BARU: "Role kamu: ..." gak ada di Sesi 5, cuma nampilin username doang -->
        <p>Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! Role kamu: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>

        <!-- <-- BARU: tombol "Home" (link ke index.php), Logout di Sesi 5 gak ada tombol Home -->
        <p>
            <a href="index.php" class="btn-dark">Home</a>
            <a href="logout.php" class="btn-outline">Logout</a>
        </p>
    </div>
</body>
</html>
```

**`admin/dashboard.php`** (baru total)
```php
<?php
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$totalUsers = 0;
$totalAdmin = 0;
$totalUser = 0;

$result = mysqli_query($koneksi, "SELECT role, COUNT(*) AS jumlah FROM users GROUP BY role");
while ($row = mysqli_fetch_assoc($result)) {
    $totalUsers += (int) $row['jumlah'];
    if ($row['role'] === 'admin') {
        $totalAdmin = (int) $row['jumlah'];
    } else {
        $totalUser = (int) $row['jumlah'];
    }
}

$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<!-- <-- BARU: seluruh layout sidebar (class "admin-body" + "admin-wrapper") gak ada di Sesi 5 -->
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- <-- BARU: sidebar partial, cuma nongol di halaman dalem folder admin/ -->
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="content">
            <h1>Dashboard Admin</h1>
            <p>Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>.</p>

            <!-- <-- BARU: kartu statistik jumlah user, ambil dari query COUNT(*) di atas -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $totalUsers ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $totalAdmin ?></div>
                    <div class="stat-label">Admin</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $totalUser ?></div>
                    <div class="stat-label">User Biasa</div>
                </div>
            </div>

            <p><a href="users.php" class="btn-admin">Kelola Users &rarr;</a></p>
        </main>
    </div>
</body>
</html>
```

**`admin/users.php`** (baru total)
```php
<?php
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$errors = [];

// --- Aksi: ganti role ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ubah_role') {
    $targetId = (int) $_POST['id'];
    $roleBaru = $_POST['role'] === 'admin' ? 'admin' : 'user';

    if ($targetId === (int) $_SESSION['user_id'] && $roleBaru !== 'admin') {
        $errors[] = "Gak bisa ubah role akun sendiri jadi user.";
    } else {
        $query = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $roleBaru, $targetId);
        mysqli_stmt_execute($stmt);

        header("Location: users.php?msg=updated");
        exit;
    }
}

// --- Aksi: hapus user ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus') {
    $targetId = (int) $_POST['id'];

    if ($targetId === (int) $_SESSION['user_id']) {
        $errors[] = "Gak bisa hapus akun sendiri.";
    } else {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $targetId);
        mysqli_stmt_execute($stmt);

        header("Location: users.php?msg=deleted");
        exit;
    }
}

$query = "SELECT id, username, role, created_at FROM users ORDER BY id ASC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
$activePage = 'users';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Users - Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="content">
            <h1>Kelola Users</h1>

            <?php if ($msg === 'updated'): ?>
                <div class="alert">Role user berhasil diubah.</div>
            <?php elseif ($msg === 'deleted'): ?>
                <div class="alert">User berhasil dihapus.</div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <ul class="alert-error">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Daftar Sejak</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <!-- <-- BARU: badge warna buat role, class "role-admin"/"role-user" beda warna -->
                    <td><span class="role-badge role-<?= htmlspecialchars($row['role']) ?>"><?= htmlspecialchars($row['role']) ?></span></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <!-- <-- BARU: div pembungkus flex, biar dropdown + tombol sejajar rapi -->
                        <div class="table-actions">
                            <form action="users.php" method="POST">
                                <input type="hidden" name="action" value="ubah_role">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="user" <?= $row['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                    <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                </select>
                            </form>

                            <form action="users.php" method="POST" onsubmit="return confirm('Yakin hapus user ini?');">
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

**`index.php`** (baru total — gak ada sama sekali di Sesi 5. Sekarang bentuknya landing page: navbar + hero + feature grid + footer, tema "Toko Buku")
```php
<?php
session_start();

// <-- BARU: seluruh file index.php gak ada sama sekali di Sesi 5.
// $sudahLogin ini yang nentuin isi navbar & hero di bawah beda apa enggak.
$sudahLogin = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Toko Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-body">
    <!-- <-- BARU: navbar, gak ada konsep ini sama sekali di Sesi 5 -->
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Toko Buku</a>

        <div class="navbar-right">
            <?php if ($sudahLogin): ?>
                <span class="navbar-user">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (<?= htmlspecialchars($_SESSION['role']) ?>)</span>
                <a href="dashboard.php" class="btn-dark">Dashboard</a>
                <a href="logout.php" class="btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-dark">Login</a>
                <a href="register.php" class="btn-outline">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- <-- BARU: hero section, tombol CTA-nya beda tergantung status login -->
    <section class="hero">
        <h1>Toko Buku</h1>
        <p>Cari buku, kelola akun, dan pantau stok di satu tempat.</p>

        <?php if ($sudahLogin): ?>
            <a href="dashboard.php" class="btn-dark btn-hero">Ke Dashboard</a>
        <?php else: ?>
            <a href="register.php" class="btn-dark btn-hero">Daftar Sekarang</a>
        <?php endif; ?>
    </section>

    <!-- <-- BARU: 3 kartu fitur, cuma teks statis (gak baca database) -->
    <section class="feature-grid">
        <div class="feature-card">
            <div class="feature-title">Katalog Buku</div>
            <p>Cari buku dari daftar yang tersedia.</p>
        </div>
        <div class="feature-card">
            <div class="feature-title">Akun</div>
            <p>Password disimpan dalam bentuk hash, bukan teks asli.</p>
        </div>
        <div class="feature-card">
            <div class="feature-title">Kelola Toko</div>
            <p>Admin bisa atur stok dan akun user lewat dashboard.</p>
        </div>
    </section>

    <!-- <-- BARU: footer, nempel ke bawah lewat CSS margin-top:auto -->
    <footer class="footer">
        <p>Toko Buku &mdash; project latihan PHP Sesi 6.</p>
        <p>&copy; <?= date("Y") ?></p>
    </footer>
</body>
</html>
```

**`register.php`** (hampir sama kayak Sesi 5, cuma INSERT-nya gak nulis kolom role)
```php
<?php
require_once "config/db.php";

$errors = [];
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if (empty($username)) {
        $errors[] = "Username wajib diisi.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }
    if ($password !== $konfirmasi) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }

    if (empty($errors)) {
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_fetch_assoc($result)) {
            $errors[] = "Username sudah dipakai, pilih yang lain.";
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // <-- SAMA seperti Sesi 5, TAPI gak nulis kolom "role" di sini,
        // jadi otomatis ke-isi default 'user' dari struktur table (setup.sql).
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPassword);
        mysqli_stmt_execute($stmt);

        header("Location: login.php?msg=registered");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h1>Register</h1>

        <?php if (!empty($errors)): ?>
            <ul class="alert-error">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>">

            <label>Password:</label>
            <input type="password" name="password">

            <label>Konfirmasi Password:</label>
            <input type="password" name="konfirmasi">

            <button type="submit">Daftar</button>
        </form>

        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        <p><a href="index.php">&larr; Kembali ke Home</a></p>
    </div>
</body>
</html>
```

**File yang gak berubah sama sekali dari Sesi 5 (copy langsung):** `config/db.php`, `logout.php`.

Semua file CSS (`style.css`) juga baru, isinya ada di folder ini langsung — gak dipaste di sini karena panjang, tinggal buka aja.

## Cara Menjalankan

1. Laragon — Start All.
2. Jalanin `setup.sql` (nambah kolom `role` ke table `users`).
3. Jadiin salah satu akun jadi admin — buka `setup.sql`, jalanin baris `UPDATE users SET role = 'admin' WHERE username = '...'` (ganti sama username kamu).
4. Buka browser:
   ```
   http://localhost/php-journey/6-Role-Management/login.php
   ```

## Urutan Belajar

| Urutan | File | Yang Dipelajari |
|---|---|---|
| 1 | `setup.sql` | `ALTER TABLE ... ADD COLUMN`, `ENUM` |
| 2 | `index.php` | Homepage publik — beda tampilan tergantung `isset($_SESSION['user_id'])` |
| 3 | `login.php` | Role disimpen ke `$_SESSION['role']` pas login |
| 4 | `dashboard.php` | Dashboard user biasa — kalau role admin, di-redirect ke `admin/dashboard.php` |
| 5 | `includes/admin-check.php` | **Inti sesi ini** — cek role di server, bukan cuma di tampilan |
| 6 | `admin/dashboard.php` | Dashboard admin — layout sidebar + statistik jumlah user |
| 7 | `admin/users.php` | Kelola user: ganti role, hapus user, masih dalam layout sidebar yang sama |
| 8 | `latihan/` | Bug hunt closed-book — cari & benerin halaman admin yang bolong |

**Cara belajar:**
1. Buka `index.php` dulu tanpa login — harusnya cuma ada tombol Login/Register.
2. Login sebagai admin — otomatis kelempar ke `admin/dashboard.php` (sidebar kiri + kartu statistik jumlah user/admin).
3. Coba ganti role user lain jadi admin/user lewat `admin/users.php`, lihat efeknya (harus logout-login ulang buat role barunya kepake, karena role disimpen di session pas login, bukan dicek real-time).
4. Logout, login lagi pakai akun role `user`. Harusnya masuk ke dashboard versi polos (bukan sidebar), gak ada akses ke halaman admin.
5. **Penting**: sambil masih login sebagai user biasa, coba ketik URL `admin/users.php` atau `admin/dashboard.php` LANGSUNG di address bar. Harusnya kena "403 Forbidden". Kalau ini masih bisa kebuka, berarti ada yang salah di `admin-check.php`.
6. Buka `index.php` lagi pas udah login (klik tombol "Home") — namanya (`$_SESSION['username']`) harus keliatan di situ.
7. Kerjain `latihan/PANDUAN.md` — closed-book, cari sendiri bug-nya dulu sebelum liat jawaban.

## Konsep Kunci

- **`auth-check.php`** jawab "udah login apa belum?" — dipake di SEMUA halaman protected.
- **`admin-check.php`** jawab "role-nya admin apa bukan?" — dipake CUMA di halaman/aksi khusus admin. Selalu include auth-check dulu, baru admin-check (admin-check.php di sini otomatis include auth-check.php juga).
- Nyembunyiin link/tombol di HTML/CSS/JS itu cuma buat **UX** (biar user gak bingung liat tombol yang bukan haknya), BUKAN buat security. Server yang harus nolak requestnya, bukan browser yang nyembunyiin tombolnya.
- Kenapa? Karena HTML/CSS/JS itu jalan di browser user — user bisa liat HTML source, ganti CSS lewat DevTools, atau langsung ketik URL manual. Semua itu di luar kendali server. Satu-satunya yang beneran "aman" adalah pengecekan yang jalan di server, sebelum data/halaman dikirim.

## File di Folder Ini

```
6-Role-Management/
  README.md
  setup.sql
  style.css
  config/
    db.php
  includes/
    auth-check.php
    admin-check.php
    admin-sidebar.php
  index.php          <- homepage publik (login/register atau nama user)
  register.php
  login.php
  logout.php
  dashboard.php       <- dashboard user biasa
  admin/
    dashboard.php      <- dashboard admin (sidebar + statistik)
    users.php
  latihan/
    PANDUAN.md
    dashboard-bermasalah.php
    admin-tanpa-proteksi.php          <- ada bug, benerin sendiri
    admin-tanpa-proteksi-jawaban.php  <- jawaban, cek terakhir
```

## Update / Perbaikan Setelah Versi Awal

Beberapa perbaikan kecil nyusul abis file-file di atas pertama kali dibikin. Kalau kamu udah kadung nyalin versi lama, sesuain ini di file kamu:

**1. `index.php` — tombol "Ke Dashboard" + "Logout" mencong (gak center)**
Penyebab: dua tombol itu ditulis di elemen terpisah (`<p>` beda-beda / salah satunya di luar `<p>`), padahal yang kena `text-align: center` cuma elemen `<p>` di dalem `.card`. Perbaikannya, satuin dua tombol dalam **1 `<p>` yang sama**:
```php
<p>
    <a href="dashboard.php" class="btn-dark">Ke Dashboard</a>
    <a href="logout.php" class="btn-outline">Logout</a>
</p>
```

**2. `dashboard.php` — sama, tombol "Home" + "Logout" mencong**
Sama persis kasusnya kayak `index.php`. Diperbaiki jadi:
```php
<p>
    <a href="index.php" class="btn-dark">Home</a>
    <a href="logout.php" class="btn-outline">Logout</a>
</p>
```
Perhatiin juga class-nya ganti dari `btn-admin`/`btn-logout` jadi `btn-dark`/`btn-outline` — alasannya di poin 3.

**3. Warna tombol di dalem `.card` sempet keliatan biru (padahal udah dikasih `text-decoration:none`)**
Penyebabnya BUKAN karena kurang `text-decoration:none`, tapi karena selector `.card a` (warna biru `#2980b9`) punya specificity CSS lebih tinggi daripada `.btn-admin`/`.btn-logout` doang, jadi warnanya ketiban. Fix-nya di `style.css`, tombol homepage/dashboard sekarang pakai selector yang lebih spesifik:
```css
.card a.btn-dark,
.card a.btn-outline {
    text-decoration: none;
    border: none;
}

.card a.btn-dark {
    color: #fff;
    background-color: #1c1c1c;
}

.card a.btn-outline {
    color: #fff;
    background-color: #555;
}
```
Makanya di `index.php` dan `dashboard.php`, class tombolnya dipakein `btn-dark`/`btn-outline`, bukan `btn-admin`/`btn-logout` (dua class itu masih dipake, tapi cuma buat tombol di luar `.card`, misalnya di `admin/dashboard.php`).

**4. Tabel di `admin/users.php` — kolom Aksi berantakan**
Dropdown role + tombol Hapus sebelumnya nempel gak rapi. Dibungkus div flex baru `.table-actions`, dan kolom Role dijadiin badge warna:
```php
<td><span class="role-badge role-<?= htmlspecialchars($row['role']) ?>"><?= htmlspecialchars($row['role']) ?></span></td>
<td>
    <div class="table-actions">
        <!-- form ganti role + form hapus, sama kayak sebelumnya -->
    </div>
</td>
```
CSS tambahannya:
```css
.table-actions { display: flex; align-items: center; gap: 10px; }
th, td { vertical-align: middle; }
.role-badge { padding: 4px 12px; border-radius: 12px; font-weight: bold; }
.role-admin { background-color: #f4e6f9; color: #8e44ad; }
.role-user { background-color: #eaf2f8; color: #2980b9; }
```

**5. Judul halaman dibedain — "Dashboard User" vs "Dashboard Admin"**
Sebelumnya dua-duanya cuma judulnya "Dashboard" doang, bingung kalau lagi buka banyak tab. Sekarang:
```php
// dashboard.php (user)
<title>Dashboard User</title>
<h1>Dashboard User</h1>

// admin/dashboard.php
<title>Dashboard Admin</title>
<h1>Dashboard Admin</h1>
```

**6. `index.php` diubah total jadi landing page + tema "Toko Buku"**
Sebelumnya `index.php` cuma kartu kecil di tengah layar (sama kayak login/register). Sekarang dirombak jadi landing page beneran: navbar di atas, hero section, 3 kartu fitur, footer di bawah. Body-nya juga ganti class dari `card dashboard-info` jadi `landing-body`. Semua teks juga diganti tema toko buku (bukan "Toko Belajar" generik lagi).

**7. Navbar item warna hitam, ketiban tombol hitam juga (gak keliatan)**
Navbar awalnya `background-color: #1c1c1c` (item), tombol Dashboard/Login juga `.btn-dark` (hitam) — dua-duanya hitam, jadi nyatu, gak keliatan. Diperbaiki: navbar diganti warna coklat tua (`#4a2c1d`, sesuai tema toko buku), dan tombol di dalem navbar dikasih style beda pakai selector lebih spesifik:
```css
.navbar {
    background-color: #4a2c1d;
}

.navbar-right a.btn-dark {
    background-color: #fff;
    color: #4a2c1d;
}

.navbar-right a.btn-outline {
    background-color: transparent;
    color: #fff;
    border: 1px solid #fff;
}
```

**8. Footer nyisain space kosong di bawahnya**
Penyebabnya: `body` (rule paling atas di `style.css`) punya `min-height: 100vh`, jadi kalau konten halaman lebih pendek dari tinggi layar, ada area kosong nganggur di bawah footer. Diperbaiki dengan bikin `body.landing-body` jadi flex container, footer-nya didorong ke paling bawah:
```css
body.landing-body {
    display: flex;
    flex-direction: column;
    align-items: stretch;      /* WAJIB di-override, defaultnya "center" dari rule body */
    justify-content: flex-start; /* WAJIB juga, defaultnya "center" */
}

.footer {
    margin-top: auto; /* footer "didorong" ke bawah, ngisi sisa ruang kosong */
}
```
Catatan penting: `align-items`/`justify-content` WAJIB di-set ulang di `body.landing-body`. Kalau lupa, nilainya kebawa `center` dari rule `body` paling atas, efeknya navbar & footer ikut nyusut ke lebar konten doang (gak full-width) — sempet kejadian pas baru nambahin `display:flex` doang tanpa nge-reset dua properti ini.

Semua perbaikan ini udah otomatis kepake di file fisik folder ini — bagian ini cuma buat kamu yang mau nyocokin kalau sempet nyalin versi lama sebelum perbaikan.

## Checkpoint Sebelum Lanjut ke Sesi 7

Sebelum lanjut ke `7-Bootstrap-Integration/`, pastikan bisa jawab/lakuin ini tanpa buka catatan/AI:
- Jelasin bedanya `auth-check.php` sama `admin-check.php`.
- Jelasin kenapa nyembunyiin link admin pakai CSS `display:none` bukan security yang cukup.
- Selesein bug hunt di `latihan/` — nemuin bug-nya sendiri sebelum liat jawaban.
- Coba akses halaman admin pakai akun `user` biasa lewat URL langsung — pastiin ketolak (403), bukan cuma linknya yang gak keliatan.
