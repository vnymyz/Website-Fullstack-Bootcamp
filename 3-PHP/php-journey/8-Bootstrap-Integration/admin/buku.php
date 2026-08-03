<?php
// ===================================================
// ADMIN — DAFTAR BUKU (LIST + HAPUS), Bootstrap table + modal + pagination
// ===================================================
require_once "../includes/admin-check.php";
require_once "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus') {
    $id = (int) $_POST['id'];
    $query = "DELETE FROM buku WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: buku.php?msg=deleted");
    exit;
}

// --- Pagination: 10 baris per halaman ---
$perPage = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;

$totalBuku = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM buku"))['total'];
$totalPage = max(1, (int) ceil($totalBuku / $perPage));

$query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, b.stok, b.gambar_url, COUNT(f.id) AS jumlah_favorit
          FROM buku b
          LEFT JOIN favorit f ON f.buku_id = b.id
          GROUP BY b.id
          ORDER BY b.id ASC
          LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "ii", $perPage, $offset);
mysqli_stmt_execute($stmt);
$bukuList = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
$activePage = 'buku';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Buku - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="flex-grow-1 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Kelola Buku</h1>
                    <p class="text-muted small mb-0"><?= $totalBuku ?> buku terdaftar</p>
                </div>
                <a href="buku-tambah.php" class="btn btn-dark d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i> Tambah Buku
                </a>
            </div>

            <?php if ($msg === 'created'): ?>
                <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> Buku berhasil ditambah.</div>
            <?php elseif ($msg === 'updated'): ?>
                <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> Buku berhasil diedit.</div>
            <?php elseif ($msg === 'deleted'): ?>
                <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> Buku berhasil dihapus.</div>
            <?php endif; ?>

            <?php if (empty($bukuList)): ?>
                <!-- Empty state -- lebih enak diliat daripada table kosong doang -->
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-book fs-1 d-block mb-2"></i>
                    Belum ada buku. <a href="buku-tambah.php">Tambah buku pertama</a>.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover bg-white align-middle shadow-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Tahun</th>
                                <th>Stok</th>
                                <th>Disukai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $nomor = $offset + 1; ?>
                            <?php foreach ($bukuList as $row): ?>
                            <tr>
                                <!-- Nomor urut tampilan, BUKAN id database -- id di database bisa
                                     bolong-bolong (11, 13, 14, ...) abis ada yang dihapus. $offset
                                     bikin nomor lanjut nyambung antar halaman (halaman 2 mulai dari 11). -->
                                <td class="text-muted"><?= $nomor++ ?>.</td>
                                <td>
                                    <?php if ($row['gambar_url']): ?>
                                        <img src="<?= htmlspecialchars($row['gambar_url']) ?>" style="width:44px; height:56px; object-fit:cover; border-radius:4px;" alt="">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center bg-light text-muted rounded" style="width:44px; height:56px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['judul']) ?></td>
                                <td class="text-muted"><?= htmlspecialchars($row['penulis']) ?></td>
                                <td><?= htmlspecialchars($row['tahun_terbit']) ?></td>
                                <td>
                                    <!-- Badge stok -- merah kalau stok abis/dikit, biar admin langsung sadar -->
                                    <?php if ((int) $row['stok'] === 0): ?>
                                        <span class="badge text-bg-danger">Habis</span>
                                    <?php elseif ((int) $row['stok'] <= 5): ?>
                                        <span class="badge text-bg-warning"><?= (int) $row['stok'] ?> tersisa</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-light border"><?= (int) $row['stok'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="text-danger fw-bold"><i class="bi bi-heart-fill"></i> <?= (int) $row['jumlah_favorit'] ?></span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="buku-edit.php?id=<?= (int) $row['id'] ?>" class="btn btn-sm btn-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#hapusModal<?= (int) $row['id'] ?>" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="hapusModal<?= (int) $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger"></i> Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Yakin mau hapus buku <strong><?= htmlspecialchars($row['judul']) ?></strong>? Semua favorit yang nunjuk ke buku ini ikut kehapus.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="buku.php" method="POST">
                                                <input type="hidden" name="action" value="hapus">
                                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPage > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">&laquo;</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $totalPage ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= min($totalPage, $page + 1) ?>">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
