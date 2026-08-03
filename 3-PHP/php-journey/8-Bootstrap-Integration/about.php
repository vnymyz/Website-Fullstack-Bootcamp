<?php
// ===================================================
// ABOUT — cerita/konten dummy tentang "Toko Buku", tampilan Bootstrap
// ===================================================
session_start();
require_once "config/db.php";

$totalBuku = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM buku"))['jumlah'];
$totalUsers = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jumlah FROM users"))['jumlah'];

$activeMenu = 'about';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <?php require "includes/navbar.php"; ?>

    <!-- ============ HEADER HALAMAN ============ -->
    <section class="text-white text-center" style="background: linear-gradient(135deg, #4a2c1d 0%, #6b4028 100%);">
        <div class="container py-5">
            <h1 class="fw-bold mb-2">Tentang Toko Buku</h1>
            <p class="text-white-50 mb-0">Cerita singkat di balik project ini.</p>
        </div>
    </section>

    <!-- ============ CERITA ============ -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <i class="bi bi-journal-bookmark" style="font-size: 10rem; color:#4a2c1d; opacity:0.15;"></i>
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3">Awalnya Cuma Latihan CRUD</h2>
                    <p>Toko Buku lahir dari project belajar PHP native + MySQL, dimulai dari hal paling dasar: nampilin teks lewat <code>echo</code>. Dari situ, sedikit demi sedikit ditambahin form, koneksi database, sistem login, sampai akhirnya jadi "toko buku" kecil kayak yang kamu liat sekarang.</p>
                    <p class="mb-0">Setiap fitur di sini — daftar buku, login, favorit, panel admin — dibangun sesi demi sesi, sambil belajar konsep baru di tiap tahap: superglobals, prepared statement, session, role management, relasi database, sampai Bootstrap yang lagi kamu liat ini.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ MISI ============ -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Yang Kami (Coba) Tawarkan</h2>
                <p class="text-muted">Meski cuma project belajar, targetnya tetep dibikin kerasa kayak aplikasi beneran.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-search fs-1" style="color:#4a2c1d;"></i>
                            <h6 class="mt-3 fw-bold">Cari Cepat</h6>
                            <p class="text-muted small mb-0">Cari buku berdasarkan judul atau penulis.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-heart fs-1" style="color:#4a2c1d;"></i>
                            <h6 class="mt-3 fw-bold">Simpan Favorit</h6>
                            <p class="text-muted small mb-0">Tandain buku yang kamu suka buat dibuka lagi nanti.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-person-check fs-1" style="color:#4a2c1d;"></i>
                            <h6 class="mt-3 fw-bold">Login Aman</h6>
                            <p class="text-muted small mb-0">Password di-hash, session dijaga tiap halaman protected.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-3">
                        <div class="card-body">
                            <i class="bi bi-speedometer2 fs-1" style="color:#4a2c1d;"></i>
                            <h6 class="mt-3 fw-bold">Panel Admin</h6>
                            <p class="text-muted small mb-0">Admin kelola katalog buku & user dari 1 dashboard.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ STATISTIK + DISCLAIMER ============ -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-5 text-center">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="fs-1 fw-bold" style="color:#4a2c1d;"><?= $totalBuku ?></div>
                            <div class="text-muted small">Buku Terdaftar</div>
                        </div>
                        <div class="col-6">
                            <div class="fs-1 fw-bold" style="color:#4a2c1d;"><?= $totalUsers ?></div>
                            <div class="text-muted small">Pembaca Terdaftar</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong> Toko Buku bukan toko beneran — gak ada transaksi/pembayaran sungguhan di sini. Semua data (buku, user) cuma buat keperluan latihan, boleh dihapus/diubah kapan aja lewat panel admin.
                    </div>
                </div>
            </div>
        </div>
    </section>

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
