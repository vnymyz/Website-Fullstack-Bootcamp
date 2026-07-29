<?php
// ===================================================
// ADMIN DASHBOARD — sidebar + statistik
// ===================================================
require_once "../includes/admin-check.php";
require_once "../config/db.php";

// Statistik sederhana: total user, jumlah per role
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
