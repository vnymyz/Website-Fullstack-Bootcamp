// ================================================
// LESSON 1: MENGAMBIL & MENGUBAH TEKS
// document.querySelector("#id") → cari element berdasarkan id/class
// element.textContent → isi teks dari element itu
// ================================================

function ubahTeks() {
  // querySelector("#teks-demo") → cari element yang punya id="teks-demo"
  const el = document.querySelector("#teks-demo");
  el.textContent = "Teks berhasil diubah oleh JavaScript! 🎉";
  el.style.color = "#27ae60";
  el.style.fontWeight = "bold";
}

function kembalikanTeks() {
  const el = document.querySelector("#teks-demo");
  el.textContent = "Teks ini bisa diubah oleh JavaScript!";
  el.style.color = "";
  el.style.fontWeight = "";
}

// ================================================
// LESSON 2: MENGUBAH WARNA / STYLE
// element.style.propertyName → ubah CSS langsung dari JS
// ================================================

function gantiWarna(warna) {
  const judul = document.querySelector("#judul-warna");
  judul.style.color = warna;
  // warna kosong "" = kembali ke default CSS
}

// ================================================
// LESSON 3: SHOW / HIDE DENGAN classList
// classList.toggle("nama-class") → kalau class ada, hapus. Kalau tidak ada, tambah.
// classList.add("nama-class")    → tambah class
// classList.remove("nama-class") → hapus class
// ================================================

function toggleKotak() {
  const kotak = document.querySelector("#kotak-tersembunyi");
  const tombol = document.querySelector("#btn-toggle");

  kotak.classList.toggle("hidden"); // tambah/hapus class "hidden"

  // ubah teks tombol sesuai kondisi
  if (kotak.classList.contains("hidden")) {
    tombol.textContent = "Tampilkan Kotak";
  } else {
    tombol.textContent = "Sembunyikan Kotak";
  }
}

// ================================================
// LESSON 4: addEventListener — reaksi saat event terjadi
// element.addEventListener("event", function)
// "click" = saat diklik
// "input" = saat user mengetik
// "submit" = saat form disubmit
// ================================================

// querySelector cari tombol, simpan ke variabel
const tombolTambah = document.querySelector("#btn-tambah");
const tombolKurang = document.querySelector("#btn-kurang");
const tombolReset  = document.querySelector("#btn-reset");
const angkaEl      = document.querySelector("#angka-counter");

let nilaiCounter = 0; // variabel untuk menyimpan angka counter

tombolTambah.addEventListener("click", function() {
  nilaiCounter++;                          // tambah 1
  angkaEl.textContent = nilaiCounter;      // tampilkan ke halaman
  updateWarnaCounter();
  console.log("Counter sekarang:", nilaiCounter);
});

tombolKurang.addEventListener("click", function() {
  nilaiCounter--;                          // kurang 1
  angkaEl.textContent = nilaiCounter;
  updateWarnaCounter();
});

tombolReset.addEventListener("click", function() {
  nilaiCounter = 0;
  angkaEl.textContent = 0;
  angkaEl.style.color = "";
});

function updateWarnaCounter() {
  if (nilaiCounter > 0) {
    angkaEl.style.color = "#27ae60"; // hijau
  } else if (nilaiCounter < 0) {
    angkaEl.style.color = "#e74c3c"; // merah
  } else {
    angkaEl.style.color = "";
  }
}

// ================================================
// LESSON 5: AMBIL VALUE DARI INPUT
// input.value → isi dari input yang diketik user
// ================================================

function sapaUser() {
  const input = document.querySelector("#input-nama");
  const hasil = document.querySelector("#hasil-sapa");

  const nama = input.value; // .value = ambil teks yang diketik

  if (nama === "") {
    hasil.textContent = "Eh, nama kamu siapa? Isi dulu dong!";
    hasil.style.color = "#e74c3c";
  } else {
    hasil.textContent = "Halo, " + nama + "! Selamat datang! 👋";
    hasil.style.color = "#27ae60";
    input.value = ""; // kosongkan input setelah dipakai
  }
}

// ================================================
// LESSON 6: BUAT & TAMBAH ELEMENT BARU
// document.createElement("tag") → buat element HTML baru
// parent.appendChild(child)     → taruh element ke dalam parent
// element.remove()              → hapus element dari halaman
// ================================================

function tambahItem() {
  const input = document.querySelector("#input-item");
  const daftar = document.querySelector("#daftar-item");

  const teksItem = input.value.trim(); // .trim() buang spasi di awal/akhir

  if (teksItem === "") {
    alert("Ketik sesuatu dulu!");
    return; // stop function kalau input kosong
  }

  // 1. Buat element <li> baru
  const itemBaru = document.createElement("li");

  // 2. Isi teksnya
  itemBaru.textContent = teksItem + " ";

  // 3. Buat tombol hapus
  const tombolHapus = document.createElement("button");
  tombolHapus.textContent = "✕";
  tombolHapus.className = "btn-hapus";
  tombolHapus.onclick = function() {
    hapusItem(tombolHapus);
  };

  // 4. Taruh tombol ke dalam li
  itemBaru.appendChild(tombolHapus);

  // 5. Taruh li ke dalam ul (daftar)
  daftar.appendChild(itemBaru);

  // 6. Kosongkan input
  input.value = "";

  console.log("Item ditambah:", teksItem);
}

function hapusItem(tombol) {
  // tombol.parentElement = element <li> yang berisi tombol ini
  tombol.parentElement.remove();
}

// Enter key juga bisa tambah item
document.querySelector("#input-item").addEventListener("keydown", function(e) {
  if (e.key === "Enter") {
    tambahItem();
  }
});

// ================================================
// LESSON 7: FORM VALIDATION
// Cek apakah input sudah diisi sebelum lanjut
// ================================================

function cekForm() {
  const username = document.querySelector("#input-username").value.trim();
  const password = document.querySelector("#input-password").value;
  const pesan    = document.querySelector("#pesan-form");

  // Reset dulu
  pesan.className = "";

  if (username === "" || password === "") {
    pesan.textContent = "❌ Username dan password harus diisi!";
    pesan.className = "pesan-error";
    return;
  }

  if (password.length < 6) {
    pesan.textContent = "❌ Password minimal 6 karakter!";
    pesan.className = "pesan-error";
    return;
  }

  // Kalau lolos semua cek di atas
  pesan.textContent = "✅ Login berhasil! Halo, " + username + "!";
  pesan.className = "pesan-sukses";
}

// ================================================
// DARK MODE — classList.toggle di <body>
// Semua warna dark mode diatur di CSS
// ================================================

const tombolDarkMode = document.querySelector("#btn-darkmode");

tombolDarkMode.addEventListener("click", function() {
  document.body.classList.toggle("dark");

  if (document.body.classList.contains("dark")) {
    tombolDarkMode.textContent = "☀️ Light Mode";
  } else {
    tombolDarkMode.textContent = "🌙 Dark Mode";
  }
});
