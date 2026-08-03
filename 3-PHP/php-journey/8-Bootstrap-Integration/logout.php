<?php
// ===================================================
// LOGOUT — sama kayak sesi-sesi sebelumnya
// ===================================================
session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit;
