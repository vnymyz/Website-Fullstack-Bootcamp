<?php
// ===================================================
// 1. TAG PHP, ECHO/PRINT, KOMENTAR
// ===================================================

// Ini komentar satu baris pakai "//"
# Ini juga komentar satu baris, tapi pakai "#" (jarang dipakai, tapi valid)

/*
  Ini komentar banyak baris.
  Bisa dipakai buat penjelasan panjang.
*/

echo "Halo, saya Leona saya lagi belajar php"; // echo = cara paling umum buat nampilin output
echo "<br>"; // "<br>" ini tag HTML biasa, PHP bisa nyampur sama HTML

print "Ini pakai print, hasilnya sama kayak echo.";
echo "<br>";

// Beda echo vs print: echo bisa nerima banyak argumen sekaligus, print cuma 1
echo "Ini ", "beberapa ", "string ", "digabung ", "pakai echo.";
echo "<br><br>";


// ===================================================
// 2. OPERATOR
// ===================================================

$a = 10;
$b = 3;

echo "Penjumlahan: " . ($a + $b) . "<br>";
echo "Pengurangan: " . ($a - $b) . "<br>";
echo "Perkalian: " . ($a * $b) . "<br>";
echo "Pembagian: " . ($a / $b) . "<br>";
echo "Sisa bagi (modulus): " . ($a % $b) . "<br>";

// Operator perbandingan penting: == vs === (mirip JS!)
var_dump(5 == "5");   // true, karena cuma bandingin NILAI (nilainya sama)
var_dump(5 === "5");  // false, karena beda TIPE (int vs string)
echo "<br>";

// Operator logika
$umur = 20;
if ($umur >= 17 && $umur < 60) {
    echo "Boleh bikin KTP.<br>";
}


// ===================================================
// 3. IF / ELSEIF / ELSE
// ===================================================

$nilai = 75;

if ($nilai >= 90) {
    echo "Grade: A<br>";
} elseif ($nilai >= 80) {
    echo "Grade: B<br>";
} elseif ($nilai >= 70) {
    echo "Grade: C<br>";
} else {
    echo "Grade: D<br>";
}


// ===================================================
// 4. SWITCH
// ===================================================

$hari = "Senin";

switch ($hari) {
    case "Senin":
        echo "Hari ini awal minggu, semangat!<br>";
        break; // "break" WAJIB, kalau lupa nanti kecondongan jatuh ke case bawahnya (fall-through)
    case "Jumat":
        echo "Yeay, udah mau weekend!<br>";
        break;
    case "Sabtu":
    case "Minggu":
        echo "Libur!<br>";
        break;
    default:
        echo "Hari biasa aja.<br>";
}

// Catatan buat kamu:
// - Coba ubah nilai $hari, $nilai, $umur di atas terus refresh browser, lihat hasilnya berubah.
// - Lanjut ke variables.php buat belajar soal variabel & tipe data lebih dalam.
