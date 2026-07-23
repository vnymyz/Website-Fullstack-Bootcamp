<?php
// ===================================================
// KONEKSI DATABASE
// ===================================================
// File ini isinya cuma buat "nyambungin" PHP ke MySQL.
// Di-include di setiap file yang butuh akses database, biar gak
// nulis ulang kode koneksi di tiap halaman (DRY - Don't Repeat Yourself).
//
// Database & table ini udah dibikin di Sesi 3 (3-MySQL-Basics/queries.sql).
// Kalau belum ada, jalanin dulu query di sana lewat phpMyAdmin.

$host = "localhost";
$user = "root";      // default XAMPP/Laragon, biasanya kosong password
$pass = "";
$dbname = "toko_belajar"; // koneksi php ke database di phpmyadmin

$koneksi = mysqli_connect($host, $user, $pass, $dbname);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
