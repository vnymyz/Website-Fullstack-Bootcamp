<?php
// ===================================================
// DASHBOARD — HALAMAN PROTECTED (CONTOH)
// ===================================================
// Baris ini yang bikin halaman ini "protected". Coba komentarin baris ini,
// akses dashboard.php langsung tanpa login — itu bug yang harus dihindari.
require_once "includes/auth-check.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card dashboard-info">
        <h1>Dashboard</h1>
        <p>Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! Kamu berhasil login.</p>

        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</body>
</html>
