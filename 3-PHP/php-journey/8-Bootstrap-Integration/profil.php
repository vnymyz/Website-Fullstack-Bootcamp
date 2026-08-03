<?php
// ===================================================
// EDIT PROFILE — ganti username & password
// ===================================================
require_once "includes/auth-check.php";
require_once "config/db.php";

$errorsUsername = [];
$errorsPassword = [];
$msgUsername = null;
$msgPassword = null;

$usernameBaru = $_SESSION['username'];

// --- Form 1: ganti username ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'username') {
    $usernameBaru = trim($_POST['username_baru'] ?? '');

    if (empty($usernameBaru)) {
        $errorsUsername[] = "Username baru wajib diisi.";
    }

    if (empty($errorsUsername)) {
        // Cek username baru gak dipakai user LAIN (boleh sama kayak punya sendiri)
        $query = "SELECT id FROM users WHERE username = ? AND id != ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $usernameBaru, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_fetch_assoc($result)) {
            $errorsUsername[] = "Username sudah dipakai user lain, pilih yang lain.";
        }
    }

    if (empty($errorsUsername)) {
        $query = "UPDATE users SET username = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $usernameBaru, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);

        // Session gak otomatis nyambung ke database -- WAJIB update manual
        // di sini, kalau enggak, navbar/sidebar masih nampilin username lama
        // sampai logout-login ulang.
        $_SESSION['username'] = $usernameBaru;

        header("Location: profil.php?msg=username-updated");
        exit;
    }
}

// --- Form 2: ganti password ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'password') {
    $passwordLama = $_POST['password_lama'] ?? '';
    $passwordBaru = $_POST['password_baru'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    // Ambil hash password yang ASLI dari database dulu, buat dicocokin
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!password_verify($passwordLama, $user['password'])) {
        $errorsPassword[] = "Password lama salah.";
    }
    if (strlen($passwordBaru) < 6) {
        $errorsPassword[] = "Password baru minimal 6 karakter.";
    }
    if ($passwordBaru !== $konfirmasi) {
        $errorsPassword[] = "Konfirmasi password baru gak cocok.";
    }

    if (empty($errorsPassword)) {
        $hashBaru = password_hash($passwordBaru, PASSWORD_DEFAULT);

        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $hashBaru, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);

        header("Location: profil.php?msg=password-updated");
        exit;
    }
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
$activePage = 'profil';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php require "includes/user-sidebar.php"; ?>

        <main class="flex-grow-1 p-4" style="max-width: 700px;">
            <h1 class="h3 mb-1"><i class="bi bi-person-gear"></i> Edit Profile</h1>
            <p class="text-muted mb-4">Ganti username atau password akun kamu.</p>

            <?php if ($msg === 'username-updated'): ?>
                <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> Username berhasil diubah.</div>
            <?php elseif ($msg === 'password-updated'): ?>
                <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> Password berhasil diubah.</div>
            <?php endif; ?>

            <!-- Card 1: Ganti Username -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3"><i class="bi bi-person"></i> Username</h2>

                    <?php if (!empty($errorsUsername)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errorsUsername as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="profil.php">
                        <input type="hidden" name="form" value="username">
                        <div class="mb-3">
                            <label class="form-label">Username Baru</label>
                            <input type="text" name="username_baru" class="form-control" value="<?= htmlspecialchars($usernameBaru) ?>">
                        </div>
                        <button type="submit" class="btn btn-dark">Simpan Username</button>
                    </form>
                </div>
            </div>

            <!-- Card 2: Ganti Password -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3"><i class="bi bi-lock"></i> Password</h2>

                    <?php if (!empty($errorsPassword)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errorsPassword as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="profil.php">
                        <input type="hidden" name="form" value="password">
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="password_lama" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-dark">Simpan Password</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
