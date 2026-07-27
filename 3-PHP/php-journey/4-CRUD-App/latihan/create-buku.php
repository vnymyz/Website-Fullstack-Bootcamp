<?php
// ===================================================
// JAWABAN LATIHAN — CREATE (tambah buku baru)
// ===================================================
require_once "../config/db.php";

$errors = [];
$judul = $penulis = $tahun_terbit = $stok = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahun_terbit = trim($_POST['tahun_terbit'] ?? '');
    $stok = trim($_POST['stok'] ?? '');

    if (empty($judul)) {
        $errors[] = "Judul wajib diisi.";
    }
    if (empty($penulis)) {
        $errors[] = "Penulis wajib diisi.";
    }
    if (!is_numeric($tahun_terbit) || $tahun_terbit < 1000) {
        $errors[] = "Tahun terbit harus angka yang valid.";
    }
    if (!is_numeric($stok) || $stok < 0) {
        $errors[] = "Stok harus angka dan tidak boleh negatif.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO buku (judul, penulis, tahun_terbit, stok) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssii", $judul, $penulis, $tahun_terbit, $stok);
        mysqli_stmt_execute($stmt);

        header("Location: index-buku.php?msg=created");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Tambah Buku</h1>

    <?php if (!empty($errors)): ?>
        <ul class="alert-error">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="create-buku.php" class="form-buku">
        <label>Judul:</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>">

        <label>Penulis:</label>
        <input type="text" name="penulis" value="<?= htmlspecialchars($penulis) ?>">

        <label>Tahun Terbit:</label>
        <input type="number" name="tahun_terbit" value="<?= htmlspecialchars($tahun_terbit) ?>">

        <label>Stok:</label>
        <input type="number" name="stok" value="<?= htmlspecialchars($stok) ?>">

        <button type="submit">Simpan</button>
        <a href="index-buku.php">Batal</a>
    </form>
</body>
</html>
