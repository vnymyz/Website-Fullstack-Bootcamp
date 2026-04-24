<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Produk</title>
    <!-- tailwind config -->
     <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<?php include '../components/navbar.php'; ?>

<?php include '../config/database.php'; ?>

<?php
$query = "SELECT * FROM product ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<section class="px-10 py-16">

  <h1 class="text-4xl font-bold text-center mb-10">
    Our Cookies 🍪
  </h1>

  <!-- tampilin data cookies -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">

<?php while($row = mysqli_fetch_assoc($result)): ?>

  <div class="bg-white rounded-xl shadow p-4">
    <img src="<?= $row['image']; ?>"
         class="rounded-lg mb-4 w-full h-48 object-cover">

    <h3 class="font-semibold"><?= $row['name']; ?></h3>
    <p class="text-gray-500">Rp <?= number_format($row['price']); ?></p>

    <button class="mt-3 w-full bg-purple-600 text-white py-2 rounded-lg">
      Buy Now
    </button>
  </div>

<?php endwhile; ?>

</div>

</section>

<?php include '../components/footer.php'; ?>
</body>
</html>
