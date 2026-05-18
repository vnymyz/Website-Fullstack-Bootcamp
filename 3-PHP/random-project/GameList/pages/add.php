<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Add Game</title>
</head>
<body class="bg-gray-100 min-h-screen">

<?php include '../components/navbar.php'; ?>

<?php
include '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $price = (float) $_POST['price'];
    $image = '';

    // File upload takes priority over URL
    if (!empty($_FILES['image_file']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext     = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = 'Only JPG, PNG, WEBP images allowed.';
        } else {
            $filename = uniqid('game_') . '.' . $ext;
            $dest     = '../uploads/' . $filename;

            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
                $image = '../uploads/' . $filename;
            } else {
                $error = 'Failed to upload image.';
            }
        }
    } elseif (!empty($_POST['image_url'])) {
        $image = mysqli_real_escape_string($conn, $_POST['image_url']);
    } else {
        $error = 'Provide an image file or URL.';
    }

    if (!$error) {
        if ($title && $genre) {
            mysqli_query($conn, "INSERT INTO games (title, genre, price, image, created_at) VALUES ('$title', '$genre', $price, '$image', NOW())");
            header('Location: index.php');
            exit;
        } else {
            $error = 'Title and genre are required.';
        }
    }
}
?>

<div class="max-w-xl mx-auto px-6 py-10">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Add New Game</h1>

    <?php if ($error) : ?>
        <div class="bg-red-100 text-red-600 px-4 py-3 rounded-lg mb-4">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow space-y-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" placeholder="e.g. Elden Ring" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
            <input type="text" name="genre" value="<?= htmlspecialchars($_POST['genre'] ?? '') ?>" placeholder="e.g. RPG" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
            <input type="number" name="price" value="<?= $_POST['price'] ?? '0' ?>" step="0.01" min="0" placeholder="0 for free" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <!-- Image Upload -->
        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Image</label>

            <!-- Upload file -->
            <div class="border border-gray-300 rounded-lg px-4 py-3">
                <p class="text-xs text-gray-500 mb-2">Option 1 — Upload from your computer</p>
                <input type="file" name="image_file" accept="image/*" id="image_file"
                    onchange="previewImage(this)"
                    class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500 file:text-white file:cursor-pointer hover:file:bg-blue-600">
                <img id="preview" src="" alt="Preview" class="mt-3 w-full h-40 object-cover rounded-lg hidden">
            </div>

            <!-- URL fallback -->
            <div class="border border-gray-300 rounded-lg px-4 py-3">
                <p class="text-xs text-gray-500 mb-2">Option 2 — Paste image URL</p>
                <input type="text" name="image_url" value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>" placeholder="https://images.unsplash.com/..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <p class="text-xs text-gray-400">If both are provided, uploaded file is used.</p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg font-medium">
                Add Game
            </button>
            <a href="index.php"
                class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg font-medium">
                Cancel
            </a>
        </div>

    </form>

</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.classList.remove('hidden');
    }
}
</script>

<?php include '../components/footer.php'; ?>
</body>
</html>
