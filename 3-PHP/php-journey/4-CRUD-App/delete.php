<?php
// ===================================================
// DELETE — HAPUS BARANG
// ===================================================
// Sengaja cuma nerima POST, bukan GET (?id=... di URL/link langsung).
// Kalau delete bisa lewat GET, tinggal taro link/gambar dari luar yang
// nunjuk ke delete.php?id=1, korban ke-klik doang barangnya kehapus.
// Makanya di index.php, tombol hapus dibungkus <form method="POST">.
require_once "config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    $query = "DELETE FROM barang WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: index.php?msg=deleted");
exit;
