<?php
// ===================================================
// LOGOUT — HAPUS SESSION
// ===================================================
session_start();

// session_unset() -> kosongin semua data $_SESSION
// session_destroy() -> hapus session-nya sendiri di server
session_unset();
session_destroy();

header("Location: login.php");
exit;
