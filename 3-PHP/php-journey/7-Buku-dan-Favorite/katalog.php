<?php
// ===================================================
// KATALOG — daftar semua buku, bisa dilihat siapa aja
// ===================================================
// Sengaja BUKAN pakai auth-check.php -- katalog boleh diliat orang
// yang belum login. Yang butuh login cuma tombol Like-nya doang,
// dan itu dicek di favorit-toggle.php, bukan di sini.
session_start();

require_once "config/db.php";

$sudahLogin = isset($_SESSION['user_id']);
$userId = $sudahLogin ? (int) $_SESSION['user_id'] : 0;

// --- SEARCH ---
$search = trim($_GET['q'] ?? '');

// --- PAGINATION ---
$perPage = 3;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;

// Hitung total buku (buat tau ada berapa halaman), pakai kondisi
// search yang sama kayak query utama di bawah.
if ($search !== '') {
    $likeParam = "%$search%";
    $countQuery = "SELECT COUNT(*) AS total FROM buku
     WHERE judul LIKE ? OR penulis LIKE ?";
    $stmt = mysqli_prepare($koneksi, $countQuery);
    mysqli_stmt_bind_param($stmt, "ss", $likeParam, $likeParam);
} else {
    $countQuery = "SELECT COUNT(*) AS total FROM buku";
    $stmt = mysqli_prepare($koneksi, $countQuery);
}
mysqli_stmt_execute($stmt);
$totalBuku = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
$totalPage = (int) ceil($totalBuku / $perPage);
if ($totalPage < 1) {
    $totalPage = 1;
}

// LEFT JOIN ke favorit, filter user_id di kondisi JOIN (bukan WHERE)
// biar buku yang BELUM difavoritin tetep muncul (f.id bakal NULL).
// LIMIT/OFFSET taro paling belakang -- itu yang bikin pagination-nya.
if ($search !== '') {
    $likeParam = "%$search%";
    $query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, b.stok, 
              b.gambar_url, f.id AS favorit_id
              FROM buku b
              LEFT JOIN favorit f ON f.buku_id = b.id AND f.user_id = ?
              WHERE b.judul LIKE ? OR b.penulis LIKE ?
              ORDER BY b.judul ASC
              LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "issii", $userId, $likeParam, 
    $likeParam, $perPage, $offset);
} else {
    $query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, 
              b.stok, b.gambar_url, f.id AS favorit_id
              FROM buku b
              LEFT JOIN favorit f ON f.buku_id = b.id AND f.user_id = ?
              ORDER BY b.judul ASC
              LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "iii", $userId, $perPage, $offset);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku - Toko Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="landing-body">
    <nav class="navbar">
        <a href="index.php" class="navbar-brand">Toko Buku</a>
        <div class="navbar-links">
            <a href="index.php">Home</a>
            <a href="katalog.php" class="active">Buku</a>
            <a href="about.php">About</a>
        </div>
        <div class="navbar-right">
            <?php if ($sudahLogin): ?>
                <span class="navbar-user">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="dashboard.php" class="btn-dark">Dashboard</a>
                <a href="logout.php" class="btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-dark">Login</a>
                <a href="register.php" class="btn-outline">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <section class="page-header">
        <h1>Daftar Buku</h1>
        <p>Klik ikon hati buat nandain buku favorit kamu.</p>

        <!-- Form search -- method GET biar kata kuncinya keliatan di URL,
             dan bisa di-bookmark/share. page sengaja gak diikutin, biar
             search baru selalu mulai dari halaman 1. -->
        <form method="GET" action="katalog.php" class="search-form">
            <input type="text" name="q" placeholder="Cari judul atau penulis..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Cari</button>
            <?php if ($search !== ''): ?>
                <a href="katalog.php" class="search-reset">Reset</a>
            <?php endif; ?>
        </form>
    </section>

    <?php if ($totalBuku === 0): ?>
        <p style="text-align:center; padding: 20px;">Gak ada buku yang cocok sama "<?= htmlspecialchars($search) ?>".</p>
    <?php endif; ?>

    <section class="book-grid" id="daftar-buku">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="book-card">
                <?php if ($row['gambar_url']): ?>
                    <img src="<?= htmlspecialchars($row['gambar_url']) ?>" alt="<?= htmlspecialchars($row['judul']) ?>" class="book-cover">
                <?php else: ?>
                    <div class="book-cover book-cover-kosong">Tanpa Gambar</div>
                <?php endif; ?>

                <div class="book-title"><?= htmlspecialchars($row['judul']) ?></div>
                <div class="book-meta"><?= htmlspecialchars($row['penulis']) ?> &middot; <?= htmlspecialchars($row['tahun_terbit']) ?></div>
                <div class="book-meta">Stok: <?= (int) $row['stok'] ?></div>

                <?php if ($sudahLogin): ?>
                    <form action="favorit-toggle.php" method="POST" class="like-form">
                        <input type="hidden" name="buku_id" value="<?= (int) $row['id'] ?>">
                        <input type="hidden" name="kembali" value="katalog.php">
                        <?php if ($row['favorit_id']): ?>
                            <button type="submit" class="btn-like btn-like-active" title="Batal favorit" aria-label="Batal favorit">&#9829;</button>
                        <?php else: ?>
                            <button type="submit" class="btn-like" title="Favoritkan" aria-label="Favoritkan">&#9825;</button>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <a href="login.php" class="btn-like" title="Login buat like" aria-label="Login buat like">&#9825;</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </section>

    <!-- Pagination sederhana: tombol Sebelumnya/Selanjutnya + nomor halaman.
         Kata kunci search (?q=...) ikut dibawa di tiap link, biar pindah
         halaman gak ilangin hasil pencarian. -->
    <?php if ($totalPage > 1): ?>
        <nav class="pagination">
            <?php
            $queryString = $search !== '' ? '&q=' . urlencode($search) : '';
            ?>
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?><?= $queryString ?>" class="pagination-link">&laquo; Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                <a href="?page=<?= $i ?><?= $queryString ?>" class="pagination-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPage): ?>
                <a href="?page=<?= $page + 1 ?><?= $queryString ?>" class="pagination-link">Selanjutnya &raquo;</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

    <footer class="footer">
        <p>Toko Buku &mdash; project latihan PHP Sesi 7.</p>
        <p>&copy; <?= date("Y") ?></p>
    </footer>

    <script>
        // Klik pagination ATAU klik like/favorit = reload halaman baru
        // (bukan AJAX), browser defaultnya reset scroll ke paling atas.
        // Biar posisi scroll gak ke-reset, simpen posisi scroll sekarang
        // ke sessionStorage SEBELUM pindah halaman, terus pas halaman
        // baru kebuka, balikin lagi ke posisi itu.
        document.querySelectorAll('.pagination-link').forEach(function (link) {
            link.addEventListener('click', function () {
                sessionStorage.setItem('katalogScrollY', window.scrollY);
            });
        });

        // Tombol like ada di dalem <form>, klik tombolnya = form submit,
        // bukan klik link biasa -- makanya yang di-dengerin "submit" di
        // form-nya, bukan "click" di tombolnya.
        document.querySelectorAll('.like-form').forEach(function (form) {
            form.addEventListener('submit', function () {
                sessionStorage.setItem('katalogScrollY', window.scrollY);
            });
        });

        var scrollTersimpan = sessionStorage.getItem('katalogScrollY');
        if (scrollTersimpan !== null) {
            window.scrollTo(0, parseInt(scrollTersimpan, 10));
            sessionStorage.removeItem('katalogScrollY');
        }
    </script>
</body>
</html>
