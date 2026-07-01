# JavaScript DOM — Level 7

DOM = **Document Object Model**. Ini cara JavaScript "melihat" dan "menyentuh" element HTML yang ada di halaman.

Buka `index.html` di browser, coba semua tombolnya, terus baca penjelasan di bawah.

---

## Apa itu DOM?

Bayangkan HTML kamu adalah sebuah pohon keluarga:

```
                    document
                       │
                     <html>
                    /       \
               <head>       <body>
                              │
                   ┌──────────┼──────────┐
                <header>    <main>    <footer>
                              │
                          <section>
                          /        \
                        <h2>      <button>
```

JavaScript bisa masuk ke "pohon" ini dan:
- **Ambil** element mana saja
- **Ubah** teks, warna, ukuran
- **Tambah** element baru
- **Hapus** element
- **Reaksi** saat user klik, ketik, scroll, dll

---

## 1. Mengambil Element HTML

```
HTML                          JavaScript
────────────────────          ─────────────────────────────────
<p id="judul">Halo</p>   →   document.querySelector("#judul")
<p class="kartu">...</p> →   document.querySelector(".kartu")
<button>Klik</button>    →   document.querySelector("button")
```

Ibarat kamu bilang ke JavaScript:
> "Tolong cari element yang punya id **judul** di halaman ini."

```js
const el = document.querySelector("#judul");
// el sekarang = element <p id="judul"> itu
```

---

## 2. Mengubah Isi & Style

Setelah ketemu element-nya, bisa ubah isinya:

```
┌─────────────────────────────────────────┐
│  el.textContent = "Teks baru"           │  → ubah teks
│  el.style.color = "red"                 │  → ubah warna teks
│  el.style.fontSize = "20px"             │  → ubah ukuran font
│  el.style.backgroundColor = "yellow"   │  → ubah warna background
└─────────────────────────────────────────┘
```

```js
const judul = document.querySelector("#judul");
judul.textContent = "Halo Vanya!";   // teks berubah di halaman
judul.style.color = "blue";          // warna jadi biru
```

---

## 3. Show / Hide dengan classList

Cara paling umum untuk tampilkan/sembunyikan sesuatu:

```
CSS punya class "hidden":          JS toggle class itu:
──────────────────────────         ──────────────────────────────
.hidden {                          el.classList.toggle("hidden")
  display: none;                   → kalau hidden ada → dihapus (muncul)
}                                  → kalau hidden tidak ada → ditambah (hilang)
```

```
SEBELUM klik:                      SESUDAH klik:
<div class="hidden">               <div class="">
  isi tersembunyi                    isi tersembunyi
</div>                             </div>
(tidak terlihat)                   (terlihat!)
```

```js
el.classList.toggle("hidden");   // tambah/hapus class
el.classList.add("aktif");       // tambah class
el.classList.remove("aktif");    // hapus class
el.classList.contains("aktif");  // cek apakah class ada (true/false)
```

---

## 4. addEventListener — Reaksi Saat Event Terjadi

```
User lakukan sesuatu  →  Browser deteksi  →  JavaScript jalankan fungsi
─────────────────────────────────────────────────────────────────────────
Klik tombol           →  "click"          →  function() { ... }
Ketik di input        →  "input"          →  function() { ... }
Submit form           →  "submit"         →  function() { ... }
Hover mouse           →  "mouseover"      →  function() { ... }
Tekan keyboard        →  "keydown"        →  function() { ... }
```

```js
const tombol = document.querySelector("#btn-klik");

tombol.addEventListener("click", function() {
  // kode ini jalan SETIAP KALI tombol diklik
  console.log("Tombol diklik!");
});
```

Bedanya dengan `onclick` di HTML:

```
onclick di HTML (cara lama):          addEventListener di JS (cara modern):
──────────────────────────────        ──────────────────────────────────────
<button onclick="fungsi()">           tombol.addEventListener("click", fungsi)
  Klik
</button>
```

Keduanya berfungsi, tapi `addEventListener` lebih fleksibel dan lebih banyak dipakai.

---

## 5. Ambil Nilai dari Input

```
User ketik "Vanya"
di input box          →   input.value   →   "Vanya"
                                             (ini string yang bisa dipakai)
```

```js
const input = document.querySelector("#nama");
const nilai = input.value;   // isi apa yang diketik user

input.value = "";            // kosongkan input (setelah dipakai)
```

---

## 6. Buat & Tambah Element Baru

```
Sebelum:                   Setelah tambahItem():
────────────────────────   ────────────────────────────────
<ul id="daftar">           <ul id="daftar">
  <li>Item 1</li>            <li>Item 1</li>
</ul>                        <li>Item 2</li>   ← baru dibuat JS
                           </ul>
```

```js
// 1. Buat element baru
const itemBaru = document.createElement("li");

// 2. Isi teksnya
itemBaru.textContent = "Item baru";

// 3. Taruh ke dalam <ul>
const daftar = document.querySelector("#daftar");
daftar.appendChild(itemBaru);
```

---

## 7. Form Validation

```
User klik "Submit"
       │
       ▼
Cek apakah input kosong?  ──── Ya ──→  tampilkan error, STOP
       │
      Tidak
       │
       ▼
Cek apakah password cukup panjang?  ── Ya ──→  tampilkan error, STOP
       │
      Tidak
       │
       ▼
Semua OK → tampilkan sukses ✅
```

```js
function cekForm() {
  const username = document.querySelector("#username").value;

  if (username === "") {
    // tampilkan error
    return;  // ← stop function, tidak lanjut ke bawah
  }

  // kalau sampai sini = lolos semua cek
  console.log("Form valid!");
}
```

---

## Ringkasan — Cheat Sheet

```
┌─────────────────────────────────────────────────────────────┐
│  AMBIL ELEMENT                                              │
│  document.querySelector("#id")      → cari by id           │
│  document.querySelector(".class")   → cari by class        │
│  document.querySelector("tag")      → cari by tag          │
├─────────────────────────────────────────────────────────────┤
│  UBAH KONTEN                                                │
│  el.textContent = "teks baru"       → ubah teks            │
│  el.innerHTML = "<b>teks</b>"       → ubah dengan HTML      │
├─────────────────────────────────────────────────────────────┤
│  UBAH STYLE                                                 │
│  el.style.color = "red"             → ubah warna teks       │
│  el.style.display = "none"          → sembunyikan           │
├─────────────────────────────────────────────────────────────┤
│  CLASS                                                      │
│  el.classList.add("nama")           → tambah class         │
│  el.classList.remove("nama")        → hapus class          │
│  el.classList.toggle("nama")        → tambah/hapus          │
│  el.classList.contains("nama")      → cek ada/tidak        │
├─────────────────────────────────────────────────────────────┤
│  EVENT                                                      │
│  el.addEventListener("click", fn)   → saat diklik          │
│  el.addEventListener("input", fn)   → saat diketik         │
│  el.addEventListener("keydown", fn) → saat tekan keyboard  │
├─────────────────────────────────────────────────────────────┤
│  BUAT ELEMENT BARU                                         │
│  document.createElement("li")       → buat element        │
│  parent.appendChild(child)          → taruh ke dalam       │
│  el.remove()                        → hapus dari halaman   │
├─────────────────────────────────────────────────────────────┤
│  INPUT                                                      │
│  input.value                        → ambil isi input      │
│  input.value = ""                   → kosongkan input      │
└─────────────────────────────────────────────────────────────┘
```

---

## Coba Sendiri

1. Buka `index.html` → coba semua tombol → lihat hasilnya
2. Buka `script.js` → baca kodenya → cocokkan sama yang terjadi di halaman
3. Ubah teks dalam `ubahTeks()` jadi kalimat lain → refresh → coba lagi
4. Di bagian counter, ubah `nilaiCounter++` jadi `nilaiCounter += 5` → coba klik → angka naik 5 sekarang
5. Tambahkan warna baru di bagian "Ubah Warna" → buat tombol baru di HTML + fungsinya di JS

## Apa Selanjutnya?

Setelah DOM, next step:
- **Events & Forms** yang lebih dalam (preventDefault, submit event)
- **fetch()** — ambil data dari internet (API)
- **Array methods** — `.map()`, `.filter()` untuk manipulasi data
- **Setelah itu bisa masuk React** — karena React pada dasarnya adalah DOM manipulation yang sudah diatur otomatis
