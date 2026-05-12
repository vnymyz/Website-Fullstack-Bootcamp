<!DOCTYPE html>
<html lang="en" data-theme="cupcake">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty Shop</title>

    <!-- DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" />

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-pink-50">

    <!-- NAVBAR -->
    <div class="navbar bg-white shadow-md px-10">

        <div class="flex-1">
            <a class="text-2xl font-bold text-pink-500">
                Glow Beauty ✨
            </a>
        </div>

        <div class="flex-none gap-2">

            <button class="btn btn-ghost">
                Home
            </button>

            <button class="btn btn-primary">
                Products
            </button>

        </div>

    </div>

    <!-- TITLE -->
    <div class="text-center py-10">

        <h1 class="text-5xl font-bold">
            Beauty Products 💄
        </h1>

        <p class="text-gray-500 mt-4">
            Premium skincare and makeup collection
        </p>

    </div>

    <!-- PRODUCT GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 px-10 pb-10">

        <?php while($row = mysqli_fetch_assoc($products)): ?>

            <div class="card bg-white shadow-xl">

                <!-- IMAGE -->
                <figure>
                    <img src="<?= $row['image']; ?>"
                         class="h-72 w-full object-cover">
                </figure>

                <!-- CARD BODY -->
                <div class="card-body">

                    <!-- CATEGORY -->
                    <div class="badge badge-secondary">
                        <?= $row['category']; ?>
                    </div>

                    <!-- PRODUCT NAME -->
                    <h2 class="card-title">
                        <?= $row['name']; ?>
                    </h2>

                    <!-- DESCRIPTION -->
                    <p>
                        <?= $row['description']; ?>
                    </p>

                    <!-- PRICE -->
                    <p class="text-pink-500 font-bold text-xl">
                        Rp <?= number_format($row['price']); ?>
                    </p>

                    <!-- STOCK -->
                    <p class="text-gray-400 text-sm">
                        Stock: <?= $row['stock']; ?>
                    </p>

                    <!-- BUTTON -->
                    <div class="card-actions justify-end mt-4">

                        <button class="btn btn-primary">
                            Buy Now
                        </button>

                    </div>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</body>
</html>