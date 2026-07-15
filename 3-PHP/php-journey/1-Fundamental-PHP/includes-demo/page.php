<?php
// ===================================================
// INCLUDE / REQUIRE -- cara "pecah" halaman jadi potongan kecil
// yang bisa dipakai ulang, biar gak copy-paste HTML yang sama terus
// ===================================================

// include: kalau file gak ketemu, PHP cuma kasih WARNING, halaman tetap jalan
include "header.php";

echo "<div style='padding:20px;'>";
echo "<h3>Ini konten utama halaman</h3>";
echo "<p>Header di atas dan footer di bawah itu file terpisah, di-include ke sini.</p>";
echo "</div>";

// require: kalau file gak ketemu, PHP kasih FATAL ERROR, halaman LANGSUNG BERHENTI
// dipakai kalau file itu WAJIB ada, misal config koneksi database
require "footer.php";

// Catatan buat kamu:
// - include_once / require_once = sama kayak include/require, tapi kalau file itu
//   sudah pernah di-include sebelumnya di halaman yang sama, gak akan diulang lagi
//   (berguna biar gak dobel-load, misal koneksi database di banyak file)
//
// - Coba buka file ini lewat browser (bukan header.php / footer.php langsung),
//   karena page.php ini yang jadi "halaman utuh"-nya
