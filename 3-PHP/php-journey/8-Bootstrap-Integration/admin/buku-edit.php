<?php
// ===================================================
// ADMIN — EDIT BUKU, form Bootstrap
// ===================================================
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: buku.php");
    exit;
}

$errors = [];

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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Buku - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="flex-grow-1 p-4">
            <div class="mx-auto" style="max-width: 520px;">
                <a href="buku.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-3">
                    <i class="bi bi-arrow-left"></i> Kembali ke Kelola Buku
                </a>
                <h1 class="h3 mb-4"><i class="bi bi-pencil-square" style="color:#4a2c1d;"></i> Edit Buku</h1>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="buku-edit.php?id=<?= $id ?>" class="card p-4 shadow-sm border-0">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-book"></i> Judul</label>
                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($judul) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person"></i> Penulis</label>
                        <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($penulis) ?>">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label"><i class="bi bi-calendar3"></i> Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" class="form-control" value="<?= htmlspecialchars($tahunTerbit) ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label"><i class="bi bi-boxes"></i> Stok</label>
                            <input type="number" name="stok" class="form-control" value="<?= htmlspecialchars($stok) ?>">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-image"></i> URL Gambar (opsional)</label>
                        <input type="text" name="gambar_url" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($gambarUrl ?? '') ?>">
                        <div class="form-text">Tempel link gambar dari Unsplash, Google Images, dll. Boleh dikosongin.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1"><i class="bi bi-check-lg"></i> Update</button>
                        <a href="buku.php" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
