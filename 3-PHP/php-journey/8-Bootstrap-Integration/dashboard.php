<?php
// ===================================================
// DASHBOARD USER — overview + quick actions, tampilan Bootstrap
// ===================================================
require_once "includes/auth-check.php";

if ($_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
}

require_once "config/db.php";

$totalFavorit = 0;
$stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS jumlah FROM favorit WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$totalFavorit = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['jumlah'];

// Preview 3 favorit terbaru doang -- daftar lengkapnya ada di favorit-saya.php
$query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, b.gambar_url
          FROM favorit f
          JOIN buku b ON b.id = f.buku_id
          WHERE f.user_id = ?
          ORDER BY f.created_at DESC
          LIMIT 3";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$favoritPreview = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php require "includes/user-sidebar.php"; ?>

        <main class="flex-grow-1 p-4" style="max-width: 900px;">
        <!-- Welcome banner -->
        <div class="p-4 rounded-3 text-white mb-4" style="background: linear-gradient(135deg, #4a2c1d 0%, #6b4028 100%);">
            <h1 class="h3 mb-1">Halo, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>
            <p class="mb-0 text-white-50">Selamat datang balik di Toko Buku.</p>
        </div>

        <!-- Stat singkat -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px; height:56px; background-color:#fdeaea;">
                    <i class="bi bi-heart-fill fs-4 text-danger"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $totalFavorit ?></div>
                    <div class="text-muted small">Buku Favorit</div>
                </div>
            </div>
        </div>

        <!-- Quick action -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <a href="favorit-saya.php" class="card shadow-sm border-0 text-decoration-none text-dark h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-heart fs-2 text-danger"></i>
                        <div>
                            <div class="fw-bold">Buku Favorit Saya</div>
                            <div class="text-muted small">Lihat semua wishlist kamu</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </div>
                </a>
            </div>
        </div>

        <!-- Preview favorit terbaru -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Favorit Terbaru</h2>
            <?php if ($totalFavorit > 0): ?>
                <a href="favorit-saya.php" class="small text-decoration-none">Lihat Semua &rarr;</a>
            <?php endif; ?>
        </div>

        <?php if (empty($favoritPreview)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-heart fs-1 d-block mb-2"></i>
                    Belum ada buku yang di-favoritin. <a href="katalog.php">Cari buku di sini</a>.
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($favoritPreview as $row): ?>
                    <div class="col-12 col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <?php if ($row['gambar_url']): ?>
                                <img src="<?= htmlspecialchars($row['gambar_url']) ?>" class="card-img-top" style="height:140px; object-fit:cover;" alt="<?= htmlspecialchars($row['judul']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary-subtle text-muted d-flex align-items-center justify-content-center" style="height:140px;">
                                    <i class="bi bi-image fs-3"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="fw-semibold small"><?= htmlspecialchars($row['judul']) ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($row['penulis']) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
