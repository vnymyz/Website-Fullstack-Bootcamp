<?php
// ===================================================
// ADMIN CHECK — sama kayak Sesi 6
// ===================================================
require_once __DIR__ . "/auth-check.php";

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die("403 Forbidden — kamu bukan admin, gak boleh akses halaman ini.");
}
