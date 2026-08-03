<?php
// ===================================================
// SIDEBAR ADMIN — Bootstrap list-group + Bootstrap Icons
// ===================================================
$activePage = $activePage ?? '';
?>
<div class="d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px; min-height: 100vh; background-color:#2c3e50;">
    <a href="../index.php" class="d-flex align-items-center gap-2 mb-1 text-white text-decoration-none fs-5 fw-bold">
        <i class="bi bi-book-half"></i> Toko Buku
    </a>
    <div class="text-white-50 small mb-3">Admin Panel</div>
    <hr class="mt-0">

    <ul class="nav nav-pills flex-column gap-1 mb-auto">
        <li class="nav-item">
            <a href="../index.php" class="nav-link d-flex align-items-center gap-2 text-white-50">
                <i class="bi bi-house"></i> Kembali ke Home
            </a>
        </li>
        <li><hr class="text-white-50 my-2"></li>
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link d-flex align-items-center gap-2 <?= $activePage === 'dashboard' ? 'active' : 'text-white-50' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="buku.php" class="nav-link d-flex align-items-center gap-2 <?= $activePage === 'buku' ? 'active' : 'text-white-50' ?>">
                <i class="bi bi-book"></i> Kelola Buku
            </a>
        </li>
        <li class="nav-item">
            <a href="users.php" class="nav-link d-flex align-items-center gap-2 <?= $activePage === 'users' ? 'active' : 'text-white-50' ?>">
                <i class="bi bi-people"></i> Kelola Users
            </a>
        </li>
    </ul>

    <hr>
    <div class="d-flex align-items-center gap-2 mb-3">
        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px;">
            <?= strtoupper(substr($_SESSION['username'] ?? '?', 0, 1)) ?>
        </div>
        <div class="text-truncate">
            <div class="text-white small text-truncate"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></div>
            <div class="text-white-50" style="font-size: 0.75rem;">Admin</div>
        </div>
    </div>
    <a href="../logout.php" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center gap-1">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>
