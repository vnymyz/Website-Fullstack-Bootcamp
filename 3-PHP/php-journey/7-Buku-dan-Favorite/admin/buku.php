<?php
// ===================================================
// ADMIN — DAFTAR BUKU (LIST + HAPUS)
// ===================================================
// Cuma nampilin table + tombol aksi. Form tambah/edit dipindah ke
// halaman sendiri (buku-tambah.php / buku-edit.php) -- gak digabung
// di 1 halaman kayak sebelumnya, biar gak numpuk.
require_once "../includes/admin-check.php";
require_once "../config/db.php";

// --- Aksi: hapus buku ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset
    ($_POST['action']) && $_POST['action'] === 'hapus') {
    $id = (int) $_POST['id'];

    // Baris favorit yang nunjuk ke buku ini ikut kehapus otomatis (ON DELETE CASCADE)
    $query = "DELETE FROM buku WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    header("Location: buku.php?msg=deleted");
    exit;
}

// --- List semua buku + jumlah favorit tiap buku ---
$query = "SELECT b.id, b.judul, b.penulis, b.tahun_terbit, 
          b.stok, b.gambar_url, COUNT(f.id) AS jumlah_favorit
          FROM buku b
          LEFT JOIN favorit f ON f.buku_id = b.id
          GROUP BY b.id
          ORDER BY b.id ASC";
$result = mysqli_query($koneksi, $query);

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
$activePage = 'buku';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Buku - Toko Buku</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="content">
            <h1>Kelola Buku</h1>

            <?php if ($msg === 'created'): ?>
                <div class="alert">Buku berhasil ditambah.</div>
            <?php elseif ($msg === 'updated'): ?>
                <div class="alert">Buku berhasil diedit.</div>
            <?php elseif ($msg === 'deleted'): ?>
                <div class="alert">Buku berhasil dihapus.</div>
            <?php endif; ?>

            <p><a href="buku-tambah.php" class="btn-admin">+ Tambah Buku</a></p>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Disukai</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td>
                        <?php if ($row['gambar_url']): ?>
                            <img src="<?= htmlspecialchars($row['gambar_url']) ?>" alt="" class="thumb-buku">
                        <?php else: ?>
                            <span class="thumb-kosong">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= htmlspecialchars($row['penulis']) ?></td>
                    <td><?= htmlspecialchars($row['tahun_terbit']) ?></td>
                    <td><?= (int) $row['stok'] ?></td>
                    <td><span class="favorit-count">&hearts; <?= (int) $row['jumlah_favorit'] ?></span></td>
                    <td>
                        <div class="table-actions">
                            <a href="buku-edit.php?id=<?= (int) $row['id'] ?>" class="btn btn-edit">Edit</a>
                            <form action="buku.php" method="POST" onsubmit="return confirm('Yakin hapus buku ini? Semua favorit yang nunjuk ke buku ini juga ikut kehapus.');">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button type="submit" class="btn btn-hapus">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </main>
    </div>
</body>
</html>
