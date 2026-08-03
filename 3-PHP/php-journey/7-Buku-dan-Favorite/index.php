<?php
// ===================================================
// HOMEPAGE — landing page publik
// ===================================================
session_start();

require_once "config/db.php";

$sudahLogin = isset($_SESSION['user_id']);

// asc dari terkecil terbesar
// desc dari terbesar terkecil

// <-- BARU (dari Sesi 6): index.php sekarang baca dari database,
// nampilin 3 buku terbaru. Sebelumnya isi homepage statis doang.
$query = "SELECT id, judul, penulis, tahun_terbit, gambar_url FROM buku ORDER BY id DESC LIMIT 3";
$result = mysqli_query($koneksi, $query);
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

        <div class="navbar-links">
            <a href="index.php" class="active">Home</a>
            <a href="katalog.php">Buku</a>
            <a href="about.php">About</a>
        </div>

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
            <a href="katalog.php" class="btn-dark btn-hero">Lihat Buku</a>
        <?php else: ?>
            <a href="register.php" class="btn-dark btn-hero">Daftar Sekarang</a>
        <?php endif; ?>
    </section>

    <section class="page-header">
        <h2>Buku Terbaru</h2>
    </section>

    <section class="book-grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="book-card">
                <?php if ($row['gambar_url']): ?>
                    <img src="<?= htmlspecialchars($row['gambar_url']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>" class="book-cover">
                <?php else: ?>
                    <div class="book-cover book-cover-kosong">Tanpa Gambar</div>
                <?php endif; ?>

                <div class="book-title"><?= htmlspecialchars($row['judul']) ?></div>
                <div class="book-meta"><?= htmlspecialchars($row['penulis']) ?> &middot; <?= htmlspecialchars($row['tahun_terbit']) ?></div>
            </div>
        <?php endwhile; ?>
    </section>

    <p style="text-align:center; margin-bottom: 40px;"><a href="katalog.php" class="btn-dark">Lihat Semua Buku</a></p>

    <footer class="footer">
        <p>Toko Buku &mdash; project latihan PHP Sesi 7.</p>
        <p>&copy; <?= date("Y") ?></p>
    </footer>
</body>
</html>
