<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Tambah Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php include '../../config/database.php'; ?>

<!-- tambah data -->
<?php

if (isset($_POST['name'])) {

  $name = $_POST['name'];
  $price = $_POST['price'];
  $image = $_POST['image'];

  $query = "INSERT INTO product (name, price, image)
            VALUES ('$name', '$price', '$image')";

  mysqli_query($conn, $query);

  header("Location: dashboard.php");
}
?>

<div class="flex min-h-screen">

  <?php include '../../components/sidebar.php'; ?>

  <main class="flex-1 p-10 bg-gray-100">

    <h1 class="text-2xl font-bold mb-6">Tambah Produk</h1>

    <div class="bg-white p-6 rounded-xl shadow max-w-lg">

      <form method="POST">

        <input type="text" name="name" placeholder="Nama Produk"
          class="border p-2 w-full mb-4 rounded">

        <input type="number" name="price" placeholder="Harga"
          class="border p-2 w-full mb-4 rounded">

        <input type="text" name="image" placeholder="Link Gambar"
          class="border p-2 w-full mb-4 rounded">

        <button class="bg-purple-600 text-white px-4 py-2 rounded">
          Simpan
        </button>

      </form>

    </div>

  </main>

</div>

<?php include '../../components/footer.php'; ?>
</body>
</html>