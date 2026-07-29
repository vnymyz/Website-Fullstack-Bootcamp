<?php
// ===================================================
// DASHBOARD — entry point setelah login
// ===================================================
require_once "includes/auth-check.php";

// Redirect admin ke dashboard admin (sidebar + statistik).
// Ini cuma buat KENYAMANAN (biar admin gak liat dashboard versi polos),
// BUKAN pengganti admin-check.php -- admin/dashboard.php tetep
// punya pengecekan role sendiri di server, gak ngandelin redirect ini.
if ($_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User - Toko Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card dashboard-info">
        <h1>Dashboard User</h1>
        <p>Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! Role kamu: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>

        <p>
            <a href="index.php" class="btn-dark">Home</a>
            <a href="logout.php" class="btn-outline">Logout</a>
        </p>
    </div>
</body>
</html>
