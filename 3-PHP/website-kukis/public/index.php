<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Cookies</title>
    <!-- tailwind config -->
     <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<!-- navbar component -->
<?php include '../components/navbar.php'; ?>

<!-- HERO SECTION -->
<section class="flex items-center justify-between px-16 py-20 bg-gradient-to-r from-purple-50 to-pink-50">

  <!-- TEXT -->
  <div class="max-w-xl">
    <h1 class="text-5xl font-bold text-gray-800 leading-tight">
      Fresh & Delicious <br>
      <span class="text-purple-600">Cookies Everyday 🍪</span>
    </h1>

    <p class="mt-6 text-gray-600">
      Nikmati cookies homemade dengan rasa premium,
      dibuat dari bahan terbaik dan selalu fresh setiap hari.
    </p>

    <div class="mt-8 space-x-4">
      <a href="product.php" class="bg-purple-600 text-white px-6 py-3 rounded-lg">
        Shop Now
      </a>

      <a href="about.php" class="border border-gray-300 px-6 py-3 rounded-lg">
        Learn More
      </a>
    </div>
  </div>

  <!-- IMAGE -->
  <div>
    <img src="https://images.unsplash.com/photo-1606313564200-e75d5e30476c"
         class="w-[500px] rounded-2xl shadow-lg">
  </div>

</section>

<!-- CARD SECTION BEST COOKIES -->
<section class="px-16 py-16">

  <h2 class="text-3xl font-bold text-center mb-10">
    Our Best Cookies 🍪
  </h2>

  <div class="grid grid-cols-3 gap-8">

    <!-- CARD 1 -->
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4">
      <img src="https://images.unsplash.com/photo-1583743089695-4b816a340f82?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
           class="rounded-lg mb-4">

      <h3 class="text-lg font-semibold">Chocolate Chip</h3>
      <p class="text-gray-500">Rp 25.000</p>

      <button class="mt-4 w-full bg-purple-600 text-white py-2 rounded-lg">
        Buy Now
      </button>
    </div>

    <!-- CARD 2 -->
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4">
      <img src="https://images.unsplash.com/photo-1567945520067-d37a381fd5e5?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
           class="rounded-lg mb-4">

      <h3 class="text-lg font-semibold">Matcha Cookie</h3>
      <p class="text-gray-500">Rp 30.000</p>

      <button class="mt-4 w-full bg-purple-600 text-white py-2 rounded-lg">
        Buy Now
      </button>
    </div>

    <!-- CARD 3 -->
    <div class="bg-white rounded-xl shadow hover:shadow-lg transition p-4">
      <img src="https://images.unsplash.com/photo-1586788680434-30d324b2d46f?q=80&w=951&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
           class="rounded-lg mb-4">

      <h3 class="text-lg font-semibold">Red Velvet</h3>
      <p class="text-gray-500">Rp 28.000</p>

      <button class="mt-4 w-full bg-purple-600 text-white py-2 rounded-lg">
        Buy Now
      </button>
    </div>

  </div>

</section>

<!-- footer component -->
<?php include '../components/footer.php'; ?>
    
</body>
</html>