<?php
// ===================================================
// LATIHAN 1: FIZZBUZZ
// ===================================================
//
// Soal:
// Tampilin angka 1 sampai 30. Tapi:
// - Kalau angkanya kelipatan 3, tampilin "Fizz" (ganti angkanya)
// - Kalau angkanya kelipatan 5, tampilin "Buzz" (ganti angkanya)
// - Kalau angkanya kelipatan 3 DAN 5, tampilin "FizzBuzz"
// - Selain itu, tampilin angkanya seperti biasa
//
// Contoh output (1 sampai 15):
// 1
// 2
// Fizz
// 4
// Buzz
// Fizz
// 7
// 8
// Fizz
// Buzz
// 11
// Fizz
// 13
// 14
// FizzBuzz
//
// Petunjuk:
// - Pakai for loop dari 1 sampai 30
// - Pakai operator modulus (%) buat ngecek kelipatan
// - Urutan pengecekan penting! Cek kelipatan 15 duluan, baru 3, baru 5
//   (atau cek kombinasi 3 DAN 5 pakai &&)

for ($i = 1; $i <= 30; $i++) {
    // TODO: tulis logic FizzBuzz di sini

    echo "$i<br>"; // baris ini nanti diganti, sekarang masih nampilin angka biasa
}
