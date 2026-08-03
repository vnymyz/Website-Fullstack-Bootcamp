<?php
// ===================================================
// LOGIN — logic sama kayak sesi 6/7, tampilan split-screen Bootstrap
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="min-height: 100vh;">
            <!-- Panel kiri -- gradient + branding, disembunyiin di layar kecil (d-none d-lg-flex) -->
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center text-white p-5"
                 style="background: linear-gradient(135deg, #4a2c1d 0%, #6b4028 100%);">
                <i class="bi bi-book-half" style="font-size: 8rem; opacity: 0.35;"></i>
                <h2 class="fw-bold mt-4">Selamat Datang Lagi</h2>
                <p class="text-white-50 text-center" style="max-width: 380px;">
                    Login buat lanjut jelajahin katalog buku dan buka lagi daftar favorit kamu.
                </p>
            </div>

            <!-- Panel kanan -- form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 bg-light">
                <div style="width: 100%; max-width: 380px;">
                    <a href="index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-4">
                        <i class="bi bi-arrow-left"></i> Kembali ke Home
                    </a>

                    <h1 class="h3 fw-bold mb-1">Login</h1>
                    <p class="text-muted mb-4">Masuk ke akun Toko Buku kamu.</p>

                    <?php if ($msg === 'registered'): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> Register berhasil, silakan login.</div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Username kamu" value="<?= htmlspecialchars($username) ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Password kamu">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0 text-muted">
                        Belum punya akun? <a href="register.php" class="fw-semibold text-decoration-none">Register di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
