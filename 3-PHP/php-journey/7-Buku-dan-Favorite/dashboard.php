<?php
// ===================================================
// DASHBOARD USER — sekarang nampilin buku favorit beneran
// ===================================================
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
