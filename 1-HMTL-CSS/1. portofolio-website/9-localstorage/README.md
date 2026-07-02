# localStorage — Level 9

Semua data JavaScript yang kita pakai sejauh ini (variabel, array, object) **hilang** begitu halaman di-refresh. `localStorage` cara nyimpen data supaya nempel di browser, bertahan walau tab ditutup atau laptop di-restart.

Buka `index.html`, coba isi form, **refresh halaman (F5)**, lihat datanya masih ada.

---

## Kenapa localStorage Penting?

```
TANPA localStorage:                    DENGAN localStorage:
────────────────────────               ────────────────────────
Isi form → data ke variabel JS         Isi form → data ke variabel JS
       │                                      │
       ▼                                      ▼
Refresh halaman (F5)                   Refresh halaman (F5)
       │                                      │
       ▼                                      ▼
Variabel JS di-reset ke awal            localStorage.getItem() ambil
Data HILANG ❌                          data lama, tampil lagi ✅
```

Ini yang bikin todo list, dark mode preference, keranjang belanja, dll bisa "ingat" walau kamu tutup browser.

---

## 1. setItem() & getItem() — Simpan & Ambil

```
localStorage itu kayak LACI di browser kamu:

┌─────────────────────────────┐
│  localStorage                │
│  ┌─────────┬──────────────┐  │
│  │ key     │ value        │  │
│  ├─────────┼──────────────┤  │
│  │ "nama"  │ "Vanya"      │  │
│  └─────────┴──────────────┘  │
└─────────────────────────────┘
```

```js
localStorage.setItem("nama", "Vanya");   // simpan: key = "nama", value = "Vanya"
localStorage.getItem("nama");            // ambil: "Vanya"
localStorage.getItem("gaada");           // ambil yang belum ada: null
```

Penting: **localStorage cuma bisa nyimpen string**. Kalau kamu `setItem("angka", 5)`, dia otomatis diubah jadi teks `"5"`.

---

## 2. removeItem() & clear()

```js
localStorage.removeItem("nama");   // hapus SATU data (key "nama")
localStorage.clear();              // hapus SEMUA data localStorage website ini
```

Data localStorage **per-website** — punya website A tidak bisa dilihat/diubah website B.

---

## 3. Simpan Array / Object dengan JSON

localStorage cuma nerima string. Kalau mau simpan array atau object, harus "dibungkus" dulu:

```
Array asli:                    JSON.stringify()             String hasil:
["merah", "biru"]      ───────────────────────▶      '["merah","biru"]'


String dari localStorage:      JSON.parse()                  Array asli lagi:
'["merah","biru"]'      ───────────────────────▶      ["merah", "biru"]
```

```js
// SIMPAN array
let warna = ["merah", "biru", "hijau"];
localStorage.setItem("warna-favorit", JSON.stringify(warna));
// yang disimpan: string '["merah","biru","hijau"]'

// AMBIL array lagi
let data = localStorage.getItem("warna-favorit");
let warnaAsli = JSON.parse(data);
// warnaAsli sekarang array beneran: ["merah", "biru", "hijau"]
```

**Pola paling umum** dipakai (ini yang bakal kamu pakai terus di todo list):

```js
function ambilData() {
  const data = localStorage.getItem("key-nya");
  return data ? JSON.parse(data) : [];   // kalau belum ada data, kasih array kosong
}

function simpanData(array) {
  localStorage.setItem("key-nya", JSON.stringify(array));
}
```

---

## Ringkasan — Cheat Sheet

```
┌─────────────────────────────────────────────────────────────┐
│  DASAR                                                        │
│  localStorage.setItem(key, value)   → simpan string          │
│  localStorage.getItem(key)          → ambil (null kalau kosong) │
│  localStorage.removeItem(key)       → hapus satu              │
│  localStorage.clear()               → hapus semua             │
├─────────────────────────────────────────────────────────────┤
│  ARRAY / OBJECT                                                │
│  JSON.stringify(array)              → array → string          │
│  JSON.parse(string)                 → string → array asli     │
├─────────────────────────────────────────────────────────────┤
│  POLA UMUM                                                     │
│  const data = localStorage.getItem(k) ? JSON.parse(...) : []  │
│  saat load halaman → cek data lama, tampilkan                 │
│  saat data berubah → simpan ulang ke localStorage              │
└─────────────────────────────────────────────────────────────┘
```

---

## Coba Sendiri

1. Isi nama di Lesson 1 → klik Simpan → **refresh halaman** → nama masih muncul
2. Buka DevTools (F12) → tab **Application** (Chrome) atau **Storage** (Firefox) → klik **Local Storage** → lihat data mentahnya di sana
3. Di Lesson 3, tambah beberapa warna → refresh → semua warna masih ada
4. Hapus satu warna pakai tombol ✕ → refresh lagi → yang dihapus tetap hilang (karena localStorage ikut ke-update)
5. Klik "Clear Semua" → semua data hilang

---

## Apa Selanjutnya?

- **Stage 10 — Multi-page navigation**: hubungin beberapa file HTML jadi satu website (bukan cuma satu halaman)
- **Stage 11 — Todo List (capstone)**: gabungin semua yang udah dipelajari — DOM, events, preventDefault, dan localStorage — jadi 1 app yang beneran "hidup" (data nggak hilang pas refresh)
