// ================================================
// SIMPLE CART — kode ini sengaja punya 5 bug.
// Cari & benerin. Baca INSTRUKSI.md buat aturan lengkap.
// ================================================

const hargaPerItem = 10000;
let jumlahItem = 0;
let totalHarga = 0;

const jumlahItemEl = document.querySelector("#jumlah-item");
const totalHargaEl = document.querySelector("#total-harga");

// --- Tambah item ---
// note: cek dulu id tombol ini sama id di index.html sama persis atau enggak
document.querySelector("#btn-tambah").addEventListener("click", function () {
  jumlahItem++;
  totalHarga = totalHarga + "10000"; // hati-hati tipe data di baris ini
  jumlahItemEl.textContent = jumlahItem;
  totalHargaEl.textContent = totalHarga;
});

// --- Dark mode toggle ---
const tombolDarkMode = document.querySelector("#btn-darkmode");

tombolDarkMode.addEventListener("click", function () {
  document.body.classList.toggle("dark-mode"); // cek nama class ini cocok sama style.css atau enggak

  if (document.body.classList.contains("dark-mode")) {
    tombolDarkMode.textContent = "☀️ Light Mode";
  } else {
    tombolDarkMode.textContent = "🌙 Dark Mode";
  }
});

// --- Tambah item custom lewat form ---
const formItemCustom = document.querySelector("#form-item-custom");

formItemCustom.addEventListener("submit", function (e) {
  // form ini reload halaman pas di-submit — kenapa ya?
  const nama = document.querySelector("#input-nama-item").value.trim();
  if (nama === "") return;

  const li = document.createElement("li");
  li.textContent = nama;
  document.querySelector("#daftar-item-custom").appendChild(li);

  document.querySelector("#input-nama-item").value = "";
});

// --- Reset ---
document.querySelector("#btn-reset").addEventListener("click", function () {
  jumlahItem = 0;
  jumlahItemEl.textContent = jumlahItem;
  // total harga di layar nggak ikut balik ke 0 — kenapa ya?
});
