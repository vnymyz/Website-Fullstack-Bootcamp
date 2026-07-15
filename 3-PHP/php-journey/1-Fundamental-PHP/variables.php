<?php
// ===================================================
// 1. VARIABEL DASAR
// ===================================================

// Semua variabel PHP wajib pakai tanda $ di depan namanya
$nama = "Budi"; // string
$umur = 25; // integer
$tinggiBadan = 170.5; // float
$sudahMenikah = false; // boolean

echo "Nama: $nama<br>";
echo "Umur: $umur<br>";


// ===================================================
// 2. TIPE DATA & var_dump()
// ===================================================

// var_dump() nunjukin ISI variabel SEKALIGUS TIPE-nya, beda sama echo yang cuma nunjukin nilai
var_dump($nama);          // string(4) "Budi"
var_dump($umur);          // int(25)
var_dump($tinggiBadan);   // float(170.5)
var_dump($sudahMenikah);  // bool(false)
echo "<br>";

// PHP itu "loose-typed" (longgar), beda sama TypeScript.
// Di JS kamu kenal typeof, di PHP ada gettype() dan var_dump()
echo gettype($umur) . "<br>"; // "integer"


// ===================================================
// 3. TYPE JUGGLING (PHP otomatis ubah tipe kalau perlu)
// ===================================================

$angkaString = "5";
$angkaAsli = 3;

// PHP otomatis ubah "5" jadi angka 5 pas dioperasiin matematika
$hasil = $angkaString + $angkaAsli;
var_dump($hasil); // int(8) -- "5" otomatis dianggap angka

// Tapi hati-hati, ini beda sama JavaScript!
// Di JS: "5" + 3 hasilnya "53" (nyambung jadi string)
// Di PHP: "5" + 3 hasilnya 8 (dihitung sebagai angka)
// Makanya PENTING pakai === (bukan cuma ==) kalau mau bandingin yang beneran ketat

echo "<br>";


// ===================================================
// 4. STRING INTERPOLATION vs CONCATENATION
// ===================================================

$namaDepan = "Siti";
$namaBelakang = "Aminah";

// Cara 1: Interpolation -- variabel langsung ditulis di dalam tanda kutip DOBEL
echo "Nama lengkap: $namaDepan $namaBelakang<br>";

// Cara 2: Concatenation -- gabungin string pakai titik (.)
echo "Nama lengkap: " . $namaDepan . " " . $namaBelakang . "<br>";

// PENTING: interpolation CUMA jalan di tanda kutip DOBEL ("...")
// Kalau pakai kutip TUNGGAL ('...'), variabel TIDAK akan diproses, cuma dianggap teks biasa
echo 'Ini gak akan jalan: $namaDepan<br>'; // hasilnya: Ini gak akan jalan: $namaDepan

// Buat nulis nama properti/array di dalam string, biasanya butuh kurung kurawal {}
$user = ["nama" => "Andi"];
echo "Halo, {$user['nama']}!<br>";

// Catatan buat kamu:
// - Ganti nilai $nama, $umur di atas, tebak dulu tipe datanya sebelum lihat hasil var_dump()
// - Lanjut ke loops.php
