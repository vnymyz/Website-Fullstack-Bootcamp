<?php
// ===================================================
// SIDEBAR ADMIN — partial, di-include di tiap halaman admin/
// ===================================================
// $activePage dikirim dari file yang include ini, buat nandain
// menu mana yang lagi aktif (kasih class "active").
$activePage = $activePage ?? '';
?>
<nav class="sidebar">
    <div class="sidebar-brand">Toko Buku</div>
    <a href="../index.php">Home</a>
    <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="users.php" class="<?= $activePage === 'users' ? 'active' : '' ?>">Kelola Users</a>
    <a href="../logout.php" class="sidebar-logout">Logout</a>
</nav>
