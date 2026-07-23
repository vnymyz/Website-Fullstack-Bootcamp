<?php
// ===================================================
// CREATE — TAMBAH BARANG BARU
// ===================================================
require_once "config/db.php";

// Ambil daftar kategori buat dropdown
$kategoriList = [];
$kategoriResult = mysqli_query($koneksi, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
while ($row = mysqli_fetch_assoc($kategoriResult)) {
    $kategoriList[] = $row;
}

$errors = [];
// Sticky value biar form gak reset kalau ada error validasi (pola dari Sesi 2)
$nama = $deskripsi = $harga = $stok = $tanggal_masuk = "";
$tersedia = true;
$kategori_id = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $stok = trim($_POST['stok'] ?? '');
    $tanggal_masuk = trim($_POST['tanggal_masuk'] ?? '');
    $tersedia = isset($_POST['tersedia']) ? 1 : 0;
    $kategori_id = trim($_POST['kategori_id'] ?? '');

    // Validasi dasar
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
        // kategori_id boleh kosong (barang belum dikategoriin) -> kirim NULL, bukan string kosong.
        // mysqli_stmt_bind_param ngirim SQL NULL kalau variabel PHP-nya null,
        // gak peduli tipe "i" yang dideklarasiin.
        $kategori_id_value = ($kategori_id === '') ? null : (int) $kategori_id;

        // PREPARED STATEMENT — ini kuncinya. Query pakai "?" sebagai placeholder,
        // nilai aslinya di-bind terpisah lewat mysqli_stmt_bind_param.
        // Ini yang mencegah SQL Injection, BUKAN dengan nge-escape manual string.
        $query = "INSERT INTO barang (nama, deskripsi, harga, stok, tanggal_masuk, tersedia, kategori_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);

        // "ssddsii" = tipe tiap parameter urut sesuai "?" di atas:
        // s = string, d = double/decimal, i = integer
        mysqli_stmt_bind_param(
            $stmt,
            "ssddsii",
            $nama,
            $deskripsi,
            $harga,
            $stok,
            $tanggal_masuk,
            $tersedia,
            $kategori_id_value
        );

        mysqli_stmt_execute($stmt);

        // Redirect after POST — mencegah form ke-submit dobel kalau user refresh
        header("Location: index.php?msg=created");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
</head>
<body>
    <h1>Tambah Barang</h1>

    <?php if (!empty($errors)): ?>
        <ul style="color: red;">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="create.php">
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

        <button type="submit">Simpan</button>
        <a href="index.php">Batal</a>
    </form>
</body>
</html>
