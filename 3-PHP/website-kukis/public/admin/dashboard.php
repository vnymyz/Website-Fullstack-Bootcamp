<?php
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: ../login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<?php 
require_once '../../config/database.php';

// ambil data
$query = "SELECT * FROM product ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <?php include '../../components/sidebar.php'; ?>

  <!-- CONTENT -->
  <main class="flex-1 p-10 bg-gray-100">

    <h1 class="text-2xl font-bold mb-6">Data Produk</h1>

    <div class="bg-white rounded-xl shadow p-6">

      <table class="w-full text-left">

        <thead>
          <tr class="border-b">
            <th class="py-2">No</th>
            <th>Nama</th>
            <th>Gambar</th>
            <th>Harga</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>

        <?php $no = 1; while($row = mysqli_fetch_assoc($result)): ?>
          <tr class="border-b">
            <td class="py-2"><?= $no++; ?></td>
            <td><?= $row['name']; ?></td>
            <td><img src="<?= $row['image']; ?>" alt="<?= $row['name']; ?>" class="w-16 h-16 object-cover"></td>
            <td>Rp <?= number_format($row['price']); ?></td>
            <td>
              <!-- EDIT BUTTON -->
                <a href="form.php?id=<?= $row['id']; ?>" 
                   class="text-blue-500">
                   Edit
                </a>
              <!-- DELETE BUTTON -->
                <a href="delete.php?id=<?= $row['id']; ?>" 
                     onclick="return confirm('Yakin mau hapus?')"
                     class="text-red-500 ml-2">
                     Delete
                </a>
            </td>
          </tr>
        <?php endwhile; ?>

        </tbody>

      </table>

    </div>

  </main>

</div>
    
</body>
</html>