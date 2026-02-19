// console.log("JavaScript is running now !");

// Variabel & Operasi Dasar

// deklarasi variabel
// let nama = "Andi";
// let umur = 16;

// console.log("Halo nama saya " + nama);
// console.log("Umur saya " + umur + " tahun");

// let angka1 = 10;
// let angka2 = 5;

// let hasil = angka1 + angka2;
// console.log("Hasil penjumlahan:", hasil);

// Percabangan (if / else) – Logika Keputusan

// dekalrasi variabel
// let nilai = 10;

// // kondisi 1
// if (nilai >= 80) {
//   // perintah
//   console.log("Nilai A");
// }
// // kondisi 2
// else if (nilai >= 70) {
//   // perintah
//   console.log("Nilai B");
// } else if (nilai >= 60) {
//   // perintah
//   console.log("Nilai C");
// } else {
//   console.log("Nilai D");
// }

// LOOPING FOR

// for (let i = 1; i <= 5; i++) {
//   console.log("Angka ke-" + i);
// }

// Function

// function hitungLuas(panjang, lebar) {
//   return panjang * lebar;
// }

// let hasil = hitungLuas(5, 4);
// console.log("Luas:", hasil);

// cek kelulusan siswa

function cekKelulusan(nilai) {
  if (nilai >= 75) {
    return "LULUS";
  } else {
    return "TIDAK LULUS";
  }
}

let nilaiSiswa = 80;
let hasil = cekKelulusan(nilaiSiswa);

console.log("Nilai:", nilaiSiswa);
console.log("Status:", hasil);
