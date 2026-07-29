<?php
// ===================================================
// ADMIN PANEL — KELOLA USERS (ADMIN ONLY)
// ===================================================
// admin-check.php di dalemnya udah include auth-check.php juga,
// jadi 1 baris ini nge-cover 2 lapis: "udah login?" + "role-nya admin?"
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$errors = [];

// --- Aksi: ganti role ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ubah_role') {
    $targetId = (int) $_POST['id'];
    $roleBaru = $_POST['role'] === 'admin' ? 'admin' : 'user';

    // Cegah admin nge-demote diri sendiri jadi user (biar gak ke-lockout
    // dari admin panel sendiri kalau cuma ada 1 admin)
    if ($targetId === (int) $_SESSION['user_id'] && $roleBaru !== 'admin') {
        $errors[] = "Gak bisa ubah role akun sendiri jadi user.";
    } else {
        $query = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $roleBaru, $targetId);
        mysqli_stmt_execute($stmt);

        header("Location: users.php?msg=updated");
        exit;
    }
}

// --- Aksi: hapus user ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus') {
    $targetId = (int) $_POST['id'];

    if ($targetId === (int) $_SESSION['user_id']) {
        $errors[] = "Gak bisa hapus akun sendiri.";
    } else {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "i", $targetId);
        mysqli_stmt_execute($stmt);

        header("Location: users.php?msg=deleted");
        exit;
    }
}

$query = "SELECT id, username, role, created_at FROM users ORDER BY id ASC";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
$activePage = 'users';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Users - Toko Buku</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="content">
            <h1>Kelola Users</h1>

            <?php if ($msg === 'updated'): ?>
                <div class="alert">Role user berhasil diubah.</div>
            <?php elseif ($msg === 'deleted'): ?>
                <div class="alert">User berhasil dihapus.</div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <ul class="alert-error">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Daftar Sejak</th>
                    <th>Aksi</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><span class="role-badge role-<?= htmlspecialchars($row['role']) ?>"><?= htmlspecialchars($row['role']) ?></span></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <div class="table-actions">
                            <form action="users.php" method="POST">
                                <input type="hidden" name="action" value="ubah_role">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="user" <?= $row['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                    <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                </select>
                            </form>

                            <form action="users.php" method="POST" onsubmit="return confirm('Yakin hapus user ini?');">
                                <input type="hidden" name="action" value="hapus">
                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                <button type="submit" class="btn btn-hapus">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </main>
    </div>
</body>
</html>
