<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Game Store</title>
</head>
<body class="bg-gray-100 min-h-screen">

<?php include '../components/navbar.php'; ?>

<?php
include '../config/database.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

if ($search) {
    $result = mysqli_query($conn, "SELECT * FROM games WHERE title LIKE '%$search%' ORDER BY created_at DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM games ORDER BY created_at DESC");
}
?>

<div class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Game Store</h1>
        <p class="text-gray-500 mt-1">Browse our collection of games</p>
    </div>

    <!-- Search Bar -->
    <form method="GET" action="" class="mb-8">
        <div class="flex gap-2 max-w-md">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Search games..."
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Search
            </button>
            <?php if ($search) : ?>
            <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                Clear
            </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Game Cards -->
    <?php if (mysqli_num_rows($result) === 0) : ?>
        <p class="text-gray-500">No games found.</p>
    <?php else : ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php while ($game = mysqli_fetch_assoc($result)) : ?>
        <div class="bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden flex flex-col">
            <img
                src="<?= $game['image'] ?>"
                alt="<?= htmlspecialchars($game['title']) ?>"
                class="w-full h-48 object-cover"
                onerror="this.src='https://placehold.co/400x300?text=No+Image'"
            >
            <div class="p-4 flex flex-col flex-1">
                <span class="text-xs bg-blue-100 text-blue-600 font-medium px-2 py-1 rounded-full w-fit">
                    <?= htmlspecialchars($game['genre']) ?>
                </span>
                <h2 class="text-gray-800 font-semibold mt-2 text-sm leading-snug flex-1">
                    <?= htmlspecialchars($game['title']) ?>
                </h2>
                <p class="text-green-600 font-bold mt-2 mb-4">
                    <?= $game['price'] == 0 ? 'Free' : '$' . number_format($game['price'], 2) ?>
                </p>
                <!-- Edit & Delete Buttons -->
                <div class="flex gap-2">
                    <a href="edit.php?id=<?= $game['id'] ?>"
                       class="flex-1 text-center bg-yellow-400 hover:bg-yellow-500 text-white text-sm py-1.5 rounded-lg">
                        Edit
                    </a>
                    <a href="delete.php?id=<?= $game['id'] ?>"
                       onclick="return confirm('Delete <?= htmlspecialchars($game['title']) ?>?')"
                       class="flex-1 text-center bg-red-500 hover:bg-red-600 text-white text-sm py-1.5 rounded-lg">
                        Delete
                    </a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>

</div>

<?php include '../components/footer.php'; ?>
</body>
</html>
