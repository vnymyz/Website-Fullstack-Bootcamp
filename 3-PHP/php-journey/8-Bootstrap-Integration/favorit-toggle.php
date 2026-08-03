<?php
// ===================================================
// FAVORIT TOGGLE — sama persis kayak sesi 7, gak ada tampilan
// ===================================================
require_once "includes/auth-check.php";
require_once "config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: katalog.php");
    exit;
}

$bukuId = isset($_POST['buku_id']) ? (int) $_POST['buku_id'] : 0;
$userId = (int) $_SESSION['user_id'];

if ($bukuId > 0) {
    $query = "SELECT id FROM favorit WHERE user_id = ? AND buku_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $sudahFavorit = mysqli_fetch_assoc($result);

    if ($sudahFavorit) {
        $query = "DELETE FROM favorit WHERE user_id = ? AND buku_id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
        mysqli_stmt_execute($stmt);
    } else {
        $query = "INSERT INTO favorit (user_id, buku_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
        mysqli_stmt_execute($stmt);
    }
}

$kembali = $_POST['kembali'] ?? 'katalog.php';
if (strpos($kembali, '://') !== false) {
    $kembali = 'katalog.php';
}
header("Location: $kembali");
exit;
