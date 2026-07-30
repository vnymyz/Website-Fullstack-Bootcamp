<?php
// ===================================================
// LOGIN — sama kayak Sesi 6
// ===================================================
session_start();

require_once "config/db.php";

$errors = [];
$username = "";
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = "Username dan password wajib diisi.";
    }

    if (empty($errors)) {
        $query = "SELECT id, username, password, role FROM users WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Toko Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h1>Login</h1>

        <?php if ($msg === 'registered'): ?>
            <div class="alert">Register berhasil, silakan login.</div>
        <?php elseif ($msg === 'harus-login'): ?>
            <div class="alert-error">Login dulu buat nge-like buku.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul class="alert-error">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>">

            <label>Password:</label>
            <input type="password" name="password">

            <button type="submit">Login</button>
        </form>

        <p>Belum punya akun? <a href="register.php">Register di sini</a></p>
        <p><a href="index.php">&larr; Kembali ke Home</a></p>
    </div>
</body>
</html>
