<?php
// ===================================================
// LOGOUT — sama kayak Sesi 5/6
// ===================================================
session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit;
