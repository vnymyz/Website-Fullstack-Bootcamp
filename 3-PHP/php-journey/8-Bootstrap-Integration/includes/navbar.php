<?php
// ===================================================
// NAVBAR — partial Bootstrap, dipake di index/katalog/about/dashboard
// ===================================================
// Beda dari sesi 7 (navbar ditulis manual tiap halaman): sekarang
// navbar dipake di 4+ halaman, jadi baru worth dipisah jadi partial
// (di sesi 7, navbar cuma dipake beberapa halaman doang, biar simpel
// dibiarin inline dulu -- baru sekarang jumlah pemakaiannya nambah).
//
// $activeMenu dikirim dari file yang include ini ('home'/'buku'/'about').
$activeMenu = $activeMenu ?? '';
$sudahLogin = isset($_SESSION['user_id']);
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#4a2c1d;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Toko Buku</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $activeMenu === 'home' ? 'active fw-bold' : '' ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeMenu === 'buku' ? 'active fw-bold' : '' ?>" href="katalog.php">Buku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $activeMenu === 'about' ? 'active fw-bold' : '' ?>" href="about.php">About</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if ($sudahLogin): ?>
                    <!-- Dropdown -- nav-item + dropdown, dropdown-toggle di link-nya,
                         dropdown-menu isinya ke-toggle otomatis pas link diklik
                         (data-bs-toggle="dropdown"), gak perlu JS manual. -->
                    <li class="nav-item dropdown">
                        <!-- Sapaan "Halo, {username}" ada di TOMBOL-nya sendiri
                             (yang keliatan di navbar), bukan di dalem menu dropdown-nya.
                             gap-2 = jarak ikon-nama. -->
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5"></i>
                            Halo, <?= htmlspecialchars($_SESSION['username']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="dashboard.php">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a href="login.php" class="btn btn-light btn-sm">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="register.php" class="btn btn-outline-light btn-sm">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
