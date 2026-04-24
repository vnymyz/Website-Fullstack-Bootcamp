<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Tambah Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<!-- PHP LOGIC -->
<?php
include '../../config/database.php';

$id = $_GET['id'] ?? null;

// ambil data kalau edit
if ($id) {
  $query = "SELECT * FROM product WHERE id = $id";
  $result = mysqli_query($conn, $query);
  $data = mysqli_fetch_assoc($result);
}

// handle submit (CREATE + UPDATE)
if (isset($_POST['name'])) {

  $id = $_POST['id'];
  $name = $_POST['name'];
  $price = $_POST['price'];
  $image = $_POST['image'];

  if ($id) {
    // UPDATE
    $query = "UPDATE product 
              SET name='$name', price='$price', image='$image'
              WHERE id=$id";
  } else {
    // CREATE
    $query = "INSERT INTO product (name, price, image)
              VALUES ('$name', '$price', '$image')";
  }

  mysqli_query($conn, $query);
  header("Location: dashboard.php");
}
?>

<div class="flex min-h-screen">

  <?php include '../../components/sidebar.php'; ?>

  <main class="flex-1 p-10 bg-gray-100">

    <!-- title -->
    <h1 class="text-2xl font-bold mb-6">
      <?= isset($data) ? 'Edit Produk' : 'Tambah Produk' ?>
    </h1>

    <div class="bg-white p-6 rounded-xl shadow max-w-lg">

    <form method="POST">

  <!-- hidden id -->
  <input type="hidden" name="id" value="<?= $data['id'] ?? '' ?>">

  <input type="text" name="name"
    value="<?= $data['name'] ?? '' ?>"
    placeholder="Nama Produk"
    class="border p-2 w-full mb-4 rounded">

  <input type="number" name="price"
    value="<?= $data['price'] ?? '' ?>"
    placeholder="Harga"
    class="border p-2 w-full mb-4 rounded">

  <input type="text" name="image"
    value="<?= $data['image'] ?? '' ?>"
    placeholder="Link Gambar"
    class="border p-2 w-full mb-4 rounded">

  <button class="bg-purple-600 text-white px-4 py-2 rounded">
    <?= isset($data) ? 'Update' : 'Simpan' ?>
  </button>

</form>

    </div>

  </main>

</div>

<?php include '../../components/footer.php'; ?>
</body>
</html>