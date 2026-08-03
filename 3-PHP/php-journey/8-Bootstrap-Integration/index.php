<?php
// ===================================================
// HOMEPAGE — hero + statistik + fitur + preview buku + CTA, semua Bootstrap
// ===================================================
session_start();
require_once "config/db.php";

$sudahLogin = isset($_SESSION['user_id']);

$totalBuku = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM buku"))['jumlah'];
$totalUsers = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM users"))['jumlah'];
$totalFavorit = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM favorit"))['jumlah'];

$query = "SELECT id, judul, penulis, tahun_terbit, gambar_url FROM buku ORDER BY id DESC LIMIT 3";
$result = mysqli_query($koneksi, $query);

$activeMenu = 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -- paket ikon terpisah dari Bootstrap inti, CDN sendiri -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <?php require "includes/navbar.php"; ?>

    <!-- ============ HERO ============ -->
    <section class="text-white" style="background: linear-gradient(135deg, #4a2c1d 0%, #6b4028 100%);">
        <div class="container py-5">
            <div class="row align-items-center py-5">
                <div class="col-lg-6">
                    <span class="badge bg-light text-dark mb-3 px-3 py-2">📚 Toko buku online</span>
                    <h1 class="display-4 fw-bold mb-3">Temukan Buku yang Bikin Betah Baca</h1>
                    <p class="fs-5 text-white-50 mb-4">
                        Jelajahi koleksi buku, simpan favorit kamu, dan pantau ketersediaan stok —
                        semua dalam satu tempat yang gampang dipakai.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($sudahLogin): ?>
                            <a href="katalog.php" class="btn btn-light btn-lg px-4">
                                <i class="bi bi-book"></i> Jelajahi Buku
                            </a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-light btn-lg px-4">Daftar Gratis</a>
                            <a href="katalog.php" class="btn btn-outline-light btn-lg px-4">Lihat Katalog</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center">
                    <i class="bi bi-book-half" style="font-size: 14rem; opacity: 0.25;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ STATISTIK SINGKAT ============ -->
    <section class="bg-white border-bottom">
        <div class="container py-4">
            <div class="row text-center g-4">
                <div class="col-4">
                    <div class="fs-2 fw-bold" style="color:#4a2c1d;"><?= $totalBuku ?>+</div>
                    <div class="text-muted small">Judul Buku</div>
                </div>
                <div class="col-4">
                    <div class="fs-2 fw-bold" style="color:#4a2c1d;"><?= $totalUsers ?>+</div>
                    <div class="text-muted small">Pembaca Terdaftar</div>
                </div>
                <div class="col-4">
                    <div class="fs-2 fw-bold" style="color:#4a2c1d;"><?= $totalFavorit ?>+</div>
                    <div class="text-muted small">Buku Difavoritkan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ KENAPA TOKO BUKU ============ -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Kenapa Belanja di Toko Buku</h2>
                <p class="text-muted">Tiga alasan simpel kenapa pembaca betah di sini.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-collection fs-1" style="color:#4a2c1d;"></i>
                            <h5 class="mt-3">Katalog Lengkap</h5>
                            <p class="text-muted mb-0">Dari novel sampai buku pengembangan diri, tinggal cari lewat fitur pencarian.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-shield-check fs-1" style="color:#4a2c1d;"></i>
                            <h5 class="mt-3">Akun Aman</h5>
                            <p class="text-muted mb-0">Password kamu di-hash, gak pernah disimpan dalam bentuk teks asli.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-heart fs-1" style="color:#4a2c1d;"></i>
                            <h5 class="mt-3">Favorit Personal</h5>
                            <p class="text-muted mb-0">Tandain buku yang kamu suka, buka lagi kapan aja lewat dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ BUKU TERBARU ============ -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 fw-bold mb-0">Buku Terbaru</h2>
                <a href="katalog.php" class="text-decoration-none">Lihat Semua &rarr;</a>
            </div>
            <div class="row g-4">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-12 col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <?php if ($row['gambar_url']): ?>
                                <img src="<?= htmlspecialchars($row['gambar_url']) ?>" class="card-img-top" style="height:200px; object-fit:cover;" alt="<?= htmlspecialchars($row['judul']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary-subtle text-muted d-flex align-items-center justify-content-center" style="height:200px;">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['judul']) ?></h5>
                                <p class="card-text text-muted small mb-0"><?= htmlspecialchars($row['penulis']) ?> &middot; <?= htmlspecialchars($row['tahun_terbit']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- ============ CTA ============ -->
    <?php if (!$sudahLogin): ?>
    <section class="text-white text-center py-5" style="background-color:#4a2c1d;">
        <div class="container">
            <h2 class="fw-bold mb-2">Siap Mulai Baca?</h2>
            <p class="text-white-50 mb-4">Daftar gratis, favoritin buku yang kamu suka, mulai dari sekarang.</p>
            <a href="register.php" class="btn btn-light btn-lg px-4">Daftar Sekarang</a>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============ FOOTER ============ -->
    <footer class="text-white pt-5 pb-4" style="background-color:#2c1810;">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="fw-bold">Toko Buku</h5>
                    <p class="text-white-50 small">Project latihan PHP + MySQL — CRUD, autentikasi, role management, relasi many-to-many, dan Bootstrap. Bukan toko beneran.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Navigasi</h6>
                    <ul class="list-unstyled small">
                        <li><a href="index.php" class="link-light link-opacity-75 text-decoration-none">Home</a></li>
                        <li><a href="katalog.php" class="link-light link-opacity-75 text-decoration-none">Buku</a></li>
                        <li><a href="about.php" class="link-light link-opacity-75 text-decoration-none">About</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Kontak (Dummy)</h6>
                    <ul class="list-unstyled small text-white-50">
                        <li><i class="bi bi-envelope"></i> halo@tokobuku.test</li>
                        <li><i class="bi bi-geo-alt"></i> Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <p class="text-center text-white-50 small mb-0">&copy; <?= date("Y") ?> Toko Buku &mdash; project latihan PHP Sesi 8.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
