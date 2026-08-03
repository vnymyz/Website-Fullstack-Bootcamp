<?php
// ===================================================
// FAVORIT SAYA — daftar lengkap buku favorit user (wishlist)
// ===================================================
require_once "includes/auth-check.php";
require_once "config/db.php";

$query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, b.stok, b.gambar_url
          FROM favorit f
          JOIN buku b ON b.id = f.buku_id
          WHERE f.user_id = ?
          ORDER BY f.created_at DESC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$favoritList = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$activePage = 'favorit';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Favorit Saya - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php require "includes/user-sidebar.php"; ?>

        <main class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1"><i class="bi bi-heart-fill text-danger"></i> Buku Favorit Saya</h1>
                <p class="text-muted small mb-0"><?= count($favoritList) ?> buku di wishlist kamu</p>
            </div>
            <a href="katalog.php" class="btn btn-dark d-flex align-items-center gap-2">
                <i class="bi bi-search"></i> Cari Buku Lagi
            </a>
        </div>

        <?php if (empty($favoritList)): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-heart fs-1 d-block mb-3"></i>
                    <p class="mb-3">Belum ada buku yang di-favoritin.</p>
                    <a href="katalog.php" class="btn btn-dark">Jelajahi Katalog Buku</a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($favoritList as $row): ?>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card h-100 shadow-sm border-0">
                            <?php if ($row['gambar_url']): ?>
                                <img src="<?= htmlspecialchars($row['gambar_url']) ?>" class="card-img-top" style="height:200px; object-fit:cover;" alt="<?= htmlspecialchars($row['judul']) ?>">
                            <?php else: ?>
                                <div class="bg-secondary-subtle text-muted d-flex align-items-center justify-content-center" style="height:200px;">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title" style="min-height: 2.6em;"><?= htmlspecialchars($row['judul']) ?></h5>
                                <p class="card-text text-muted small mb-1"><?= htmlspecialchars($row['penulis']) ?> &middot; <?= htmlspecialchars($row['tahun_terbit']) ?></p>
                                <p class="card-text text-muted small">Stok: <?= (int) $row['stok'] ?></p>

                                <form action="favorit-toggle.php" method="POST" class="mt-auto">
                                    <input type="hidden" name="buku_id" value="<?= (int) $row['id'] ?>">
                                    <input type="hidden" name="kembali" value="favorit-saya.php">
                                    <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2">
                                        <i class="bi bi-heartbreak"></i> Hapus dari Favorit
                                    </button>
                                </form>
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
