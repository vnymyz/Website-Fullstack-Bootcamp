<?php
// ===================================================
// AUTH CHECK — sama kayak Sesi 5, cek udah login apa belum
// ===================================================
session_start();

// Pakai path absolut dari root web (bukan relative "login.php" atau
// "../login.php") karena file ini di-include dari 2 lokasi beda:
// langsung dari root folder (dashboard.php) dan dari admin/ (admin/users.php).
// Kalau pakai path relative, salah satu lokasi bakal nyasar redirect-nya.
if (!isset($_SESSION['user_id'])) {
    header("Location: /php-journey/6-Role-Management/login.php");
    exit;
}
