<?php
// ===================================================
// ADMIN — EDIT BUKU (halaman sendiri)
// ===================================================
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: buku.php");
    exit;
}

$errors = [];

// Ambil data buku yang mau diedit dulu (buat isi form)
$query = "SELECT * FROM buku WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$buku = mysqli_fetch_assoc($result);

if (!$buku) {
    header("Location: buku.php");
    exit;
}

$judul = $buku['judul'];
$penulis = $buku['penulis'];
$tahunTerbit = $buku['tahun_terbit'];
$stok = $buku['stok'];
$gambarUrl = $buku['gambar_url'];

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

        $query = "UPDATE buku SET judul = ?, penulis = ?, tahun_terbit = ?, stok = ?, gambar_url = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssiisi", $judul, $penulis, $tahunTerbit, $stok, $gambarUrlValue, $id);
        mysqli_stmt_execute($stmt);

        header("Location: buku.php?msg=updated");
        exit;
    }
}

$activePage = 'buku';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku - Toko Buku</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="content">
            <h1 class="form-title">Edit Buku</h1>

            <?php if (!empty($errors)): ?>
                <ul class="alert-error">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" action="buku-edit.php?id=<?= $id ?>" class="form-buku">
                <label>Judul:</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>">

                <label>Penulis:</label>
                <input type="text" name="penulis" value="<?= htmlspecialchars($penulis) ?>">

                <label>Tahun Terbit:</label>
                <input type="number" name="tahun_terbit" value="<?= htmlspecialchars($tahunTerbit) ?>">

                <label>Stok:</label>
                <input type="number" name="stok" value="<?= htmlspecialchars($stok) ?>">

                <label>URL Gambar (opsional):</label>
                <input type="text" name="gambar_url" placeholder="https://..." value="<?= htmlspecialchars($gambarUrl ?? '') ?>">

                <div class="form-actions">
                    <button type="submit">Update</button>
                    <a href="buku.php" class="btn-batal">Batal</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
