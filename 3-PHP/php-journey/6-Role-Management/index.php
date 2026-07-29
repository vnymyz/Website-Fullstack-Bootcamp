<?php
// ===================================================
// HOMEPAGE — landing page publik, bisa diakses login atau belum
// ===================================================
// Sengaja BUKAN pakai auth-check.php di sini -- halaman ini emang
// buat siapa aja, login atau enggak. session_start() tetep dipanggil
// manual biar bisa baca $_SESSION kalau ada.
session_start();

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

    <section class="hero">
        <h1>Toko Buku</h1>
        <p>Cari buku, kelola akun, dan pantau stok di satu tempat.</p>

        <?php if ($sudahLogin): ?>
            <a href="dashboard.php" class="btn-dark btn-hero">Ke Dashboard</a>
        <?php else: ?>
            <a href="register.php" class="btn-dark btn-hero">Daftar Sekarang</a>
        <?php endif; ?>
    </section>

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

    <footer class="footer">
        <p>Toko Buku &mdash; project latihan PHP Sesi 6.</p>
        <p>&copy; <?= date("Y") ?></p>
    </footer>
</body>
</html>
