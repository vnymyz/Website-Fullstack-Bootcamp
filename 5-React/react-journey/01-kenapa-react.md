# Sesi 1 — Kenapa React?

Tujuan: ngerasain masalah yang React selesaikan, sebelum belajar sintaksnya.

Project kerja: `5-React/learn-react/` (folder kosong — kita bikin project React-nya dari nol di sini).

## Konsep Dasar: Server-side vs Client-side

Inget PHP: browser minta halaman → server (Apache) jalanin file `.php` → server kirim HTML jadi → browser tampilin. Tiap ada perubahan data, kamu **reload halaman**.

React beda. Browser download file JS (React) sekali → **JS itu sendiri yang bikin HTML-nya**, langsung di browser, tanpa reload. Makanya disebut **SPA (Single Page Application)** — satu halaman HTML doang (`index.html`), isinya dikendaliin penuh sama JS.

Analogi waiter/chef yang udah kamu tau: kalau PHP itu chef masak di dapur restoran terus kirim makanan jadi ke meja, React itu kayak dapur portable yang ikut dibawa ke meja customer — customer (browser) yang masak sendiri berdasarkan resep (kode React) yang dikasih.

## Setup

1. Install Node.js LTS dari [nodejs.org](https://nodejs.org). Cek: `node -v` dan `npm -v`.
2. Masuk ke folder `learn-react/` (masih kosong), bikin project Vite + React langsung di situ (titik `.` di akhir = "di folder sini, jangan bikin folder baru"):
   ```
   cd learn-react
   npm create vite@latest .
   ```
3. Pas ditanya, pilih:
   - Framework: **React**
   - Variant: **JavaScript** (bukan TypeScript — kita belum pakai TS di materi ini)
4. Install dependency & jalanin dev server:
   ```
   npm install
   npm run dev
   ```
5. Buka alamat yang muncul (biasanya `http://localhost:5173`).

**Kalau muncul warning folder gak kosong:** pastikan `learn-react/` beneran kosong sebelum langkah 2 (cek `ls -la` / lihat di File Explorer, hapus dulu isinya kalau ada sisa).

## Latihan Perbandingan: Todo List, Vanilla JS vs React

### Bagian A — Cara Manual (Vanilla JS, dijalanin beneran)

File-nya udah disiapin di `react-journey/scratch-vanilla/` (folder terpisah, BUKAN bagian dari `learn-react/` — ini plain HTML+JS biasa, gak butuh npm/Vite sama sekali).

1. Buka `react-journey/scratch-vanilla/index.html` — **double-click aja**, langsung kebuka di browser (gak perlu server, gak perlu `npm run dev`).
2. Klik salah satu todo, perhatiin: teksnya jadi ke-coret (toggle status selesai).
3. Baca `react-journey/scratch-vanilla/script.js` sambil ngetes klik-klik di browser — cocokin baris kode mana yang ngasilin efek apa.

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

Perhatiin: **setiap kali data berubah**, kamu harus manual (1) cari elemen DOM, (2) bikin/hapus elemen, (3) pasang lagi ke halaman. Fungsi `render()` bongkar-pasang SEMUA `<li>` dari nol tiap kali.

### Bagian B — Bikin di `learn-react/src/App.jsx`

Ganti isi `src/App.jsx` jadi:

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

Jalanin `npm run dev`, coba klik salah satu todo buat toggle status "selesai".

**Perhatiin:** kamu gak pernah nulis `document.createElement` atau `innerHTML`. Kamu cuma manggil `setTodos(...)` — React yang mikirin bagian mana dari tampilan yang perlu diupdate.

## Apa itu "Virtual DOM"? (secukupnya aja dulu)

React nyimpen "salinan" ringan dari tampilan di memori (virtual DOM). Tiap kamu `setState`, React bandingin salinan lama vs baru, terus **cuma update bagian yang beda** ke DOM asli browser. Itu sebabnya kamu gak perlu manual `innerHTML = ""` kayak di vanilla JS. Belum perlu paham detail teknisnya sekarang — cukup tau kenapa kodenya jadi lebih sedikit.

## Kenapa Butuh `npm run dev`? (padahal katanya "jalan di browser")

Kode React yang kita tulis pakai JSX (`<div>...</div>` di dalam file `.jsx`) itu **bukan JS yang bisa langsung dimengerti browser**. Vite (`npm run dev`) yang nerjemahin JSX itu jadi JS biasa on-the-fly, sambil kasih fitur auto-refresh pas kamu save file. Pas mau di-deploy beneran, `npm run build` bikin versi final yang udah diterjemahin & dioptimasi, disimpen di folder `dist/`.

## Project Anatomy

- `index.html` — satu-satunya file HTML, isinya cuma `<div id="root"></div>` kosong.
- `src/main.jsx` — titik masuk, nempelin React ke `<div id="root">`.
- `src/App.jsx` — component utama, tempat kamu nulis kode React.
- `package.json` — daftar dependency + script (`npm run dev`, dll).
- `node_modules/` — isi library-nya beneran (jangan commit ke git, bisa di-generate ulang lewat `npm install`).

## Checkpoint

Jelasin pakai kata-kata sendiri: kenapa React butuh build step (`npm run dev`/`build`), sedangkan HTML/CSS biasa nggak?

## Catatan buat Kamu

1. Di Bagian A (vanilla), coba bayangin gimana caranya nambahin fitur "tambah todo baru" — kira-kira DOM manipulation apa aja yang perlu ditulis manual?
2. Di Bagian B (React), tambahin state `input` (`useState("")`) + `<input>` + tombol buat nambah todo baru ke `todos`. Bandingin effort-nya sama bayangan kamu di poin 1.
3. Coba ganti teks di `<h1>`, save, lihat React auto-refresh (Hot Module Replacement) tanpa reload manual.

Lanjut ke [02-jsx-dan-component.md](02-jsx-dan-component.md).
