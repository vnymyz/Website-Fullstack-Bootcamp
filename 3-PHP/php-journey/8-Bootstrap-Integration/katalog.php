<?php
// ===================================================
// KATALOG — logic sama kayak sesi 7 (search + pagination + like),
// tampilannya diganti total pake komponen Bootstrap
// ===================================================
session_start();
require_once "config/db.php";

$sudahLogin = isset($_SESSION['user_id']);
$userId = $sudahLogin ? (int) $_SESSION['user_id'] : 0;

$search = trim($_GET['q'] ?? '');

$perPage = 6;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;

if ($search !== '') {
    $likeParam = "%$search%";
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM buku WHERE judul LIKE ? OR penulis LIKE ?");
    mysqli_stmt_bind_param($stmt, "ss", $likeParam, $likeParam);
} else {
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM buku");
}
mysqli_stmt_execute($stmt);
$totalBuku = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
$totalPage = max(1, (int) ceil($totalBuku / $perPage));

if ($search !== '') {
    $likeParam = "%$search%";
    $query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, b.stok, b.gambar_url, f.id AS favorit_id
              FROM buku b
              LEFT JOIN favorit f ON f.buku_id = b.id AND f.user_id = ?
              WHERE b.judul LIKE ? OR b.penulis LIKE ?
              ORDER BY b.judul ASC
              LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "issii", $userId, $likeParam, $likeParam, $perPage, $offset);
} else {
    $query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, b.stok, b.gambar_url, f.id AS favorit_id
              FROM buku b
              LEFT JOIN favorit f ON f.buku_id = b.id AND f.user_id = ?
              ORDER BY b.judul ASC
              LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "iii", $userId, $perPage, $offset);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$activeMenu = 'buku';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <?php require "includes/navbar.php"; ?>

    <div class="container py-5">
        <h1 class="text-center mb-2">Daftar Buku</h1>
        <p class="text-center text-muted mb-4">Klik ikon hati buat nandain buku favorit kamu.</p>

        <form method="GET" action="katalog.php" class="d-flex justify-content-center gap-2 mb-4">
            <input type="text" name="q" class="form-control" style="max-width:300px;" placeholder="Cari judul atau penulis..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-dark">Cari</button>
            <?php if ($search !== ''): ?>
                <a href="katalog.php" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </form>

        <?php if ($totalBuku === 0): ?>
            <p class="text-center text-muted">Gak ada buku yang cocok sama "<?= htmlspecialchars($search) ?>".</p>
        <?php endif; ?>

        <div class="row g-4" id="daftar-buku">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card h-100">
                        <?php if ($row['gambar_url']): ?>
                            <img src="<?= htmlspecialchars($row['gambar_url']) ?>" class="card-img-top" style="height:200px; object-fit:cover;" alt="<?= htmlspecialchars($row['judul']) ?>">
                        <?php else: ?>
                            <div class="bg-secondary-subtle text-muted d-flex align-items-center justify-content-center" style="height:200px;">Tanpa Gambar</div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title" style="min-height: 2.6em;"><?= htmlspecialchars($row['judul']) ?></h5>
                            <p class="card-text text-muted small mb-1"><?= htmlspecialchars($row['penulis']) ?> &middot; <?= htmlspecialchars($row['tahun_terbit']) ?></p>
                            <p class="card-text text-muted small">Stok: <?= (int) $row['stok'] ?></p>

                            <div class="mt-auto">
                                <?php if ($sudahLogin): ?>
                                    <form action="favorit-toggle.php" method="POST" class="like-form">
                                        <input type="hidden" name="buku_id" value="<?= (int) $row['id'] ?>">
                                        <input type="hidden" name="kembali" value="katalog.php">
                                        <?php if ($row['favorit_id']): ?>
                                            <button type="submit" class="btn btn-danger rounded-circle" style="width:44px; height:44px;" title="Batal favorit">&#9829;</button>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-outline-danger rounded-circle" style="width:44px; height:44px;" title="Favoritkan">&#9825;</button>
                                        <?php endif; ?>
                                    </form>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-outline-danger rounded-circle" style="width:44px; height:44px;" title="Login buat like">&#9825;</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($totalPage > 1): ?>
            <!-- Komponen pagination bawaan Bootstrap, tinggal pakai class .pagination -->
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php
                    $queryString = $search !== '' ? '&q=' . urlencode($search) : '';
                    ?>
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link pagination-link" href="?page=<?= max(1, $page - 1) ?><?= $queryString ?>">&laquo;</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link pagination-link" href="?page=<?= $i ?><?= $queryString ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPage ? 'disabled' : '' ?>">
                        <a class="page-link pagination-link" href="?page=<?= min($totalPage, $page + 1) ?><?= $queryString ?>">&raquo;</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

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
                        <li>halo@tokobuku.test</li>
                        <li>Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary">
            <p class="text-center text-white-50 small mb-0">&copy; <?= date("Y") ?> Toko Buku &mdash; project latihan PHP Sesi 8.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sama kayak sesi 7 -- simpen posisi scroll sebelum pindah halaman
        // (pagination) atau like/unlike, biar gak lompat ke atas pas reload.
        document.querySelectorAll('.pagination-link').forEach(function (link) {
            link.addEventListener('click', function () {
                sessionStorage.setItem('katalogScrollY', window.scrollY);
            });
        });
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
