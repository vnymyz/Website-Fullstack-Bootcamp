<?php
// ===================================================
// ADMIN — TAMBAH BUKU (halaman sendiri)
// ===================================================
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$errors = [];
$judul = $penulis = $tahunTerbit = $stok = $gambarUrl = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $tahunTerbit = trim($_POST['tahun_terbit'] ?? '');
    $stok = trim($_POST['stok'] ?? '');
    $gambarUrl = trim($_POST['gambar_url'] ?? '');

    if (empty($judul)) {
        $errors[] = "Judul wajib diisi.";
    }
    if (empty($penulis)) {
        $errors[] = "Penulis wajib diisi.";
    }
    if (!is_numeric($tahunTerbit) || $tahunTerbit < 1000) {
        $errors[] = "Tahun terbit gak valid.";
    }
    if (!is_numeric($stok) || $stok < 0) {
        $errors[] = "Stok harus angka, gak boleh negatif.";
    }
    if (!empty($gambarUrl) && !filter_var($gambarUrl, FILTER_VALIDATE_URL)) {
        $errors[] = "URL gambar gak valid.";
    }

    if (empty($errors)) {
        $gambarUrlValue = $gambarUrl === '' ? null : $gambarUrl;

        $query = "INSERT INTO buku (judul, penulis, tahun_terbit, stok, gambar_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssiis", $judul, $penulis, $tahunTerbit, $stok, $gambarUrlValue);
        mysqli_stmt_execute($stmt);

        header("Location: buku.php?msg=created");
        exit;
    }
}

$activePage = 'buku';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Buku - Toko Buku</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="content">
            <h1 class="form-title">Tambah Buku</h1>

            <?php if (!empty($errors)): ?>
                <ul class="alert-error">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" action="buku-tambah.php" class="form-buku">
                <label>Judul:</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>">

                <label>Penulis:</label>
                <input type="text" name="penulis" value="<?= htmlspecialchars($penulis) ?>">

                <label>Tahun Terbit:</label>
                <input type="number" name="tahun_terbit" value="<?= htmlspecialchars($tahunTerbit) ?>">

                <label>Stok:</label>
                <input type="number" name="stok" value="<?= htmlspecialchars($stok) ?>">

                <label>URL Gambar (opsional):</label>
                <input type="text" name="gambar_url" placeholder="https://..." value="<?= htmlspecialchars($gambarUrl) ?>">

                <div class="form-actions">
                    <button type="submit">Simpan</button>
                    <a href="buku.php" class="btn-batal">Batal</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
