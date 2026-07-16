<?php
// ===================================================
// 1. ARRAY INDEXED (mirip Array biasa di JS)
// ===================================================
        // 0          1         2
$warna = ["Merah", "Kuning", "Hijau"];

echo "Warna pertama: " . $warna[0] . "<br>"; // index mulai dari 0
echo "Warna kedua: " . $warna[1] . "<br>";
echo "Jumlah warna: " . count($warna) . "<br>"; // count() mirip .length di JS

// Nambah elemen baru di akhir array
$warna[] = "Biru";
echo "Setelah ditambah: ";
print_r($warna); // print_r nunjukin isi array dalam bentuk gampang dibaca
echo "<br>";


// ===================================================
// 2. ARRAY ASOSIATIF (mirip Object di JS)
// ===================================================

$mahasiswa = [
    "nama" => "Dewi",
    "nim" => "2024001",
    "jurusan" => "Sistem Informasi"
];

echo "Nama: " . $mahasiswa["nama"] . "<br>";
echo "NIM: " . $mahasiswa["nim"] . "<br>";

// Ubah / tambah data kayak object JS: obj.key = value -> tapi PHP pakai $arr["key"] = value
$mahasiswa["semester"] = 4;
print_r($mahasiswa);
echo "<br>";


// ===================================================
// 3. ARRAY DUA DIMENSI (array isinya array, mirip array of objects di JS)
// ===================================================

$daftarProduk = [
    ["nama" => "Mouse", "harga" => 50000],
    ["nama" => "Keyboard", "harga" => 150000],
    ["nama" => "Monitor", "harga" => 1200000],
];

echo "<h3>Daftar Produk</h3>";
foreach ($daftarProduk as $produk) {
    echo $produk["nama"] . " - Rp" . number_format($produk["harga"]) . "<br>";
}


// ===================================================
// 4. FUNGSI ARRAY YANG SERING DIPAKAI
// ===================================================

$angka = [5, 3, 8, 1, 9];
// 1 3 5 8 9

sort($angka); // urutin kecil ke besar
echo "Setelah sort: ";
print_r($angka);
echo "<br>";

echo "Total: " . array_sum($angka) . "<br>"; // 5 + 3 + 8 + 1 + 9 = 26
echo "Terbesar: " . max($angka) . "<br>"; // 9
echo "Terkecil: " . min($angka) . "<br>"; // 1

$genap = array_filter($angka, function ($n) {
    return $n % 2 == 0;
});
echo "Angka genap saja: ";
print_r($genap);

// Catatan buat kamu:
// - Coba bikin array asosiatif buat data diri kamu sendiri (nama, umur, hobi), tampilin pakai foreach
// - Lanjut ke functions.php
