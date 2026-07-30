<?php
// ===================================================
// SIDEBAR ADMIN — partial, di-include di tiap halaman admin/
// ===================================================
$activePage = $activePage ?? '';
?>
<nav class="sidebar">
    <div class="sidebar-brand">Toko Buku</div>
    <a href="../index.php">Home</a>
    <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
    <a href="buku.php" class="<?= $activePage === 'buku' ? 'active' : '' ?>">Kelola Buku</a>
    <a href="users.php" class="<?= $activePage === 'users' ? 'active' : '' ?>">Kelola Users</a>
    <a href="../logout.php" class="sidebar-logout">Logout</a>
</nav>
