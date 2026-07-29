<?php
// ===================================================
// LOGOUT — sama kayak Sesi 5
// ===================================================
session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit;
