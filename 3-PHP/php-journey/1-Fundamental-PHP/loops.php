<?php
// ===================================================
// 1. FOR LOOP
// ===================================================

echo "<h3>For Loop</h3>";
for ($i = 1; $i <= 5; $i++) {
    echo "<br>Perulangan ke-$i<br>";
}


// ===================================================
// 2. WHILE LOOP
// ===================================================

echo "<h3>While Loop</h3>";
$angka = 1;
while ($angka <= 5) {
    echo "<br>Angka: $angka<br>";
    $angka++; // JANGAN lupa naikin nilainya, kalau lupa jadi infinite loop!
}


// ===================================================
// 3. DO-WHILE LOOP (dijalanin minimal 1x, baru dicek kondisinya)
// ===================================================

echo "<h3>Do-While Loop</h3>";
$hitung = 1;
do {
    echo "Ini jalan minimal sekali: $hitung<br>";
    $hitung++;
} while ($hitung <= 3);


// ===================================================
// 4. FOREACH (khusus buat looping ARRAY)
// ===================================================

echo "<h3>Foreach - Array Sederhana</h3>";
$buah = ["Apel", "Jeruk", "Mangga"];

// Ini mirip banget sama "for...of" di JavaScript:
// for (const item of buah) { console.log(item) }
foreach ($buah as $item) {
    echo "Buah: $item<br>";
}

echo "<h3>Foreach - Array Asosiatif (key => value)</h3>";
$biodata = [
    "nama" => "Rina",
    "umur" => 22,
    "kota" => "Bandung"
];

// Ini mirip Object.entries() di JS: for (const [key, value] of Object.entries(obj))
foreach ($biodata as $key => $value) {
    echo "$key: $value<br>";
}


// ===================================================
// 5. BREAK & CONTINUE
// ===================================================

echo "<h3>Break & Continue</h3>";
for ($i = 1; $i <= 10; $i++) {
    if ($i == 6) {
        break; // langsung berhenti total dari loop begitu i == 6
    }
    if ($i % 2 == 0) {
        continue; // lewatin angka genap, lanjut ke perulangan berikutnya
    }
    echo "Angka ganjil sebelum 6: $i<br>";
}

// Catatan buat kamu:
// - Coba ganti array $buah isinya 5 nama temen kamu, tampilin pakai foreach
// - Lanjut ke arrays.php
