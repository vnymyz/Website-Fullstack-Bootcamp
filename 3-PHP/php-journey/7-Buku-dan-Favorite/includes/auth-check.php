<?php
// ===================================================
// AUTH CHECK — sama kayak Sesi 5/6, cek udah login apa belum
// ===================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /php-journey/7-Buku-dan-Favorite/login.php");
    exit;
}
