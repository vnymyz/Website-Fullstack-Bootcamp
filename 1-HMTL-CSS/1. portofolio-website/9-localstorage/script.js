// ================================================
// LESSON 1: setItem & getItem
// localStorage.setItem("key", "value") → simpan
// localStorage.getItem("key")          → ambil (null kalau belum ada)
// Data ini nempel di browser, TIDAK hilang walau tab ditutup / refresh
// ================================================

const inputNama = document.querySelector("#input-nama");
const hasilNama = document.querySelector("#hasil-nama");

document.querySelector("#btn-simpan-nama").addEventListener("click", function () {
  const nama = inputNama.value.trim();
  if (nama === "") return;

  localStorage.setItem("nama", nama); // simpan ke localStorage
  hasilNama.textContent = "Tersimpan: " + nama + " (coba refresh halaman!)";
  hasilNama.className = "pesan-sukses";
});

// Saat halaman dibuka/refresh, cek apakah ada data lama tersimpan
window.addEventListener("DOMContentLoaded", function () {
  const namaTersimpan = localStorage.getItem("nama");
  if (namaTersimpan) {
    inputNama.value = namaTersimpan;
    hasilNama.textContent = "Data lama ditemukan: " + namaTersimpan;
    hasilNama.className = "pesan-sukses";
  }
});

// ================================================
// LESSON 2: removeItem & clear
// removeItem("key") → hapus satu data spesifik
// clear()           → hapus SEMUA data localStorage website ini
// ================================================

document.querySelector("#btn-hapus-nama").addEventListener("click", function () {
  localStorage.removeItem("nama");
  inputNama.value = "";
  document.querySelector("#hasil-hapus").textContent = "Nama dihapus dari localStorage.";
});

document.querySelector("#btn-clear").addEventListener("click", function () {
  localStorage.clear();
  document.querySelector("#hasil-hapus").textContent = "Semua data localStorage dihapus.";
  location.reload(); // reload biar semua form keliatan kosong lagi
});

// ================================================
// LESSON 3: simpan array pakai JSON.stringify / JSON.parse
// localStorage CUMA bisa nyimpen string.
// Array/object harus "dibungkus" jadi string dulu.
// ================================================

const inputWarna = document.querySelector("#input-warna");
const daftarWarna = document.querySelector("#daftar-warna");

// ambil array warna dari localStorage, atau array kosong kalau belum ada
function ambilWarna() {
  const data = localStorage.getItem("warna-favorit");
  return data ? JSON.parse(data) : []; // JSON.parse: string → array asli
}

function simpanWarna(array) {
  localStorage.setItem("warna-favorit", JSON.stringify(array)); // array → string
}

function tampilkanWarna() {
  const warnaList = ambilWarna();
  daftarWarna.innerHTML = ""; // kosongkan dulu, render ulang dari data

  warnaList.forEach(function (warna, index) {
    const li = document.createElement("li");
    li.textContent = warna;

    const tombolHapus = document.createElement("button");
    tombolHapus.textContent = "✕";
    tombolHapus.className = "btn-hapus-item";
    tombolHapus.addEventListener("click", function () {
      const warnaBaru = ambilWarna();
      warnaBaru.splice(index, 1); // buang 1 item di posisi index
      simpanWarna(warnaBaru);
      tampilkanWarna(); // render ulang
    });

    li.appendChild(tombolHapus);
    daftarWarna.appendChild(li);
  });
}

document.querySelector("#btn-tambah-warna").addEventListener("click", function () {
  const warna = inputWarna.value.trim();
  if (warna === "") return;

  const warnaList = ambilWarna();
  warnaList.push(warna);
  simpanWarna(warnaList);

  inputWarna.value = "";
  tampilkanWarna();
});

// render pertama kali saat halaman dibuka (ambil data lama kalau ada)
tampilkanWarna();
