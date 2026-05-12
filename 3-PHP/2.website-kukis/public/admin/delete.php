<?php
require_once '../../config/database.php';

$id = $_GET['id'];

$query = "DELETE FROM product WHERE id = $id";
mysqli_query($conn, $query);

header("Location: dashboard.php");
?>