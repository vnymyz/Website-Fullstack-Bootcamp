<?php
include '../../config/database.php';

$id = $_GET['id'];

$query = "DELETE FROM product WHERE id = $id";
mysqli_query($conn, $query);

header("Location: dashboard.php");
?>

<a href="delete.php?id=<?= $row['id']; ?>" 
   onclick="return confirm('Yakin mau hapus?')"
   class="text-red-500 ml-2">
   Delete
</a>