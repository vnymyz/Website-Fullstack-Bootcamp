# Sesi 6 — useEffect & Fetch (json-server)

Tujuan: ngobrol sama backend beneran (walau masih "palsu") — `json-server`, `useEffect`, async data.

**Hasil akhir sesi ini:**
- `json-server` jalan sebagai REST API palsu di `localhost:3001`.
- `App.jsx` ngambil daftar tugas dari API (dengan status loading / error / sukses).
- UI lengkap: tambah tugas (POST), toggle selesai pakai checkbox (PATCH), edit judul (PATCH), hapus (DELETE) — datanya tersimpan beneran di `db.json`.

**Sebelum mulai:** sesi 5 selesai. Kamu paham `useState`, form controlled, dan `preventDefault`.

**Peta langkah:**

| Langkah | Isi |
|---|---|
| Konsep | Apa itu API, `useEffect`, 3 status fetch |
| 1 | Pasang & jalanin json-server (+ cegah Vite reload) |
| 2 | Ambil daftar tugas (GET) |
| 3 | Tambah tugas (POST) |
| 4 | Toggle selesai (PATCH) pakai checkbox |
| 5 | Hapus (DELETE) |
| 6 | Edit judul (PATCH) |
| Bug Hunt | Infinite loop effect |

---

## Konsep

### Apa itu REST API? (versi singkat)

Backend nyediain "alamat" (URL) yang bisa kamu minta datanya lewat HTTP. Jenis permintaan ditentuin oleh **method**:

| Method | Artinya | Contoh URL | Setara di PHP/SQL |
|---|---|---|---|
| `GET` | Ambil data | `GET /tasks` | `SELECT` |
| `POST` | Tambah data baru | `POST /tasks` | `INSERT` |
| `PATCH` | Ubah sebagian field | `PATCH /tasks/3` | `UPDATE` |
| `DELETE` | Hapus | `DELETE /tasks/3` | `DELETE` |

Format datanya **JSON** — teks mirip objek JavaScript, kamu udah sering lihat.

### `useEffect` = "Lakuin ini SETELAH tampil di layar"

Fetch data itu **efek samping** (side effect): ngerjain sesuatu di luar "gambar tampilan". Efek samping gak boleh langsung di badan component (bakal jalan tiap render). `useEffect` yang ngatur kapan jalannya.

### Kenapa Ada 3 Status?

Ngambil data dari server itu butuh waktu dan bisa gagal. Jadi ada **tiga keadaan** yang wajib kamu tampilkan:

| Status | State | Tampilan |
|---|---|---|
| Lagi nunggu | `loading = true` | "Loading..." |
| Gagal | `error = "pesan"` | "Error: ..." |
| Berhasil | `data` terisi | Daftar data |

---

## Langkah 1 — Setup json-server

`json-server` = program kecil yang ngebaca file `db.json` dan otomatis jadi REST API lengkap.

### 1a. Pasang

Di `learn-react/`, install:

```
npm install -D json-server
```

### 1b. Bikin `db.json`

Bikin file `db.json` di root `learn-react/` (sejajar `package.json`):

```json
{
  "tasks": [
    { "id": 1, "judul": "Setup json-server", "prioritas": "tinggi", "selesai": true },
    { "id": 2, "judul": "Belajar useEffect", "prioritas": "tinggi", "selesai": false },
    { "id": 3, "judul": "Fetch data dari API", "prioritas": "normal", "selesai": false }
  ]
}
```

### 1c. Tambah script di `package.json`

Di bagian `"scripts"`, tambah baris `"api"` (jangan lupa koma di baris sebelumnya):

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "preview": "vite preview",
  "api": "json-server --watch db.json --port 3001"
}
```

### 1d. Cegah Vite ngereload halaman tiap data berubah

`json-server` **nulis ulang `db.json`** tiap kamu tambah/edit/hapus data. Karena `db.json` ada di dalam folder project Vite, Vite ngira itu perubahan kode dan **me-reload seluruh halaman** — halamanmu "lompat ke atas" tiap kali klik sesuatu. Suruh Vite ngabaikan file itu.

Edit `vite.config.js`:

```js
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    // json-server nulis ke db.json tiap add/edit/hapus; tanpa ini Vite
    // nganggep itu perubahan kode dan reload seluruh halaman.
    watch: { ignored: ["**/db.json"] },
  },
});
```

### 1e. Jalanin DUA terminal terpisah

```
npm run dev     <- terminal 1, React di :5173
npm run api     <- terminal 2, fake REST API di :3001
```

(Kalau `npm run dev` udah jalan dari tadi, **restart dulu** — config Vite cuma kebaca pas startup.)

### Cek

- [ ] Buka `http://localhost:3001/tasks` di browser — itu udah jadi REST API beneran (GET, POST, PUT, DELETE semua jalan), tanpa kamu nulis backend sama sekali. Harusnya muncul 3 tugas dalam format JSON.
- [ ] `http://localhost:3001/tasks/2` → cuma tugas nomor 2.
- [ ] Terminal `npm run api` nampilin daftar endpoint tanpa error.

---

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

---

## Langkah 2 — Ambil Daftar Tugas (GET)

Kita tulis dulu versi paling sederhana: **fetch + tampilkan**.

`src/App.jsx` (ganti isinya):

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

**Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `useEffect(() => {...}, [])` | Jalanin fungsi ini sekali setelah component pertama tampil |
| `async function fetchTasks()` di dalam effect | Fungsi async terpisah (effect sendiri gak boleh `async`) |
| `try { ... } catch { ... } finally { ... }` | Coba dulu; kalau gagal ke `catch`; `finally` jalan di kedua kasus |
| `await fetch(API_URL)` | "Tunggu sampai server jawab" |
| `if (!res.ok) throw ...` | `fetch` **gak** error sendiri buat status 404/500; harus dicek manual |
| `await res.json()` | Ubah teks JSON dari server jadi array/objek JS |
| `setLoading(false)` di `finally` | Berhenti nampilin "Loading..." baik sukses maupun gagal |
| `if (loading) return ...` | **Early return**: kalau masih loading, tampilkan itu aja |

**Aturan penting:** semua `useState` dan `useEffect` **harus di atas** `if (loading) return ...`. Hook gak boleh dipanggil setelah early return (aturan hooks, sesi 8). Ini bakal penting banget pas kamu nambah state baru di langkah berikutnya.

### Cek

- [ ] Refresh browser → sekilas "Loading...", lalu 3 tugas dari `db.json` tampil.
- [ ] Buka DevTools → **Network** → lihat request `tasks` (status 200).
- [ ] **Matiin `npm run api`** (`Ctrl+C` di terminal-nya), refresh React — kamu bakal LIAT state error-nya "Error: Failed to fetch" (bukan cuma blank/crash). Itu tujuannya render 3 state di atas. Nyalain lagi API-nya setelah tes.

---

## Langkah 3 — Tambah Tugas (POST)

Sekarang kita bikin UI nambah tugas dan simpan ke server.

### 3a. Tulis fungsi `tambahTask`

Taruh di dalam component `App` (di bawah `useEffect`):

```jsx
async function tambahTask(task) {
  const res = await fetch(API_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ ...task, selesai: false }),
  });
  const newTask = await res.json();
  setTasks([...tasks, newTask]); // refetch-after-write pattern: update state lokal juga
}
```

**Baca per bagian:**
- `method: "POST"` — bilang ke server "ini permintaan tambah data". (Default `fetch` itu GET.)
- `headers: { "Content-Type": "application/json" }` — kasih tau body-nya JSON.
- `body: JSON.stringify(...)` — body request cuma bisa teks, jadi objek diubah dulu jadi teks JSON.
- `const newTask = await res.json()` — server balikin data yang baru dibuat **lengkap dengan `id` baru**. Pakai itu (bukan bikin objek sendiri) supaya `id`-nya asli dari server.
- `setTasks([...tasks, newTask])` — update tampilan tanpa fetch ulang (immutable add, sesi 4).

### 3b. Tulis state + form di JSX

Tambah state di atas (bareng `useState` lain — **sebelum** early return):

```jsx
const [judulBaru, setJudulBaru] = useState("");
const [prioritasBaru, setPrioritasBaru] = useState("normal");
```

Fungsi submit:

```jsx
async function handleSubmitBaru(e) {
  e.preventDefault(); // cegah halaman reload
  if (!judulBaru.trim()) return;
  await tambahTask({ judul: judulBaru, prioritas: prioritasBaru });
  setJudulBaru("");
  setPrioritasBaru("normal");
}
```

JSX (di atas `<ul>`):

```jsx
<form onSubmit={handleSubmitBaru} className="mb-4 flex gap-2">
  <input
    value={judulBaru}
    onChange={(e) => setJudulBaru(e.target.value)}
    placeholder="Tugas baru..."
    className="flex-1 rounded border px-2 py-1"
  />
  <select
    value={prioritasBaru}
    onChange={(e) => setPrioritasBaru(e.target.value)}
    className="rounded border px-2 py-1"
  >
    <option value="rendah">Rendah</option>
    <option value="normal">Normal</option>
    <option value="tinggi">Tinggi</option>
  </select>
  <button type="submit" className="rounded bg-blue-600 px-3 py-1 text-white">
    Tambah
  </button>
</form>
```

### Cek

- [ ] Ketik judul → Tambah → tugas muncul di list.
- [ ] Buka `http://localhost:3001/tasks` di tab lain → tugas baru **ada di sana** (bukti beneran tersimpan di `db.json`). Buka file `db.json` juga — isinya nambah.
- [ ] Refresh halaman → tugas tetap ada.
- [ ] Halaman **gak reload / lompat ke atas** pas submit (kalau iya, cek langkah 1d dan restart `npm run dev`).

---

## Langkah 4 — Toggle Selesai (PATCH) Pakai Checkbox

### 4a. Fungsi `toggleTask`

```jsx
async function toggleTask(id, selesai) {
  await fetch(`${API_URL}/${id}`, {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ selesai: !selesai }),
  });
  setTasks(tasks.map((t) => (t.id === id ? { ...t, selesai: !selesai } : t)));
}
```

- `PATCH` = ubah **sebagian** field (di sini cuma `selesai`). Beda dengan `PUT` yang ganti keseluruhan objek.
- `` `${API_URL}/${id}` `` → URL spesifik satu tugas, misal `http://localhost:3001/tasks/2`.
- `!selesai` = kebalikannya (true jadi false, false jadi true).

### 4b. Ganti tampilan `<li>` jadi checkbox

```jsx
<li key={task.id} className="flex items-center justify-between gap-2 rounded border p-2">
  <label className="flex flex-1 items-center gap-2">
    <input
      type="checkbox"
      checked={task.selesai}
      onChange={() => toggleTask(task.id, task.selesai)}
    />
    <span className={task.selesai ? "line-through text-slate-400" : ""}>
      {task.judul} — <span className="text-xs uppercase">{task.prioritas}</span>
    </span>
  </label>
</li>
```

- `checked={task.selesai}` — centang tergantung data (controlled, sama kayak sesi 5).
- Dibungkus `<label>` supaya klik teksnya juga menceklis kotak.

### Cek

- [ ] Klik checkbox → tugas ke-coret. Refresh → status tetap.
- [ ] Di `db.json`, field `selesai` tugas itu ikut berubah.

---

## Langkah 5 — Hapus (DELETE)

### 5a. Fungsi `hapusTask`

```jsx
async function hapusTask(id) {
  await fetch(`${API_URL}/${id}`, { method: "DELETE" });
  setTasks(tasks.filter((t) => t.id !== id));
}
```

### 5b. Tombol di `<li>` (setelah `</label>`)

```jsx
<button onClick={() => hapusTask(task.id)} className="text-sm text-red-500">
  Hapus
</button>
```

### Cek

- [ ] Klik Hapus → tugas hilang dari list.
- [ ] Refresh → tetap hilang. Cek `db.json`, barisnya beneran hilang.

---

## Langkah 6 — Edit Judul (PATCH)

Bikin satu tugas bisa diubah judulnya. Kita pakai **mode edit di baris itu sendiri**: klik Edit → judul jadi input → Simpan/Batal.

### 6a. State edit

Tambah di atas (sebelum early return):

```jsx
const [editingId, setEditingId] = useState(null); // id tugas yang lagi diedit
const [editJudul, setEditJudul] = useState("");
```

`editingId` = `null` berarti gak ada yang lagi diedit.

### 6b. Fungsi edit

```jsx
function mulaiEdit(task) {
  setEditingId(task.id);
  setEditJudul(task.judul); // isi input dengan judul lama
}

async function simpanEdit(e, id) {
  e.preventDefault();
  if (!editJudul.trim()) return;
  await fetch(`${API_URL}/${id}`, {
    method: "PATCH",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ judul: editJudul }),
  });
  setTasks(tasks.map((t) => (t.id === id ? { ...t, judul: editJudul } : t)));
  setEditingId(null); // keluar dari mode edit
}
```

### 6c. Ubah JSX `<li>`: dua tampilan tergantung mode

```jsx
<li key={task.id} className="flex items-center justify-between gap-2 rounded border p-2">
  {editingId === task.id ? (
    /* mode edit */
    <form onSubmit={(e) => simpanEdit(e, task.id)} className="flex flex-1 gap-2">
      <input
        value={editJudul}
        onChange={(e) => setEditJudul(e.target.value)}
        className="flex-1 rounded border px-2 py-1"
        autoFocus
      />
      <button type="submit" className="text-sm text-blue-600">Simpan</button>
      <button type="button" onClick={() => setEditingId(null)} className="text-sm text-slate-500">
        Batal
      </button>
    </form>
  ) : (
    /* mode biasa */
    <>
      <label className="flex flex-1 items-center gap-2">
        <input
          type="checkbox"
          checked={task.selesai}
          onChange={() => toggleTask(task.id, task.selesai)}
        />
        <span className={task.selesai ? "line-through text-slate-400" : ""}>
          {task.judul} — <span className="text-xs uppercase">{task.prioritas}</span>
        </span>
      </label>
      <button onClick={() => mulaiEdit(task)} className="text-sm text-blue-600">Edit</button>
      <button onClick={() => hapusTask(task.id)} className="text-sm text-red-500">Hapus</button>
    </>
  )}
</li>
```

**Baca per bagian:**
- `editingId === task.id ? (...) : (...)` — cuma **satu** baris yang jadi mode edit (yang id-nya cocok). Baris lain tetap biasa.
- `type="button"` di tombol Batal — supaya gak ikut men-submit form.
- `autoFocus` — kursor langsung masuk ke input.
- Field di mode edit dikontrol state `editJudul` yang terpisah — jadi kalau kamu klik Batal, judul asli di list gak berubah.

### Cek

- [ ] Klik Edit → judul jadi kolom input berisi judul lama.
- [ ] Ubah → Simpan → judul berubah di list dan di `db.json`.
- [ ] Edit lalu Batal → gak ada yang berubah.

### Kode lengkap `App.jsx` (buat dicocokin)

```jsx
import { useState, useEffect } from "react";

const API_URL = "http://localhost:3001/tasks";

export default function App() {
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [judulBaru, setJudulBaru] = useState("");
  const [prioritasBaru, setPrioritasBaru] = useState("normal");
  const [editingId, setEditingId] = useState(null);
  const [editJudul, setEditJudul] = useState("");

  useEffect(() => {
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
  }, []);

  if (loading) return <p className="p-6">Loading...</p>;
  if (error) return <p className="p-6 text-red-500">Error: {error}</p>;

  async function tambahTask(task) {
    const res = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ...task, selesai: false }),
    });
    const newTask = await res.json();
    setTasks([...tasks, newTask]);
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

  async function handleSubmitBaru(e) {
    e.preventDefault();
    if (!judulBaru.trim()) return;
    await tambahTask({ judul: judulBaru, prioritas: prioritasBaru });
    setJudulBaru("");
    setPrioritasBaru("normal");
  }

  function mulaiEdit(task) {
    setEditingId(task.id);
    setEditJudul(task.judul);
  }

  async function simpanEdit(e, id) {
    e.preventDefault();
    if (!editJudul.trim()) return;
    await fetch(`${API_URL}/${id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ judul: editJudul }),
    });
    setTasks(tasks.map((t) => (t.id === id ? { ...t, judul: editJudul } : t)));
    setEditingId(null);
  }

  return (
    <main className="mx-auto max-w-md px-6 py-8">
      <h1 className="mb-4 text-2xl font-bold">Daftar Tugas (dari API)</h1>

      <form onSubmit={handleSubmitBaru} className="mb-4 flex gap-2">
        <input
          value={judulBaru}
          onChange={(e) => setJudulBaru(e.target.value)}
          placeholder="Tugas baru..."
          className="flex-1 rounded border px-2 py-1"
        />
        <select
          value={prioritasBaru}
          onChange={(e) => setPrioritasBaru(e.target.value)}
          className="rounded border px-2 py-1"
        >
          <option value="rendah">Rendah</option>
          <option value="normal">Normal</option>
          <option value="tinggi">Tinggi</option>
        </select>
        <button type="submit" className="rounded bg-blue-600 px-3 py-1 text-white">
          Tambah
        </button>
      </form>

      <ul className="space-y-2">
        {tasks.map((task) => (
          <li key={task.id} className="flex items-center justify-between gap-2 rounded border p-2">
            {editingId === task.id ? (
              <form onSubmit={(e) => simpanEdit(e, task.id)} className="flex flex-1 gap-2">
                <input
                  value={editJudul}
                  onChange={(e) => setEditJudul(e.target.value)}
                  className="flex-1 rounded border px-2 py-1"
                  autoFocus
                />
                <button type="submit" className="text-sm text-blue-600">Simpan</button>
                <button type="button" onClick={() => setEditingId(null)} className="text-sm text-slate-500">
                  Batal
                </button>
              </form>
            ) : (
              <>
                <label className="flex flex-1 items-center gap-2">
                  <input
                    type="checkbox"
                    checked={task.selesai}
                    onChange={() => toggleTask(task.id, task.selesai)}
                  />
                  <span className={task.selesai ? "line-through text-slate-400" : ""}>
                    {task.judul} — <span className="text-xs uppercase">{task.prioritas}</span>
                  </span>
                </label>
                <button onClick={() => mulaiEdit(task)} className="text-sm text-blue-600">Edit</button>
                <button onClick={() => hapusTask(task.id)} className="text-sm text-red-500">Hapus</button>
              </>
            )}
          </li>
        ))}
      </ul>
    </main>
  );
}
```

---

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
2. Tambah fitur "refresh manual" (tombol yang manggil ulang `fetchTasks()`). Petunjuk: `fetchTasks` sekarang ada di dalam `useEffect` — kamu perlu ngeluarin fungsinya supaya bisa dipanggil dari tombol juga.
3. Coba ganti dependency array `useEffect` fetch tadi jadi tanpa array sama sekali (`}, )` tanpa `[]`) — buka DevTools Network tab, lihat request-nya jadi berapa kali.

## Kalau Error

| Gejala | Biasanya penyebabnya |
|---|---|
| Halaman `Error: Failed to fetch` | `npm run api` belum jalan, atau port salah |
| Halaman "lompat ke atas"/reload tiap tambah/edit/hapus | `db.json` belum di-ignore Vite (Langkah 1d) atau `npm run dev` belum di-restart |
| `Rendered more hooks than during the previous render` | Ada `useState`/`useEffect` yang ditaruh SETELAH `if (loading) return ...` |
| Tugas baru gak muncul walau POST sukses | Lupa `setTasks([...tasks, newTask])` |
| Klik checkbox gak berpengaruh | `checked` dikasih nilai tapi lupa `onChange` |
| Request terus-terusan di Network tab | Effect tanpa array dependency, atau dependency berupa object baru tiap render |
| `Unexpected token '<' ... is not valid JSON` | URL salah — server balikin halaman HTML (404), bukan JSON |
| `db.json` gak berubah walau sukses | Yang jalan bukan json-server (cek terminal-nya), atau file `db.json` beda folder |

Lanjut ke [07-router-multi-halaman.md](07-router-multi-halaman.md).
