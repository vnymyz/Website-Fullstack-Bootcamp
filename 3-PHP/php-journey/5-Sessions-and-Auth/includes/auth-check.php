<?php
// ===================================================
// AUTH CHECK — WAJIB DI-INCLUDE DI SETIAP HALAMAN PROTECTED
// ===================================================
// Cukup taro require_once file ini di paling atas halaman yang mau
// dilindungi (sebelum ada output HTML apapun). Kalau belum login,
// langsung ditendang ke login.php.
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
