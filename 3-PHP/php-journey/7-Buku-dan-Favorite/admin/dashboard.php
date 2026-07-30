<?php
// ===================================================
// ADMIN DASHBOARD — sidebar + statistik (users + buku + favorit)
// ===================================================
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$totalUsers = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM users"))['jumlah'];
$totalBuku = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM buku"))['jumlah'];
$totalFavorit = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM favorit"))['jumlah'];

$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Toko Buku</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="content">
            <h1>Dashboard Admin</h1>
            <p>Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>.</p>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $totalUsers ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $totalBuku ?></div>
                    <div class="stat-label">Total Buku</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $totalFavorit ?></div>
                    <div class="stat-label">Total Favorit</div>
                </div>
            </div>

            <p>
                <a href="buku.php" class="btn-admin">Kelola Buku &rarr;</a>
                <a href="users.php" class="btn-admin">Kelola Users &rarr;</a>
            </p>
        </main>
    </div>
</body>
</html>
