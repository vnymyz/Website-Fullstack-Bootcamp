# Soal 4 — Notes App (localStorage)

Waktu: ~25 menit. **Tanpa AI.**

## Yang Harus Dibikin

Bikin aplikasi catatan kecil:

1. Ada input text + tombol "Tambah Catatan"
2. Klik tombol (atau Enter) → catatan baru muncul di list, input dikosongkan
3. Tiap catatan punya tombol hapus (✕) sendiri
4. Klik ✕ → catatan itu hilang dari list
5. **Semua catatan tersimpan di localStorage** — kalau halaman di-refresh, catatan yang udah ditambah tetap muncul
6. Kalau hapus catatan, harus ke-update juga di localStorage (bukan cuma hilang dari tampilan doang)

## Requirement Teknis

- Data catatan disimpan sebagai **array** di localStorage, dibungkus `JSON.stringify()`, dibaca balik pakai `JSON.parse()`
- Wajib ada 2 function terpisah: satu buat ambil data dari localStorage, satu buat simpan data ke localStorage (contoh nama: `ambilCatatan()` dan `simpanCatatan()`)
- Wajib ada function `render()` yang gambar ulang tampilan list dari data localStorage — dipanggil ulang tiap kali data berubah (tambah/hapus)
- Elemen `<li>` catatan dibuat pakai `document.createElement()`, bukan nulis HTML string manual (`innerHTML`)

## Tools/Syntax yang Dipakai

- JS localStorage: `localStorage.getItem("key")`, `localStorage.setItem("key", value)`
- JS convert data: `JSON.stringify(array)` (array → string, sebelum disimpan), `JSON.parse(string)` (string → array, pas dibaca)
- JS bikin elemen: `document.createElement("li")`, `document.createElement("button")`, `.appendChild()`
- JS ubah array: `.push(item)` (tambah di akhir), `.splice(index, 1)` (hapus 1 item di posisi tertentu)
- JS event: `.addEventListener("click", ...)`, opsional `.addEventListener("keydown", ...)` buat Enter
- 3 function terpisah: 1 buat ambil data (`ambilCatatan`), 1 buat simpan data (`simpanCatatan`), 1 buat gambar ulang tampilan (`render`)
- `render()` dipanggil di 3 tempat: pas tambah, pas hapus, dan sekali di paling bawah file (buat load data lama)

## File yang Dipakai

Edit `index.html`, `style.css`, `script.js` yang udah disediakan.

## Ditanya Pas Review

- "Kenapa data harus di-`JSON.stringify()` dulu sebelum disimpan ke localStorage?"
- "Coba jelasin alur lengkap dari klik tombol hapus sampai catatan itu beneran hilang dari localStorage"
- "Kenapa dipanggil `render()` ulang tiap kali ada perubahan, bukan langsung `appendChild()` satu item baru?"
