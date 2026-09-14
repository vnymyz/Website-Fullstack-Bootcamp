# Sesi 6 — useEffect & Fetch (json-server)

Tujuan: ngobrol sama backend beneran (walau masih "palsu") — `json-server`, `useEffect`, async data.

## Setup json-server

Di `learn-react/`, install:

```
npm install -D json-server
```

Bikin file `db.json` di root `learn-react/`:

```json
{
  "tasks": [
    { "id": 1, "judul": "Setup json-server", "prioritas": "tinggi", "selesai": true },
    { "id": 2, "judul": "Belajar useEffect", "prioritas": "tinggi", "selesai": false },
    { "id": 3, "judul": "Fetch data dari API", "prioritas": "normal", "selesai": false }
  ]
}
```

Tambah script di `package.json`:

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "preview": "vite preview",
  "api": "json-server --watch db.json --port 3001"
}
```

Jalanin DUA terminal terpisah:

```
npm run dev     <- terminal 1, React di :5173
npm run api     <- terminal 2, fake REST API di :3001
```

Cek di browser: buka `http://localhost:3001/tasks` — itu udah jadi REST API beneran (GET, POST, PUT, DELETE semua jalan), tanpa kamu nulis backend sama sekali.

## `useEffect`: Kapan Kode Dijalankan Lagi?

```jsx
useEffect(() => {
  // kode di sini jalan SETELAH render
}, [dependency]); // <- array dependency
```

- `[]` (array kosong) → jalan **sekali** aja, pas component pertama kali muncul.
- `[id]` → jalan tiap kali `id` berubah nilainya.
- Gak ada array sama sekali → jalan **SETIAP** render (jarang dipakai, biasanya gak sengaja/bug).

## Tiga Status Wajib Setiap Fetch: loading / error / success

Ini kebiasaan yang harus dibangun dari fetch PERTAMA yang kamu tulis, jangan di-skip:

```jsx
const [data, setData] = useState(null);
const [loading, setLoading] = useState(true);
const [error, setError] = useState(null);
```

## Langkah — Fetch List dari json-server

`src/App.jsx`:

```jsx
import { useState, useEffect } from "react";

const API_URL = "http://localhost:3001/tasks";

export default function App() {
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Kenapa gak `async function useEffect...`? Karena useEffect HARUS
    // return undefined atau fungsi cleanup -- bukan Promise. Makanya kita
    // bikin fungsi async TERPISAH di dalam, terus panggil.
    async function fetchTasks() {
      try {
        setLoading(true);
        const res = await fetch(API_URL);
        if (!res.ok) throw new Error("Gagal ambil data");
        const data = await res.json();
        setTasks(data);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }
    fetchTasks();
  }, []); // [] -> cuma jalan sekali pas component pertama muncul

  if (loading) return <p className="p-6">Loading...</p>;
  if (error) return <p className="p-6 text-red-500">Error: {error}</p>;

  return (
    <main className="mx-auto max-w-md px-6 py-8">
      <h1 className="mb-4 text-2xl font-bold">Daftar Tugas (dari API)</h1>
      <ul className="space-y-2">
        {tasks.map((task) => (
          <li key={task.id} className="rounded border p-2">
            {task.judul} — <span className="text-xs uppercase">{task.prioritas}</span>
          </li>
        ))}
      </ul>
    </main>
  );
}
```

Refresh browser — kalau `npm run api` gak jalan, kamu bakal LIAT state error-nya (bukan cuma blank/crash). Itu tujuannya render 3 state di atas.

## Langkah — Tambah, Toggle, Hapus (POST/PATCH/DELETE)

```jsx
async function tambahTask(judul) {
  const res = await fetch(API_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ judul, prioritas: "normal", selesai: false }),
  });
  const newTask = await res.json();
  setTasks([...tasks, newTask]); // refetch-after-write pattern: update state lokal juga
}

async function toggleTask(id, selesai) {
  await fetch(`${API_URL}/${id}`, {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ selesai: !selesai }),
  });
  setTasks(tasks.map((t) => (t.id === id ? { ...t, selesai: !selesai } : t)));
}

async function hapusTask(id) {
  await fetch(`${API_URL}/${id}`, { method: "DELETE" });
  setTasks(tasks.filter((t) => t.id !== id));
}
```

Tempel ketiga fungsi ini di dalam component `App`, sambungin ke tombol-tombol di list.

## Bug Hunt — Infinite Loop Effect

Ini bug paling umum & paling bikin browser lag. Coba jalanin (HATI-HATI, refresh tab kalau lag):

```jsx
import { useState, useEffect } from "react";

export default function BugHuntEffect() {
  const [count, setCount] = useState(0);
  const [options, setOptions] = useState({ multiplier: 2 }); // <- object BARU tiap render

  useEffect(() => {
    console.log("effect jalan!");
    setCount((c) => c + options.multiplier);
  }, [options]); // <- BUG: object literal `{ multiplier: 2 }` dibikin ULANG tiap render

  return <p>{count}</p>;
}
```

<details>
<summary>Klik buat lihat jawaban (coba analisa dulu!)</summary>

**Bug: dependency array isinya object yang dibikin ulang tiap render.**

`options` di-`useState({ multiplier: 2 })`, tapi kalau ada kode lain yang bikin object BARU dengan isi sama tiap render (misal `{ multiplier: 2 }` ditulis inline di JSX tanpa `useState`), React ngecek dependency pakai `===` — dua object dengan isi sama tapi dibikin di waktu berbeda itu **BEDA reference**. Jadi effect dianggap "dependency berubah", jalan lagi, yang bisa memicu re-render, yang bikin object baru lagi, dst — infinite loop.

**Fix:** kalau dependency-nya object/array, pastikan dia BENERAN cuma berubah kalau perlu (simpan di `useState`/`useMemo`, jangan bikin literal baru tiap render), atau depend ke nilai primitif-nya langsung (`options.multiplier`) bukan ke object-nya.

</details>

## Checkpoint

Jelasin ke tutor: dikasih sebuah `useEffect` dengan dependency array tertentu, tebak kapan dia bakal jalan lagi (dan kapan enggak).

## Catatan buat Kamu

1. Matiin `npm run api` sebentar, refresh React — pastikan state `error` beneran muncul, bukan halaman blank.
2. Tambah fitur "refresh manual" (tombol yang manggil ulang `fetchTasks()`).
3. Coba ganti dependency array `useEffect` fetch tadi jadi tanpa array sama sekali (`}, )` tanpa `[]`) — buka DevTools Network tab, lihat request-nya jadi berapa kali.

Lanjut ke [07-router-multi-halaman.md](07-router-multi-halaman.md).
