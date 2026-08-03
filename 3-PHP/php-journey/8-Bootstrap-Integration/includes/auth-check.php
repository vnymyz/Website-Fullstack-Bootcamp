<?php
// ===================================================
// AUTH CHECK — sama kayak sesi-sesi sebelumnya
// ===================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /php-journey/8-Bootstrap-Integration/login.php");
    exit;
}
