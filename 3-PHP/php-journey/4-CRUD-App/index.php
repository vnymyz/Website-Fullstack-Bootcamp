<?php
// ===================================================
// READ — TAMPILIN SEMUA BARANG
// ===================================================
require_once "config/db.php";

// Prepared statement: walaupun query ini gak ada input dari user,
// tetep dibiasain pakai mysqli_prepare biar konsisten sama pola CRUD lain.
// LEFT JOIN ke kategori -> barang yang belum punya kategori (kategori_id NULL) tetep muncul.
$query = "SELECT b.id, b.nama, b.deskripsi, b.harga, b.stok, 
          b.tanggal_masuk, b.tersedia, k.nama_kategori
          FROM barang b
          LEFT JOIN kategori k ON b.kategori_id = k.id
          ORDER BY b.id ASC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Flash message sederhana lewat query string (?msg=...).
// Nanti di Sesi 5+ ini diganti pakai $_SESSION biar lebih rapi & gak keliatan di URL.
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Barang - Sesi 4</title>
</head>
<body>
    <h1>Daftar Barang</h1>

    <?php if ($msg === 'created'): ?>
        <p style="color: green;">Barang berhasil ditambahkan.</p>
    <?php elseif ($msg === 'updated'): ?>
        <p style="color: green;">Barang berhasil diupdate.</p>
    <?php elseif ($msg === 'deleted'): ?>
        <p style="color: green;">Barang berhasil dihapus.</p>
    <?php endif; ?>

    <p><a href="create.php">+ Tambah Barang</a></p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Tersedia</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
            <td><?= $row['nama_kategori'] ? htmlspecialchars($row['nama_kategori']) : '-' ?></td>
            <td>Rp<?= number_format($row['harga']) ?></td>
            <td><?= htmlspecialchars($row['stok']) ?></td>
            <td><?= $row['tersedia'] ? 'Ya' : 'Tidak' ?></td>
            <td>
                <a href="edit.php?id=<?= (int) $row['id'] ?>">Edit</a>
                |
                <form action="delete.php" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus barang ini?');">
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <button type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
