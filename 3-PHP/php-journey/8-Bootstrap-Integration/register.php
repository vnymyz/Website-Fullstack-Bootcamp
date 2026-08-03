<?php
// ===================================================
// REGISTER — logic sama kayak sesi 5/6/7, tampilan split-screen Bootstrap
// ===================================================
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Toko Buku</title>
    <!-- Bootstrap dari CDN -- gak perlu download/install apa-apa,
         browser yang narik file CSS/JS-nya langsung dari server jsDelivr. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row" style="min-height: 100vh;">
            <!-- Panel kiri -- gradient + branding, disembunyiin di layar kecil -->
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center text-white p-5"
                 style="background: linear-gradient(135deg, #4a2c1d 0%, #6b4028 100%);">
                <i class="bi bi-book-half" style="font-size: 8rem; opacity: 0.35;"></i>
                <h2 class="fw-bold mt-4">Gabung Sekarang</h2>
                <p class="text-white-50 text-center" style="max-width: 380px;">
                    Daftar gratis, simpen buku favorit kamu, dan pantau katalog terbaru kapan aja.
                </p>
            </div>

            <!-- Panel kanan -- form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 bg-light">
                <div style="width: 100%; max-width: 380px;">
                    <a href="index.php" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-4">
                        <i class="bi bi-arrow-left"></i> Kembali ke Home
                    </a>

                    <h1 class="h3 fw-bold mb-1">Buat Akun Baru</h1>
                    <p class="text-muted mb-4">Isi form di bawah buat daftar.</p>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Pilih username" value="<?= htmlspecialchars($username) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="konfirmasi" class="form-control" placeholder="Ulangi password">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-person-plus"></i> Daftar
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0 text-muted">
                        Sudah punya akun? <a href="login.php" class="fw-semibold text-decoration-none">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
