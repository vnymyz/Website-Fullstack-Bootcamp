<?php
// ===================================================
// REGISTER — sama kayak Sesi 5
// ===================================================
// User baru selalu masuk sebagai role 'user' (default kolom di database).
// Gak ada opsi "daftar sebagai admin" di form — admin cuma bisa dibikin
// manual lewat database/oleh admin lain, gak boleh user nge-assign
// role diri sendiri sendiri lewat form register.
require_once "config/db.php";

$errors = [];
$username = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    if (empty($username)) {
        $errors[] = "Username wajib diisi.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }
    if ($password !== $konfirmasi) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }

    if (empty($errors)) {
        $query = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_fetch_assoc($result)) {
            $errors[] = "Username sudah dipakai, pilih yang lain.";
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Gak nulis kolom "role" di sini -> otomatis ke-isi default 'user'
        // dari struktur table (lihat setup.sql).
        $query = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPassword);
        mysqli_stmt_execute($stmt);

        header("Location: login.php?msg=registered");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Toko Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h1>Register</h1>

        <?php if (!empty($errors)): ?>
            <ul class="alert-error">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>">

            <label>Password:</label>
            <input type="password" name="password">

            <label>Konfirmasi Password:</label>
            <input type="password" name="konfirmasi">

            <button type="submit">Daftar</button>
        </form>

        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        <p><a href="index.php">&larr; Kembali ke Home</a></p>
    </div>
</body>
</html>
