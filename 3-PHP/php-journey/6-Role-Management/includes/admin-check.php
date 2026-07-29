<?php
// ===================================================
// ADMIN CHECK — INI LAPISAN KEAMANAN YANG SEBENARNYA
// ===================================================
// auth-check.php doang cuma mastiin "udah login". File ini mastiin
// "yang login ini emang admin". WAJIB di-include di SETIAP halaman/aksi
// yang cuma boleh diakses admin (bukan cukup nyembunyiin tombolnya di HTML/CSS).
//
// Include auth-check.php DULU sebelum file ini, biar $_SESSION udah
// pasti ke-set (session_start() + cek login).
require_once __DIR__ . "/auth-check.php";

if ($_SESSION['role'] !== 'admin') {
    // Bukan admin -> tendang balik, jangan kasih akses sama sekali.
    // (403 = Forbidden, beda sama 401 Unauthorized yang berarti belum login)
    http_response_code(403);
    die("403 Forbidden — kamu bukan admin, gak boleh akses halaman ini.");
}
