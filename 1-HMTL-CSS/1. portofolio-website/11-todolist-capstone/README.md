# Todo List — Capstone Project (Level 11)

Ini bukan lesson baru — ini **gabungan semua yang udah dipelajari** dari Stage 1 sampai 10, jadi satu aplikasi yang beneran kepake.

Buka `index.html`, tambahin beberapa todo, centang, hapus, **refresh halaman** — semuanya tetap ada.

---

## Fitur yang Ada

```
┌───────────────────────────────────────────┐
│  📝 My Todo List                            │
├───────────────────────────────────────────┤
│  [ Mau ngapain hari ini?      ] [+ Tambah] │  ← Stage 8: preventDefault
├───────────────────────────────────────────┤
│  [Semua] [Aktif] [Selesai]                 │  ← Stage 6/7: if/else, classList
├───────────────────────────────────────────┤
│  ☐ Belajar localStorage              [✕]   │  ← Stage 7: createElement
│  ☑ ~~Belajar preventDefault~~        [✕]   │  ← Stage 9: localStorage
│  ☐ Bikin capstone project            [✕]   │
├───────────────────────────────────────────┤
│  2 item tersisa      Hapus yang selesai    │
└───────────────────────────────────────────┘
```

- Tambah todo baru (form, `preventDefault`)
- Centang = selesai (checkbox, coret teks)
- Hapus todo satuan
- Filter: Semua / Aktif / Selesai
- Hapus semua yang udah selesai sekaligus
- **Semua data nempel di localStorage** — refresh browser, data tetap ada

---

## Peta Konsep — Semua Stage Ketemu Di Sini

```
Stage 6 (JS Syntax)     →  variabel, array, function, if/else
Stage 7 (DOM)           →  querySelector, createElement, appendChild, classList
Stage 8 (Events/Forms)  →  addEventListener("submit"), e.preventDefault()
Stage 9 (localStorage)  →  setItem/getItem, JSON.stringify/parse
                                    │
                                    ▼
                        Todo List App yang beneran jalan
```

---

## Bagian Paling Penting: Pola "Ubah Data → Simpan → Render Ulang"

Ini pola inti yang dipakai di HAMPIR SEMUA aplikasi web nyata (bukan cuma todo list):

```
1. User lakukan sesuatu (klik, ketik, submit)
         │
         ▼
2. Data di localStorage DIUBAH (tambah/hapus/edit array)
         │
         ▼
3. Data BARU disimpan balik ke localStorage
         │
         ▼
4. render() dipanggil ULANG → gambar ulang tampilan dari data terbaru
```

```js
function tambahTodo(teks) {
  const semuaTodo = ambilTodo();      // 1. ambil data lama
  semuaTodo.push({ id: Date.now(), teks: teks, selesai: false });  // 2. ubah data
  simpanTodo(semuaTodo);              // 3. simpan
  render();                           // 4. gambar ulang
}
```

Kenapa nggak langsung `appendChild()` satu item baru tanpa render ulang semua? Karena kalau langsung nambah 1 elemen doang, gampang ke-skip logic filter/kondisi lain. Render ulang semua dari data itu lebih **predictable** — tampilan SELALU sama persis dengan isi data. Ini prinsip yang nanti dipakai React (disebut "data-driven UI").

---

## Bagian yang Baru: Array Method Lain

Beberapa method array dipakai di sini yang belum dibahas detail sebelumnya:

```js
array.find(fn)      // cari SATU item yang cocok, return item-nya (bukan array)
array.filter(fn)     // ambil SEMUA item yang cocok, return array baru

// contoh:
const todo = semuaTodo.find(t => t.id === 5);      // { id: 5, teks: "...", selesai: false }
const belumSelesai = semuaTodo.filter(t => !t.selesai);  // [ {...}, {...} ]
```

```js
Date.now()   // angka unik berdasarkan waktu sekarang (dalam milidetik)
             // dipakai sebagai "id" tiap todo biar gampang dicari/dihapus
```

---

## Coba Sendiri

1. Tambah 3-4 todo, centang beberapa, coba klik filter Aktif/Selesai
2. Refresh halaman (F5) — semua data harus tetap ada
3. Buka DevTools (F12) → Application → Local Storage → lihat key `daftar-todo`, isinya array JSON mentah
4. Modifikasi: tambah fitur **edit todo** (klik teks → jadi input, bisa diubah, Enter buat save)
5. Modifikasi: tambah tanggal dibuat tiap todo, tampilkan di bawah teks (`new Date(todo.id).toLocaleDateString()`)

---

## Ini Fundamentalnya Sudah Lengkap

```
HTML struktur → CSS styling/responsive/layout → JS syntax → DOM → Events →
fetch() → localStorage → multi-page → Capstone (semua digabung)
```

Semua konsep dasar buat baca dan ngerti kode udah dikuasai. Langkah selanjutnya bukan belajar syntax baru lagi — tapi belajar **pakai AI** buat bantu/percepat bikin project portfolio asli, dengan pemahaman yang cukup buat baca dan validasi apa yang AI kasih (bukan copy-paste buta).
