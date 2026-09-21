# Sesi 1 — Kenapa React?

Tujuan: ngerasain masalah yang React selesaikan, sebelum belajar sintaksnya.

Project kerja: `5-React/learn-react/` (folder kosong — kita bikin project React-nya dari nol di sini).

**Hasil akhir sesi ini:**
- Node.js terpasang, project `learn-react/` jalan di `http://localhost:5173`.
- Kamu udah coba versi Vanilla JS dan versi React dari todo list yang sama, dan bisa jelasin bedanya.
- Kamu tau fungsi tiap file di project React (`index.html`, `main.jsx`, `App.jsx`, dst).

**Sebelum mulai:** gak ada. Ini sesi pertama.

**Peta langkah:**

| Langkah | Isi |
|---|---|
| Konsep | Server-side (PHP) vs client-side (React) |
| 1 | Pasang Node.js |
| 2 | Bikin project Vite + React |
| 3 | Jalanin & kenali project |
| 4 | Latihan A: todo list Vanilla JS |
| 5 | Latihan B: todo list React |
| 6 | Kenali anatomi project |

---

## Konsep Dasar: Server-side vs Client-side

Inget PHP: browser minta halaman → server (Apache) jalanin file `.php` → server kirim HTML jadi → browser tampilin. Tiap ada perubahan data, kamu **reload halaman**.

React beda. Browser download file JS (React) sekali → **JS itu sendiri yang bikin HTML-nya**, langsung di browser, tanpa reload. Makanya disebut **SPA (Single Page Application)** — satu halaman HTML doang (`index.html`), isinya dikendaliin penuh sama JS.

Analogi waiter/chef yang udah kamu tau: kalau PHP itu chef masak di dapur restoran terus kirim makanan jadi ke meja, React itu kayak dapur portable yang ikut dibawa ke meja customer — customer (browser) yang masak sendiri berdasarkan resep (kode React) yang dikasih.

| | PHP (server-rendered) | React (SPA) |
|---|---|---|
| Siapa yang bikin HTML | Server | Browser (lewat JS) |
| Ganti halaman | Browser minta halaman baru, reload penuh | JS ganti isi halaman, tanpa reload |
| Ambil data | Query DB langsung di file `.php` | `fetch` ke API (mulai sesi 6) |
| Yang dikirim server | HTML jadi | File JS + data JSON |

---

## Langkah 1 — Pasang Node.js

Node.js dibutuhin buat jalanin tool build (Vite) dan ngelola library (npm). React sendiri jalan di browser, tapi *proses bikin* project-nya butuh Node.

**1a.** Download **Node.js LTS** dari [nodejs.org](https://nodejs.org) dan install (Next terus sampai selesai).

**1b.** Buka terminal baru (PowerShell atau terminal VS Code: `` Ctrl+` ``), cek:

```
node -v
npm -v
```

### Cek

- [ ] `node -v` munculin nomor versi (contoh `v22.x.x`).
- [ ] `npm -v` munculin nomor versi juga.

**Kalau muncul "node is not recognized":** tutup semua terminal/VS Code, buka lagi (PATH baru kebaca setelah restart). Kalau masih gagal, install ulang dan pastikan opsi "Add to PATH" dicentang.

---

## Langkah 2 — Bikin Project Vite + React

**2a.** Pastikan folder `learn-react/` **kosong** (cek di File Explorer). Kalau ada sisa file, hapus dulu — kalau gak, Vite bakal nanya mau overwrite atau nggak.

**2b.** Masuk ke folder itu lewat terminal:

```
cd learn-react
```

**2c.** Bikin project langsung di folder ini (titik `.` di akhir = "di folder sini, jangan bikin folder baru"):

```
npm create vite@latest .
```

**2d.** Pas ditanya, pilih pakai tombol panah + Enter:
- Framework: **React**
- Variant: **JavaScript** (bukan TypeScript — kita belum pakai TS di materi ini)
- Kalau ada pertanyaan "Install with npm and start now?" — pilih **No** dulu, kita jalanin manual biar paham tiap langkahnya.

**2e.** Install dependency (library yang dibutuhin project, tercatat di `package.json`):

```
npm install
```

Ini bikin folder `node_modules/` — bisa beberapa puluh detik.

### Cek

- [ ] Di `learn-react/` sekarang ada: `index.html`, `package.json`, `src/`, `node_modules/`, dst.
- [ ] Gak ada error merah di terminal.

**Kalau muncul warning folder gak kosong:** pastikan `learn-react/` beneran kosong sebelum langkah 2c (lihat di File Explorer, hapus dulu isinya kalau ada sisa).

---

## Langkah 3 — Jalanin & Kenali Project

**3a.** Jalanin dev server:

```
npm run dev
```

**3b.** Terminal nampilin alamat, biasanya `http://localhost:5173`. Buka di browser (atau `Ctrl+Klik` alamatnya).

**3c.** Kamu bakal lihat halaman contoh Vite + React dengan tombol counter. Klik tombolnya — angkanya nambah tanpa reload halaman. Itu SPA.

**3d.** Coba edit sesuatu: buka `src/App.jsx`, ganti teks apa aja, tekan `Ctrl+S`. Browser update **sendiri**, tanpa kamu refresh (namanya Hot Module Replacement).

### Cek

- [ ] Halaman React muncul di `localhost:5173`.
- [ ] Ubah teks + save → browser update otomatis.

**Kalau halaman blank atau error:** lihat terminal `npm run dev` (error sintaks muncul di sana) dan Console browser (`F12` → Console).

**Kalau port 5173 udah dipakai:** Vite otomatis pakai port lain (5174, dst). Pakai alamat yang tertulis di terminal.

**Matiin server:** `Ctrl+C` di terminal. Server harus tetap jalan selama kamu ngoding.

---

## Kenapa Butuh `npm run dev`? (padahal katanya "jalan di browser")

Kode React yang kita tulis pakai JSX (`<div>...</div>` di dalam file `.jsx`) itu **bukan JS yang bisa langsung dimengerti browser**. Vite (`npm run dev`) yang nerjemahin JSX itu jadi JS biasa on-the-fly, sambil kasih fitur auto-refresh pas kamu save file. Pas mau di-deploy beneran, `npm run build` bikin versi final yang udah diterjemahin & dioptimasi, disimpen di folder `dist/`.

---

## Langkah 4 — Latihan A: Todo List Cara Manual (Vanilla JS)

Tujuan langkah ini: **ngerasain capeknya** ngatur tampilan secara manual, supaya kamu ngerti kenapa React ada.

File-nya udah disiapin di `react-journey/scratch-vanilla/` (folder terpisah, BUKAN bagian dari `learn-react/` — ini plain HTML+JS biasa, gak butuh npm/Vite sama sekali).

**4a.** Buka `react-journey/scratch-vanilla/index.html` — **double-click aja**, langsung kebuka di browser (gak perlu server, gak perlu `npm run dev`).

**4b.** Klik salah satu todo, perhatiin: teksnya jadi ke-coret (toggle status selesai).

**4c.** Buka `react-journey/scratch-vanilla/script.js` di VS Code, baca sambil ngetes klik-klik di browser — cocokin baris kode mana yang ngasilin efek apa.

Isi `script.js`:

```js
// script.js -- vanilla JS, TANPA React
let todos = [
  { id: 1, text: "Belajar HTML CSS JS", done: true },
  { id: 2, text: "Belajar React", done: false },
];

const listEl = document.getElementById("list-todo");

// Fungsi ini "manual" render ulang SELURUH list ke DOM.
// Tiap ada perubahan data, kita panggil ini lagi dari nol.
function render() {
  listEl.innerHTML = ""; // kosongin dulu, baru bikin ulang semua <li>
  todos.forEach((todo) => {
    const li = document.createElement("li");
    if (todo.done) li.classList.add("done");
    const span = document.createElement("span");
    span.textContent = todo.text;
    span.addEventListener("click", () => toggleTodo(todo.id));
    li.appendChild(span);
    listEl.appendChild(li);
  });
}

function toggleTodo(id) {
  todos = todos.map((t) => (t.id === id ? { ...t, done: !t.done } : t));
  render(); // manual: render ulang semua abis data berubah
}

render(); // render pertama kali
```

**Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `let todos = [...]` | Data-nya: array objek |
| `document.getElementById("list-todo")` | Cari elemen `<ul>` di halaman (manual) |
| `listEl.innerHTML = ""` | Hapus SEMUA isi list |
| `document.createElement("li")` | Bikin elemen `<li>` satu per satu (manual) |
| `addEventListener("click", ...)` | Pasang event klik ke tiap `<span>` (manual) |
| `render()` di ujung `toggleTodo` | Gambar ulang semuanya dari nol |

Perhatiin: **setiap kali data berubah**, kamu harus manual (1) cari elemen DOM, (2) bikin/hapus elemen, (3) pasang lagi ke halaman. Fungsi `render()` bongkar-pasang SEMUA `<li>` dari nol tiap kali.

### Cek

- [ ] Klik todo → teks ke-coret, klik lagi → balik normal.
- [ ] Kamu bisa nunjuk baris mana di `script.js` yang bikin `<li>`, dan baris mana yang manggil ulang `render()`.

---

## Langkah 5 — Latihan B: Todo List yang Sama, Pakai React

**5a.** Buka `learn-react/src/App.jsx`. Hapus semua isinya, ganti dengan:

```jsx
import { useState } from "react";

const initialTodos = [
  { id: 1, text: "Belajar HTML CSS JS", done: true },
  { id: 2, text: "Belajar React", done: false },
];

function App() {
  const [todos, setTodos] = useState(initialTodos);

  function toggleTodo(id) {
    setTodos(todos.map((t) => (t.id === id ? { ...t, done: !t.done } : t)));
  }

  return (
    <div style={{ maxWidth: 400, margin: "40px auto" }}>
      <h1>Todo List (React)</h1>
      <ul>
        {todos.map((todo) => (
          <li
            key={todo.id}
            onClick={() => toggleTodo(todo.id)}
            style={{ textDecoration: todo.done ? "line-through" : "none", cursor: "pointer" }}
          >
            {todo.text}
          </li>
        ))}
      </ul>
    </div>
  );
}

export default App;
```

**5b.** Save. Pastikan `npm run dev` masih jalan. Coba klik salah satu todo buat toggle status "selesai".

**Baca per bagian (ini cuma pengenalan, detailnya di sesi 3 dan 4):**

| Bagian | Artinya |
|---|---|
| `useState(initialTodos)` | "Ingat" data `todos` di dalam component. Balikin 2 hal: nilainya (`todos`) dan fungsi buat ngubahnya (`setTodos`) |
| `setTodos(...)` | Ubah data. Setiap dipanggil, React otomatis update tampilan |
| `todos.map(...)` di dalam JSX | Ubah tiap objek todo jadi satu `<li>` |
| `key={todo.id}` | Penanda unik tiap `<li>` (dijelasin di sesi 3) |
| `onClick={() => toggleTodo(todo.id)}` | Pasang event klik langsung di JSX |
| `style={{ ... }}` | Dua kurung kurawal: yang luar = "ini JS", yang dalam = "ini objek style" |

**Perhatiin:** kamu gak pernah nulis `document.createElement` atau `innerHTML`. Kamu cuma manggil `setTodos(...)` — React yang mikirin bagian mana dari tampilan yang perlu diupdate.

### Cek

- [ ] Klik todo → ke-coret, klik lagi → balik normal (sama kayak versi vanilla).
- [ ] Kamu bisa jelasin: di versi React, siapa yang ngurus update tampilan setelah data berubah?

**Kalau error `Objects are not valid as a React child`:** ada yang salah di `{todo.text}` — pastikan kamu render `todo.text` (string), bukan `todo` (objek).

---

## Apa itu "Virtual DOM"? (secukupnya aja dulu)

React nyimpen "salinan" ringan dari tampilan di memori (virtual DOM). Tiap kamu `setState`, React bandingin salinan lama vs baru, terus **cuma update bagian yang beda** ke DOM asli browser. Itu sebabnya kamu gak perlu manual `innerHTML = ""` kayak di vanilla JS. Belum perlu paham detail teknisnya sekarang — cukup tau kenapa kodenya jadi lebih sedikit.

---

## Langkah 6 — Kenali Anatomi Project

Buka tiap file ini di VS Code, lihat isinya, cocokin sama tabel:

| File / Folder | Fungsinya |
|---|---|
| `index.html` | Satu-satunya file HTML. Isinya cuma `<div id="root"></div>` kosong + tag `<script>` yang manggil `main.jsx` |
| `src/main.jsx` | Titik masuk. Nempelin React ke `<div id="root">` |
| `src/App.jsx` | Component utama, tempat kamu nulis kode React |
| `src/index.css` | CSS global |
| `package.json` | Daftar dependency + script (`npm run dev`, dll) |
| `node_modules/` | Isi library-nya beneran. **Jangan commit ke git** (bisa di-generate ulang lewat `npm install`) |
| `vite.config.js` | Konfigurasi Vite |

### Cek

- [ ] Buka `index.html`: kamu nemu `<div id="root">` dan tag `<script type="module" src="/src/main.jsx">`.
- [ ] Buka `src/main.jsx`: kamu nemu `createRoot(document.getElementById("root")).render(...)` — inilah yang nempelin `<App />` ke `root`.
- [ ] Kamu bisa jelasin urutan: `index.html` → `main.jsx` → `App.jsx`.

---

## Checkpoint

Jelasin pakai kata-kata sendiri: kenapa React butuh build step (`npm run dev`/`build`), sedangkan HTML/CSS biasa nggak?

## Catatan buat Kamu

1. Di Bagian A (vanilla), coba bayangin gimana caranya nambahin fitur "tambah todo baru" — kira-kira DOM manipulation apa aja yang perlu ditulis manual? Tulis daftarnya.
2. Di Bagian B (React), tambahin state `input` (`useState("")`) + `<input>` + tombol buat nambah todo baru ke `todos`. Bandingin effort-nya sama daftar kamu di poin 1.
3. Coba ganti teks di `<h1>`, save, lihat React auto-refresh (Hot Module Replacement) tanpa reload manual.

## Kalau Error

| Gejala | Biasanya penyebabnya |
|---|---|
| `node is not recognized` | Node belum ke-install / terminal belum di-restart |
| Halaman blank di `localhost:5173` | Ada error di Console (`F12`) atau terminal — baca pesannya |
| Perubahan gak muncul | Belum di-save (`Ctrl+S`), atau `npm run dev` udah mati |
| `EADDRINUSE` / port dipakai | Ada `npm run dev` lain masih jalan — tutup, atau pakai port yang ditunjukkin terminal |

Lanjut ke [02-jsx-dan-component.md](02-jsx-dan-component.md).
