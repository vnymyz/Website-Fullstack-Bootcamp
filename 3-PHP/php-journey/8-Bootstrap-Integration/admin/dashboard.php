<?php
// ===================================================
// ADMIN DASHBOARD — sidebar Bootstrap + statistik (users, buku, favorit)
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="flex-grow-1 p-4" style="max-width: 1100px;">
            <!-- Welcome banner -->
            <div class="p-4 rounded-3 text-white mb-4" style="background: linear-gradient(135deg, #4a2c1d 0%, #6b4028 100%);">
                <h1 class="h3 mb-1">Halo, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>
                <p class="mb-0 text-white-50">Ini ringkasan toko buku kamu hari ini.</p>
            </div>

            <!-- Stat cards, tiap kartu punya ikon dalam lingkaran warna -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px; height:56px; background-color:#e7f1ff;">
                                <i class="bi bi-people fs-4 text-primary"></i>
                            </div>
                            <div>
                                <div class="fs-3 fw-bold"><?= $totalUsers ?></div>
                                <div class="text-muted small">Total Users</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px; height:56px; background-color:#fdeee3;">
                                <i class="bi bi-book fs-4" style="color:#4a2c1d;"></i>
                            </div>
                            <div>
                                <div class="fs-3 fw-bold"><?= $totalBuku ?></div>
                                <div class="text-muted small">Total Buku</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px; height:56px; background-color:#fdeaea;">
                                <i class="bi bi-heart-fill fs-4 text-danger"></i>
                            </div>
                            <div>
                                <div class="fs-3 fw-bold"><?= $totalFavorit ?></div>
                                <div class="text-muted small">Total Favorit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick action cards, gantiin tombol polos -->
            <h2 class="h6 text-muted text-uppercase mb-3">Aksi Cepat</h2>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <a href="buku.php" class="card shadow-sm border-0 text-decoration-none text-dark h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="bi bi-book fs-2" style="color:#4a2c1d;"></i>
                            <div>
                                <div class="fw-bold">Kelola Buku</div>
                                <div class="text-muted small">Tambah, edit, atau hapus buku dari katalog</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <a href="users.php" class="card shadow-sm border-0 text-decoration-none text-dark h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="bi bi-people fs-2 text-primary"></i>
                            <div>
                                <div class="fw-bold">Kelola Users</div>
                                <div class="text-muted small">Ubah role atau hapus akun user</div>
                            </div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
