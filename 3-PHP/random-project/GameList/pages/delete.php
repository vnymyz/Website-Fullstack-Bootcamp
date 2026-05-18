<?php
include '../config/database.php';

$id = (int) $_GET['id'];

mysqli_query($conn, "DELETE FROM games WHERE id = $id");

header('Location: index.php');
exit;
