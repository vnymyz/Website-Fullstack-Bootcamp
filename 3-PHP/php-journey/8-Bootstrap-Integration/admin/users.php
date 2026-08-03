<?php
// ===================================================
// ADMIN — KELOLA USERS, Bootstrap table + modal konfirmasi hapus + pagination
// ===================================================
require_once "../includes/admin-check.php";
require_once "../config/db.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ubah_role') {
    $targetId = (int) $_POST['id'];
    $roleBaru = $_POST['role'] === 'admin' ? 'admin' : 'user';

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

// --- Pagination: 10 baris per halaman ---
$perPage = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;

$totalUsers = (int) mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalPage = max(1, (int) ceil($totalUsers / $perPage));

$query = "SELECT id, username, role, created_at FROM users ORDER BY id ASC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "ii", $perPage, $offset);
mysqli_stmt_execute($stmt);
$users = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
$activePage = 'users';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Users - Toko Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php require "../includes/admin-sidebar.php"; ?>

        <main class="flex-grow-1 p-4">
            <div class="mb-4">
                <h1 class="h3 mb-0">Kelola Users</h1>
                <p class="text-muted small mb-0"><?= $totalUsers ?> user terdaftar</p>
            </div>

            <?php if ($msg === 'updated'): ?>
                <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> Role user berhasil diubah.</div>
            <?php elseif ($msg === 'deleted'): ?>
                <div class="alert alert-success d-flex align-items-center gap-2"><i class="bi bi-check-circle"></i> User berhasil dihapus.</div>
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

            <?php if (empty($users)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                    Belum ada user.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover bg-white align-middle shadow-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Daftar Sejak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $nomor = $offset + 1; ?>
                            <?php foreach ($users as $row): ?>
                            <tr>
                                <!-- Nomor urut tampilan, BUKAN id database -- id bisa bolong abis
                                     ada user yang dihapus. $offset nyambungin nomor antar halaman. -->
                                <td class="text-muted"><?= $nomor++ ?>.</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Avatar bulat isinya inisial username, biar gak polos teks doang -->
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; font-size:0.9rem;">
                                            <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                        </div>
                                        <div class="fw-semibold"><?= htmlspecialchars($row['username']) ?></div>
                                        <?php if ((int) $row['id'] === (int) $_SESSION['user_id']): ?>
                                            <span class="badge text-bg-light border">Kamu</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><span class="badge <?= $row['role'] === 'admin' ? 'text-bg-primary' : 'text-bg-secondary' ?>"><?= htmlspecialchars($row['role']) ?></span></td>
                                <td class="text-muted small"><?= htmlspecialchars($row['created_at']) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <form action="users.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="ubah_role">
                                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="user" <?= $row['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                                <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                            </select>
                                        </form>

                                        <!-- Tombol ini gak langsung ngirim form -- cuma buka modal Bootstrap
                                             (data-bs-toggle="modal"), form hapus beneran ada DI DALEM modal-nya -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#hapusModal<?= (int) $row['id'] ?>" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal konfirmasi hapus, 1 per baris user -->
                            <div class="modal fade" id="hapusModal<?= (int) $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger"></i> Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Yakin mau hapus user <strong><?= htmlspecialchars($row['username']) ?></strong>? Aksi ini gak bisa dibatalin.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="users.php" method="POST">
                                                <input type="hidden" name="action" value="hapus">
                                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPage > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= max(1, $page - 1) ?>">&laquo;</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $totalPage ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= min($totalPage, $page + 1) ?>">&raquo;</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
