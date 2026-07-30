<?php
// Sama persis kayak config/db.php di sesi sebelumnya — koneksi ke database yang sama.
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "toko_belajar";

$koneksi = mysqli_connect($host, $user, $pass, $dbname);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
