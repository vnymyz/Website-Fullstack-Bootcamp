<?php
// ===================================================
// LATIHAN 2: KALKULATOR STRUK BELANJA — JAWABAN
// ===================================================
//
// Soal asli ada di kalkulator-struk.php. File ini isi contoh solusinya.
// Cek punya kamu dulu sebelum liat ini — nyocokin logic > nyalin.

$belanjaan = [
    ["nama" => "Beras 5kg", "harga" => 65000],
    ["nama" => "Minyak Goreng 2L", "harga" => 32000],
    ["nama" => "Telur 1kg", "harga" => 28000],
    ["nama" => "Gula 1kg", "harga" => 15000],
    ["nama" => "Kopi Bubuk", "harga" => 12000],
    ["nama" => "PS4", "harga" => 1000000],
];

echo "<h3>Struk Belanja</h3>";

$total = 0;

foreach ($belanjaan as $barang) {
    echo $barang["nama"] . " - Rp" . number_format($barang["harga"]) . "<br>";
    $total += $barang["harga"];
}

echo "<hr>";
echo "Total: Rp" . number_format($total) . "<br>";

// Bonus: diskon 10% kalau total > 500000
if ($total > 500000) {
    $totalSetelahDiskon = $total * 0.9;
    echo "Total setelah diskon: Rp" . number_format($totalSetelahDiskon) . "<br>";
}
