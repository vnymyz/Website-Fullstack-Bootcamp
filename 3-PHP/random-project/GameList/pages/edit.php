<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Edit Game</title>
</head>
<body class="bg-gray-100 min-h-screen">

<?php include '../components/navbar.php'; ?>

<?php
include '../config/database.php';

$id    = (int) $_GET['id'];
$error = '';

// Get game data
$result = mysqli_query($conn, "SELECT * FROM games WHERE id = $id");
$game   = mysqli_fetch_assoc($result);

if (!$game) {
    echo "Game not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $price = (float) $_POST['price'];
    $image = $game['image']; // keep existing by default

    // File upload takes priority
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
    }

    if (!$error) {
        mysqli_query($conn, "UPDATE games SET title='$title', genre='$genre', price=$price, image='$image' WHERE id=$id");
        header('Location: index.php');
        exit;
    }
}
?>

<div class="max-w-xl mx-auto px-6 py-10">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Game</h1>

    <?php if ($error) : ?>
        <div class="bg-red-100 text-red-600 px-4 py-3 rounded-lg mb-4">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl shadow space-y-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($game['title']) ?>" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
            <input type="text" name="genre" value="<?= htmlspecialchars($game['genre']) ?>" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
            <input type="number" name="price" value="<?= $game['price'] ?>" step="0.01" min="0" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <!-- Image -->
        <div class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">Image</label>

            <!-- Current image -->
            <div>
                <p class="text-xs text-gray-500 mb-2">Current image</p>
                <img src="<?= htmlspecialchars($game['image']) ?>" alt="Current"
                    class="w-full h-40 object-cover rounded-lg"
                    onerror="this.src='https://placehold.co/400x300?text=No+Image'">
            </div>

            <!-- Upload new file -->
            <div class="border border-gray-300 rounded-lg px-4 py-3">
                <p class="text-xs text-gray-500 mb-2">Option 1 — Upload new image</p>
                <input type="file" name="image_file" accept="image/*"
                    onchange="previewImage(this)"
                    class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500 file:text-white file:cursor-pointer hover:file:bg-blue-600">
                <img id="preview" src="" alt="Preview" class="mt-3 w-full h-40 object-cover rounded-lg hidden">
            </div>

            <!-- URL -->
            <div class="border border-gray-300 rounded-lg px-4 py-3">
                <p class="text-xs text-gray-500 mb-2">Option 2 — Paste new image URL</p>
                <input type="text" name="image_url" placeholder="https://images.unsplash.com/..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <p class="text-xs text-gray-400">Leave both empty to keep current image.</p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg font-medium">
                Save Changes
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
