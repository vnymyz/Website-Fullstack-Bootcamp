<?php
// ===================================================
// 1. FUNGSI DASAR
// ===================================================

function sapa() {
    echo "Halo dari fungsi!<br>";
}

sapa(); // panggil fungsinya


// ===================================================
// 2. FUNGSI DENGAN PARAMETER
// ===================================================

function sapaNama($nama) {
    echo "Halo, $nama!<br>";
}

sapaNama("Rangga");
sapaNama("Cinta");


// ===================================================
// 3. PARAMETER DENGAN NILAI DEFAULT
// ===================================================

function sapaFormal($nama, $sapaan = "Kak") {
    echo "Halo, $sapaan $nama!<br>";
}

sapaFormal("Andi");             // pakai default "Kak" karena $sapaan gak diisi
sapaFormal("Sari", "Bu");       // override default


// ===================================================
// 4. RETURN VALUE
// ===================================================

function tambah($a, $b) {
    return $a + $b;
}

$hasilTambah = tambah(5, 3);
echo "Hasil tambah: $hasilTambah<br>";

// Fungsi bisa dipakai langsung di dalam echo/string
echo "5 + 3 = " . tambah(5, 3) . "<br>";


// ===================================================
// 5. TYPE HINT (OPSIONAL, TAPI BAGUS BUAT KEBIASAAN)
// ===================================================

// Kasih tahu PHP tipe data yang diharapkan masuk & keluar dari fungsi
function kali(int $a, int $b): int {
    return $a * $b;
}

echo "6 x 7 = " . kali(6, 7) . "<br>";

// Type hint buat array
function totalBelanja(array $harga): int {
    return array_sum($harga);
}

echo "Total belanja: " . totalBelanja([10000, 20000, 5000]) . "<br>";


// ===================================================
// 6. FUNGSI DENGAN BANYAK PARAMETER (VARIADIC) -- BONUS
// ===================================================

function jumlahkanSemua(...$angka) {
    return array_sum($angka);
}

echo "Jumlah semua: " . jumlahkanSemua(1, 2, 3, 4, 5) . "<br>";

// Catatan buat kamu:
// - Bikin fungsi hitungLuasPersegiPanjang($panjang, $lebar) yang return hasil kali-nya
// - Lanjut ke folder includes-demo/ buat belajar include & require
