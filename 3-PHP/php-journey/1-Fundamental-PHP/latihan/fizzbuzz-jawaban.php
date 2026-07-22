<?php
// ===================================================
// LATIHAN 1: FIZZBUZZ — JAWABAN
// ===================================================
//
// Soal asli ada di fizzbuzz.php. File ini isi contoh solusinya.
// Cek punya kamu dulu sebelum liat ini — nyocokin logic > nyalin.
//
// Kunci logic: cek kelipatan 15 (3 DAN 5) DULUAN, baru 3, baru 5.
// Kalau urutannya kebalik (cek 3 duluan), angka kelipatan 15
// bakal ke-print "Fizz" aja, gak pernah nyampe ke pengecekan "FizzBuzz".

for ($i = 1; $i <= 30; $i++) {
    if ($i % 3 == 0 && $i % 5 == 0) {
        echo "FizzBuzz<br>";
    } elseif ($i % 3 == 0) {
        echo "Fizz<br>";
    } elseif ($i % 5 == 0) {
        echo "Buzz<br>";
    } else {
        echo "$i<br>";
    }
}
