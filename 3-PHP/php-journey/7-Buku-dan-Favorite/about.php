<?php
// ===================================================
// ABOUT — halaman dummy, isinya statis doang
// ===================================================
session_start();
$sudahLogin = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>About - Toko Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-body">
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Toko Buku</a>
        <div class="navbar-links">
            <a href="index.php">Home</a>
            <a href="katalog.php">Buku</a>
            <a href="about.php" class="active">About</a>
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
        <h1>Tentang Toko Buku</h1>
        <p>Ini halaman dummy, isinya belum beneran, cuma buat contoh navbar 3 menu.</p>
    </section>

    <section class="about-content">
        <p>Toko Buku ini bukan toko beneran. Ini project latihan PHP + MySQL, dibikin buat belajar CRUD, autentikasi, role management, sampai relasi many-to-many (favorit).</p>
        <p>Kalau ini toko beneran, di sini biasanya ada cerita berdirinya toko, visi misi, alamat, kontak. Tapi karena project latihan, isinya segini aja.</p>
    </section>

    <footer class="footer">
        <p>Toko Buku &mdash; project latihan PHP Sesi 7.</p>
        <p>&copy; <?= date("Y") ?></p>
    </footer>
</body>
</html>
