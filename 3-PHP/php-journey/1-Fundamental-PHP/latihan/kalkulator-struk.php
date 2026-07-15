<?php
// ===================================================
// LATIHAN 2: KALKULATOR STRUK BELANJA
// ===================================================
//
// Soal:
// Ada array asosiatif isinya daftar barang belanjaan (nama & harga).
// Tugas kamu:
// 1. Tampilin setiap barang dalam format: "Nama Barang - Rp harga"
//    (pakai number_format biar ada titik ribuan, contoh: Rp150.000)
// 2. Hitung TOTAL semua harga pakai loop (jangan pakai array_sum dulu,
//    biar kamu latihan looping manual)
// 3. Tampilin total belanja di paling bawah
//
// Bonus (opsional):
// - Kasih diskon 10% kalau total belanja di atas Rp500.000
// - Tampilin "Total setelah diskon: Rp..."

$belanjaan = [
    ["nama" => "Beras 5kg", "harga" => 65000],
    ["nama" => "Minyak Goreng 2L", "harga" => 32000],
    ["nama" => "Telur 1kg", "harga" => 28000],
    ["nama" => "Gula 1kg", "harga" => 15000],
    ["nama" => "Kopi Bubuk", "harga" => 12000],
];

echo "<h3>Struk Belanja</h3>";

$total = 0; // TODO: pakai variabel ini buat nampung total harga

// TODO: loop $belanjaan pakai foreach, tampilin nama & harga tiap barang,
// terus tambahin harganya ke $total

echo "<hr>";
echo "Total: Rp" . number_format($total) . "<br>";

// TODO (bonus): cek kalau $total > 500000, kasih diskon 10%, tampilin total setelah diskon
