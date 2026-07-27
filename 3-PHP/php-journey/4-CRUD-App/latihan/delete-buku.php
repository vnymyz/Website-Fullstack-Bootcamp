<?php
// ===================================================
// JAWABAN LATIHAN — DELETE (hapus buku)
// ===================================================
// Cuma nerima POST — sama alasannya kayak delete.php di folder induk:
// delete lewat GET/link bisa ke-trigger gak sengaja (misal link luar/gambar).
require_once "../config/db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index-buku.php");
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id > 0) {
    $query = "DELETE FROM buku WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: index-buku.php?msg=deleted");
exit;
