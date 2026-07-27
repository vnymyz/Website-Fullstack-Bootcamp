<?php
// ===================================================
// JAWABAN LATIHAN — READ (tampilin semua buku)
// ===================================================
require_once "../config/db.php";

$query = "SELECT id, judul, penulis, tahun_terbit, stok FROM buku ORDER BY id ASC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRUD Buku - Latihan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Daftar Buku</h1>

    <?php if ($msg === 'created'): ?>
        <div class="alert">Buku berhasil ditambahkan.</div>
    <?php elseif ($msg === 'updated'): ?>
        <div class="alert">Buku berhasil diupdate.</div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert">Buku berhasil dihapus.</div>
    <?php endif; ?>

    <a href="create-buku.php" class="btn-tambah">+ Tambah Buku</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Tahun Terbit</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= htmlspecialchars($row['penulis']) ?></td>
            <td><?= htmlspecialchars($row['tahun_terbit']) ?></td>
            <td><?= htmlspecialchars($row['stok']) ?></td>
            <td>
                <a href="edit-buku.php?id=<?= (int) $row['id'] ?>" class="btn btn-edit">Edit</a>
                <form action="delete-buku.php" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus buku ini?');">
                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                    <button type="submit" class="btn btn-hapus">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
