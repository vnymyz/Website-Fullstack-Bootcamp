<?php
// ===================================================
// JAWABAN LATIHAN — UPDATE (edit buku)
// ===================================================
require_once "../config/db.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index-buku.php");
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
    header("Location: index-buku.php");
    exit;
}

$judul = $buku['judul'];
$penulis = $buku['penulis'];
$tahun_terbit = $buku['tahun_terbit'];
$stok = $buku['stok'];

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
        $query = "UPDATE buku SET judul = ?, penulis = ?, tahun_terbit = ?, stok = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ssiii", $judul, $penulis, $tahun_terbit, $stok, $id);
        mysqli_stmt_execute($stmt);

        header("Location: index-buku.php?msg=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Edit Buku</h1>

    <?php if (!empty($errors)): ?>
        <ul class="alert-error">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="edit-buku.php?id=<?= (int) $id ?>" class="form-buku">
        <label>Judul:</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>">

        <label>Penulis:</label>
        <input type="text" name="penulis" value="<?= htmlspecialchars($penulis) ?>">

        <label>Tahun Terbit:</label>
        <input type="number" name="tahun_terbit" value="<?= htmlspecialchars($tahun_terbit) ?>">

        <label>Stok:</label>
        <input type="number" name="stok" value="<?= htmlspecialchars($stok) ?>">

        <button type="submit">Update</button>
        <a href="index-buku.php">Batal</a>
    </form>
</body>
</html>
