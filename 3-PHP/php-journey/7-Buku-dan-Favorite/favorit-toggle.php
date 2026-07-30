<?php
// ===================================================
// FAVORIT TOGGLE — like/unlike buku
// ===================================================
// Pakai auth-check.php -- kalau belum login, ketendang ke login.php.
// INI YANG BENERAN NENTUIN "harus login buat like", bukan tombolnya
// yang disembunyiin di katalog.php. Tombol disembunyiin cuma UX,
// pengecekan di sini yang beneran nge-block.
require_once "includes/auth-check.php";
require_once "config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: katalog.php");
    exit;
}

$bukuId = isset($_POST['buku_id']) ? (int) $_POST['buku_id'] : 0;
$userId = (int) $_SESSION['user_id'];

if ($bukuId > 0) {
    // Cek udah difavoritin apa belum
    $query = "SELECT id FROM favorit WHERE user_id = ? AND buku_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $sudahFavorit = mysqli_fetch_assoc($result);

    if ($sudahFavorit) {
        // Udah difavoritin -> hapus (unlike)
        $query = "DELETE FROM favorit WHERE user_id = ? AND buku_id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
        mysqli_stmt_execute($stmt);
    } else {
        // Belum difavoritin -> tambah (like)
        $query = "INSERT INTO favorit (user_id, buku_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $bukuId);
        mysqli_stmt_execute($stmt);
    }
}

// Balikin ke halaman yang sama (katalog atau dashboard), bukan hardcode
// satu tujuan -- biar tombol like bisa dipasang di halaman manapun nanti.
// Validasi dikit: tolak kalau isinya URL luar (ada "://"), biar gak
// disalahgunain buat redirect ke website lain (open redirect).
$kembali = $_POST['kembali'] ?? 'katalog.php';
if (strpos($kembali, '://') !== false) {
    $kembali = 'katalog.php';
}
header("Location: $kembali");
exit;
