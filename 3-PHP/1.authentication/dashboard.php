<?php
include 'auth/auth.php';
?>

<h1>Dashboard</h1>
<p>Welcome, <?= $_SESSION['user']['name']; ?></p>

<a href="auth/logout.php">Logout</a>