<?php
// ===================================================
// UPDATE — EDIT BARANG
// ===================================================
require_once "config/db.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$errors = [];

// Ambil daftar kategori buat dropdown
$kategoriList = [];
$kategoriResult = mysqli_query($koneksi, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
while ($row = mysqli_fetch_assoc($kategoriResult)) {
    $kategoriList[] = $row;
}

// Ambil data barang yang mau diedit dulu (buat isi form)
$query = "SELECT * FROM barang WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$barang = mysqli_fetch_assoc($result);

if (!$barang) {
    // ID gak ketemu di database (misal: udah dihapus, atau ngasal di URL)
    header("Location: index.php");
    exit;
}

// Default value form = data yang ada di database
$nama = $barang['nama'];
$deskripsi = $barang['deskripsi'];
$harga = $barang['harga'];
$stok = $barang['stok'];
$tanggal_masuk = $barang['tanggal_masuk'];
$tersedia = $barang['tersedia'];
$kategori_id = $barang['kategori_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $stok = trim($_POST['stok'] ?? '');
    $tanggal_masuk = trim($_POST['tanggal_masuk'] ?? '');
    $tersedia = isset($_POST['tersedia']) ? 1 : 0;
    $kategori_id = trim($_POST['kategori_id'] ?? '');

    if (empty($nama)) {
        $errors[] = "Nama barang wajib diisi.";
    }
    if (!is_numeric($harga) || $harga < 0) {
        $errors[] = "Harga harus angka dan tidak boleh negatif.";
    }
    if (!is_numeric($stok) || $stok < 0) {
        $errors[] = "Stok harus angka dan tidak boleh negatif.";
    }

    if (empty($errors)) {
        $kategori_id_value = ($kategori_id === '') ? null : (int) $kategori_id;

        $query = "UPDATE barang SET nama = ?, deskripsi = ?, harga = ?, stok = ?, tanggal_masuk = ?, tersedia = ?, kategori_id = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "ssddsiii",
            $nama,
            $deskripsi,
            $harga,
            $stok,
            $tanggal_masuk,
            $tersedia,
            $kategori_id_value,
            $id
        );
        mysqli_stmt_execute($stmt);

        header("Location: index.php?msg=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
</head>
<body>
    <h1>Edit Barang</h1>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>">
        <label>Nama:</label><br>
        <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>"><br><br>

        <label>Deskripsi:</label><br>
        <textarea name="deskripsi"><?= htmlspecialchars($deskripsi) ?></textarea><br><br>

        <label>Kategori:</label><br>
        <select name="kategori_id">
            <option value="">-- Tanpa Kategori --</option>
            <?php foreach ($kategoriList as $kategori): ?>
                <option value="<?= (int) $kategori['id'] ?>" <?= ((string) $kategori['id'] === (string) $kategori_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($kategori['nama_kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Harga:</label><br>
        <input type="number" step="0.01" name="harga" value="<?= htmlspecialchars($harga) ?>"><br><br>

        <label>Stok:</label><br>
        <input type="number" name="stok" value="<?= htmlspecialchars($stok) ?>"><br><br>

        <label>Tanggal Masuk:</label><br>
        <input type="date" name="tanggal_masuk" value="<?= htmlspecialchars($tanggal_masuk) ?>"><br><br>

        <label>
            <input type="checkbox" name="tersedia" <?= $tersedia ? 'checked' : '' ?>>
            Tersedia
        </label><br><br>

        <button type="submit">Update</button>
        <a href="index.php">Batal</a>
    </form>
</body>
</html>
