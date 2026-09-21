# Sesi 9 — Project TaskFlow Lite

Tujuan: gabungin semua materi sesi 2-8 jadi satu aplikasi yang koheren. Ini **bukan** project portofolio/serius — itu nanti di stage Golang, pakai database beneran. Project ini murni buat ngelatih fundamental React kamu.

Kerjain ini juga di `learn-react/` — boleh timpa/rapihin ulang file dari sesi-sesi sebelumnya, ini memang titik di mana semuanya disatuin.

**Hasil akhir sesi ini:** aplikasi dengan 2 halaman:
- `/` → daftar project.
- `/projects/:id` → daftar tugas milik project itu, lengkap dengan tambah, toggle selesai, edit, hapus, cari, dan filter.

**Sebelum mulai (cek dulu, jangan lanjut kalau belum):**
- [ ] Sesi 6 selesai: `json-server` terpasang, script `npm run api` ada di `package.json`.
- [ ] Sesi 7 selesai: `react-router-dom` terpasang, `<BrowserRouter>` ada di `src/main.jsx`, ada `src/Layout.jsx`.
- [ ] Sesi 8 selesai: `src/hooks/useFetch.js` ada dan jalan.

## Cara Kerja Panduan Ini

Panduan ini ngasih **kode lengkap satu file sekaligus** — tinggal copy seluruh isinya ke file yang disebut, gak perlu nyisipin potongan satu-satu.

- **File:** path file, dihitung dari folder `learn-react/`. Contoh `src/pages/ProjectDetail.jsx` = folder `learn-react` → `src` → `pages` → file `ProjectDetail.jsx`.
- **Isi lengkap:** copy semuanya, **ganti seluruh isi** file lama (kalau file-nya udah ada) atau isi file baru.
- **Penanda `[A]`, `[B]`, ...** di dalam kode `ProjectDetail.jsx`: tiap fitur punya huruf sendiri. Di kode, semua bagian milik satu fitur ditandain huruf yang sama, jadi kamu bisa lihat **apa** yang ditambahin dan **di mana** (di bagian state, fungsi, atau tampilan).
- Setelah paste, di **Langkah 6** ada penjelasan per huruf.

Cara bikin file baru di VS Code: klik kanan folder tujuan di panel kiri → **New File** → ketik nama lengkap dengan ekstensi (misal `Button.jsx`).

**Fitur yang dibangun:**

| Huruf | Fitur | Yang dikirim ke server |
|---|---|---|
| **[A]** | Tambah tugas | `POST /tasks` |
| **[B]** | Toggle selesai (optimistic update + rollback) | `PATCH /tasks/:id` |
| **[C]** | Hapus dengan konfirmasi | `DELETE /tasks/:id` |
| **[D]** | Cari & filter (semua/aktif/selesai) | — (cuma ngatur tampilan) |
| **[E]** | Edit tugas pakai `Modal` | `PATCH /tasks/:id` |
| Latihan | Loading skeleton, sort | (kamu kerjain sendiri) |

**Peta langkah:**

| Langkah | Isi |
|---|---|
| 1 | `db.json` + `vite.config.js` |
| 2 | Struktur folder |
| 3 | Component reusable (`Button`, `EmptyState`, `Modal`, `Toast`) |
| 4 | Halaman daftar project |
| 5 | Halaman detail project (`ProjectDetail.jsx`), route, navbar |
| 6 | Bedah kode: penjelasan per fitur `[A]`–`[E]` |
| 7 | Tes alur lengkap |

---

## Langkah 1 — `db.json` dan `vite.config.js`

### 1a. `db.json`

**File:** `db.json` (di root `learn-react/`, sejajar `package.json`).

**Isi lengkap** (ganti seluruh isinya):

```json
{
  "projects": [
    { "id": 1, "nama": "Website Portofolio" },
    { "id": 2, "nama": "Belajar React" }
  ],
  "tasks": [
    { "id": 1, "projectId": 1, "judul": "Desain homepage", "prioritas": "tinggi", "deadline": "2026-01-10", "selesai": false },
    { "id": 2, "projectId": 1, "judul": "Setup hosting", "prioritas": "normal", "deadline": "2026-01-15", "selesai": false },
    { "id": 3, "projectId": 2, "judul": "Belajar hooks", "prioritas": "tinggi", "deadline": "2026-01-05", "selesai": true }
  ]
}
```

`projectId` = penghubung: tugas ini milik project nomor berapa. (Mirip foreign key di MySQL.)

### 1b. `vite.config.js`

`json-server` **nulis ulang** `db.json` tiap kamu tambah/edit/hapus data. Kalau `db.json` ada di dalam folder project Vite, Vite ngira itu perubahan kode dan **me-reload seluruh halaman** — hasilnya halaman "lompat ke atas" tiap kamu klik sesuatu. Suruh Vite ngabaikan file itu.

**File:** `vite.config.js` (root `learn-react/`).

**Isi lengkap** (ganti seluruh isinya; bagian `server` yang baru):

```js
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],
  // BARU: json-server nulis ke db.json tiap add/edit/hapus; tanpa ini Vite
  // nganggep itu perubahan kode dan reload seluruh halaman.
  server: {
    watch: { ignored: ["**/db.json"] },
  },
});
```

### 1c. Jalanin dua server

Buka **dua terminal** terpisah (di VS Code: tombol `+` di panel Terminal). Keduanya dijalanin dari folder `learn-react/`:

```
npm run dev     <- terminal 1, React di :5173
npm run api     <- terminal 2, fake REST API di :3001
```

Kalau `npm run dev` udah jalan dari tadi: matiin (`Ctrl+C`) lalu jalanin lagi — config Vite cuma kebaca saat startup.

### Cek

- [ ] Buka `http://localhost:3001/projects` → muncul 2 project (JSON).
- [ ] Buka `http://localhost:3001/tasks?projectId=1` → muncul 2 tugas milik project 1. Ini query yang bakal dipakai React.

---

## Langkah 2 — Struktur Folder

Setelah sesi ini, struktur `learn-react/` jadi begini. `<- BARU` = file yang kamu bikin/ganti di sesi ini (dibikinnya di langkah masing-masing).

```
learn-react/
  db.json                       <- diganti (langkah 1a)
  vite.config.js                <- diganti (langkah 1b)
  src/
    main.jsx                    (sudah ada)
    App.jsx                     <- diganti (langkah 5b)
    Layout.jsx                  (sudah ada, sesi 7)
    components/
      Navbar.jsx                <- diganti (langkah 5c)
      Button.jsx                <- BARU (langkah 3a)
      EmptyState.jsx            <- BARU (langkah 3b)
      Modal.jsx                 <- BARU (langkah 3c)
      Toast.jsx                 <- BARU (langkah 3d)
    hooks/
      useFetch.js               (sudah ada, sesi 8)
    pages/
      ProjectList.jsx           <- BARU (langkah 4)
      ProjectDetail.jsx         <- BARU (langkah 5a)
```

**Kenapa dipisah begini?**
- `components/` = potongan UI yang dipakai berulang & gak tau apa-apa soal data (tombol, modal).
- `pages/` = satu file per halaman/URL. Di sinilah data di-fetch dan state diatur.
- `hooks/` = logic yang dipakai ulang.

---

## Langkah 3 — Component Reusable Dulu

Bikin blok bangunan kecilnya dulu. Component ini "bodoh": cuma nerima props dan nampilin. Semuanya **file baru**.

### 3a. `Button`

**File baru:** `src/components/Button.jsx`

```jsx
export default function Button({ variant = "primary", children, ...props }) {
  const styles = {
    primary: "bg-blue-600 text-white",
    danger: "bg-red-600 text-white",
    ghost: "bg-transparent text-slate-600 border",
  };
  return (
    <button className={`rounded px-3 py-1 text-sm ${styles[variant]}`} {...props}>
      {children}
    </button>
  );
}
```

**Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `variant = "primary"` | Prop `variant`, default `"primary"` |
| `...props` | **Rest**: kumpulin SEMUA prop sisanya (`onClick`, `type`, `disabled`, dst) ke satu objek `props` |
| `{...props}` di `<button>` | **Spread**: sebar lagi semuanya ke `<button>`. Jadi `<Button onClick={...}>` tetap berfungsi tanpa `Button` perlu tau `onClick` |
| `styles[variant]` | Ambil class sesuai variant: `styles["danger"]` → `"bg-red-600 text-white"` |
| `` `rounded ... ${...}` `` | Template string buat gabungin class |

### 3b. `EmptyState`

**File baru:** `src/components/EmptyState.jsx`

```jsx
export default function EmptyState({ text }) {
  return <p className="py-8 text-center text-sm text-slate-400">{text}</p>;
}
```

### 3c. `Modal`

**File baru:** `src/components/Modal.jsx`

Perhatiin baris `import` paling atas — `Modal` pakai `Button`, jadi WAJIB di-import; kalau lupa, error `Button is not defined`.

```jsx
import Button from "./Button.jsx";

export default function Modal({ open, onClose, children }) {
  if (!open) return null;
  return (
    <div className="fixed inset-0 flex items-center justify-center bg-black/40">
      <div className="w-full max-w-sm rounded-lg bg-white p-6 shadow-lg">
        {children}
        <div className="mt-3">
          <Button variant="ghost" onClick={onClose}>Tutup</Button>
        </div>
      </div>
    </div>
  );
}
```

**Baca per bagian:**
- `if (!open) return null;` — `null` = "render nothing". Modal ada di kode, tapi gak muncul di layar kalau `open` false.
- `fixed inset-0` — nutupin seluruh layar; `bg-black/40` — latar hitam transparan 40%.
- `{children}` — isi modal ditentuin pemanggil.

### 3d. `Toast`

Notifikasi kecil yang ilang sendiri setelah 2 detik.

**File baru:** `src/components/Toast.jsx`

```jsx
import { useEffect } from "react";

export default function Toast({ message, onDone }) {
  useEffect(() => {
    if (!message) return; // gak ada pesan = gak usah pasang timer
    const timer = setTimeout(onDone, 2000);
    return () => clearTimeout(timer); // cleanup -- penting biar gak numpuk timer
  }, [message, onDone]);

  if (!message) return null;
  return (
    <div className="fixed bottom-4 right-4 rounded bg-slate-900 px-4 py-2 text-white">
      {message}
    </div>
  );
}
```

**Kenapa `onDone` ada di `[message, onDone]`?** Aturan lint: semua nilai dari luar yang dipakai effect harus masuk daftar. Konsekuensinya: kalau pemanggil ngirim fungsi *baru* tiap render (contoh `onDone={() => setToastMsg("")}`), effect jalan ulang tiap render dan timernya ke-reset terus. Makanya nanti di `ProjectDetail` fungsinya dibungkus `useCallback`. Ini salah satu kasus `useCallback` yang beneran perlu.

### Cek

- [ ] 4 file ada di `src/components/`: `Button.jsx`, `EmptyState.jsx`, `Modal.jsx`, `Toast.jsx`.
- [ ] `Modal.jsx` punya `import Button from "./Button.jsx";` di baris paling atas.
- [ ] Belum ada yang tampil di browser — normal (belum dipakai).

---

## Langkah 4 — Halaman Daftar Project

**File baru:** `src/pages/ProjectList.jsx`

```jsx
import { Link } from "react-router-dom";
import useFetch from "../hooks/useFetch.js";
import EmptyState from "../components/EmptyState.jsx";

export default function ProjectList() {
  const { data: projects, loading, error } = useFetch("http://localhost:3001/projects");

  if (loading) return <p className="p-6">Loading...</p>;
  if (error) return <p className="p-6 text-red-500">Error: {error}</p>;
  if (projects.length === 0) return <EmptyState text="Belum ada project." />;

  return (
    <div className="p-6">
      <h1 className="mb-4 text-2xl font-bold">Project Saya</h1>
      <ul className="space-y-2">
        {projects.map((p) => (
          <li key={p.id}>
            <Link to={`/projects/${p.id}`} className="text-blue-600 underline">{p.nama}</Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
```

**Baca per bagian:**
- `useFetch(url)` dari sesi 8 ngasih 3 status: `loading`, `error`, `data`. Urutan `if`-nya penting: cek `loading` dulu, lalu `error`, baru `data` aman dipakai.
- `` `/projects/${p.id}` `` — bikin URL dinamis per project: `/projects/1`, `/projects/2`.

**Belum bisa dicek di browser** — routenya baru disambung di langkah 5b.

---

## Langkah 5 — Halaman Detail Project, Route, Navbar

### 5a. `ProjectDetail.jsx` (kode lengkap)

Ini file paling besar. Kodenya di bawah **langsung lengkap** dengan semua fitur. Setiap bagian ditandain huruf `[A]`–`[E]` sesuai tabel fitur di atas, dan ditandain juga **di bagian mana** ia berada (`IMPORT`, `STATE`, `FUNGSI`, `TAMPILAN`).

**File baru:** `src/pages/ProjectDetail.jsx`

```jsx
import { useState, useEffect, useCallback } from "react";
import { useParams } from "react-router-dom";
import Button from "../components/Button.jsx";            // [A][C][E] IMPORT: dipakai tombol
import Modal from "../components/Modal.jsx";              // [E] IMPORT: jendela edit
import Toast from "../components/Toast.jsx";
import EmptyState from "../components/EmptyState.jsx";    // [D] IMPORT: pesan "gak ada tugas yang cocok"

const API = "http://localhost:3001/tasks";

export default function ProjectDetail() {
  const { id } = useParams(); // ambil ":id" dari URL /projects/:id

  // ───────────── STATE (semua useState di sini, di ATAS if (loading)) ─────────────
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [toastMsg, setToastMsg] = useState("");
  const [judulBaru, setJudulBaru] = useState("");            // [A] STATE: isi kolom "Tugas baru..."
  const [filter, setFilter] = useState("semua");             // [D] STATE: tombol filter yang aktif
  const [search, setSearch] = useState("");                  // [D] STATE: isi kolom "Cari tugas..."
  const [editing, setEditing] = useState(null);              // [E] STATE: tugas yang lagi diedit (null = tidak ada)
  const [editJudul, setEditJudul] = useState("");            // [E] STATE: isi kolom judul di modal
  const [editDeadline, setEditDeadline] = useState("");      // [E] STATE: isi kolom tanggal di modal

  // fungsi stabil buat Toast (biar timer Toast gak ke-reset tiap render)
  const tutupToast = useCallback(() => setToastMsg(""), []);

  // ───────────── AMBIL DATA (sekali, dan tiap `id` di URL berubah) ─────────────
  useEffect(() => {
    async function load() {
      try {
        setLoading(true);
        const res = await fetch(`${API}?projectId=${id}`);
        if (!res.ok) throw new Error("Gagal ambil data");
        setTasks(await res.json());
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [id]);

  // ───────────── FUNGSI (semua di BAWAH useEffect, di ATAS if (loading)) ─────────────

  // [A] FUNGSI: tambah tugas (POST)
  async function tambahTask(e) {
    e.preventDefault(); // cegah form reload halaman
    if (!judulBaru.trim()) return;
    const res = await fetch(API, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        projectId: Number(id),
        judul: judulBaru,
        prioritas: "normal",
        selesai: false,
        deadline: "",
      }),
    });
    const newTask = await res.json(); // server balikin data + id baru
    setTasks([...tasks, newTask]);    // update state lokal juga
    setJudulBaru("");
    setToastMsg("Tugas ditambahkan");
  }

  // [B] FUNGSI: toggle selesai (PATCH), optimistic update
  async function toggleTask(taskId, selesaiSekarang) {
    const snapshot = tasks; // simpan buat rollback kalau gagal
    // OPTIMISTIC UPDATE: ubah tampilan DULUAN, baru kirim request
    setTasks(tasks.map((t) => (t.id === taskId ? { ...t, selesai: !selesaiSekarang } : t)));
    try {
      const res = await fetch(`${API}/${taskId}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ selesai: !selesaiSekarang }),
      });
      if (!res.ok) throw new Error();
    } catch {
      setTasks(snapshot); // ROLLBACK -- balikin ke keadaan sebelum diubah
      setToastMsg("Gagal update, dibatalkan");
    }
  }

  // [C] FUNGSI: hapus tugas (DELETE)
  async function hapusTask(taskId) {
    if (!confirm("Yakin hapus tugas ini?")) return;
    await fetch(`${API}/${taskId}`, { method: "DELETE" });
    setTasks(tasks.filter((t) => t.id !== taskId));
    setToastMsg("Tugas dihapus");
  }

  // [E] FUNGSI: buka modal edit, isi kolomnya dengan data tugas
  function mulaiEdit(task) {
    setEditing(task);
    setEditJudul(task.judul);
    setEditDeadline(task.deadline || "");
  }

  // [E] FUNGSI: simpan hasil edit (PATCH)
  async function simpanEdit(e) {
    e.preventDefault();
    if (!editJudul.trim()) return;
    await fetch(`${API}/${editing.id}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ judul: editJudul, deadline: editDeadline }),
    });
    setTasks(
      tasks.map((t) =>
        t.id === editing.id ? { ...t, judul: editJudul, deadline: editDeadline } : t
      )
    );
    setEditing(null); // tutup modal
    setToastMsg("Tugas diperbarui");
  }

  // [D] DATA TURUNAN: dihitung tiap render dari tasks + filter + search.
  // Gak disimpan di useState sendiri (sesi 4).
  const visibleTasks = tasks
    .filter((t) => {
      if (filter === "aktif") return !t.selesai;
      if (filter === "selesai") return t.selesai;
      return true;
    })
    .filter((t) => t.judul.toLowerCase().includes(search.toLowerCase()));

  // ───────────── EARLY RETURN (loading / error) ─────────────
  if (loading) return <p className="p-6">Loading...</p>;
  if (error) return <p className="p-6 text-red-500">Error: {error}</p>;

  // ───────────── TAMPILAN ─────────────
  return (
    <div className="mx-auto max-w-lg p-6">
      <h1 className="mb-4 text-xl font-bold">Tugas Project #{id}</h1>

      {/* [A] TAMPILAN: form tambah tugas */}
      <form onSubmit={tambahTask} className="mb-4 flex gap-2">
        <input
          value={judulBaru}
          onChange={(e) => setJudulBaru(e.target.value)}
          placeholder="Tugas baru..."
          className="flex-1 rounded border px-2 py-1"
        />
        <Button type="submit">Tambah</Button>
      </form>

      {/* [D] TAMPILAN: kolom cari */}
      <input
        value={search}
        onChange={(e) => setSearch(e.target.value)}
        placeholder="Cari tugas..."
        className="mb-2 w-full rounded border px-2 py-1"
      />

      {/* [D] TAMPILAN: tombol filter */}
      <div className="mb-4 flex gap-2 text-sm">
        {["semua", "aktif", "selesai"].map((f) => (
          <button
            key={f}
            onClick={() => setFilter(f)}
            className={filter === f ? "font-bold underline" : "text-slate-500"}
          >
            {f}
          </button>
        ))}
      </div>

      {/* [D] TAMPILAN: kalau hasil filter kosong, tampilkan pesan; kalau ada, tampilkan list */}
      {visibleTasks.length === 0 ? (
        <EmptyState text="Gak ada tugas yang cocok." />
      ) : (
        <ul className="space-y-2">
          {visibleTasks.map((task) => (
            <li key={task.id} className="flex items-center justify-between gap-2 rounded border p-2">
              {/* [B] TAMPILAN: checkbox toggle + judul (dicoret kalau selesai) */}
              <label className="flex flex-1 items-center gap-2">
                <input
                  type="checkbox"
                  checked={task.selesai}
                  onChange={() => toggleTask(task.id, task.selesai)}
                />
                <span className={task.selesai ? "line-through text-slate-400" : ""}>
                  {task.judul}
                </span>
                {/* [E] TAMPILAN: deadline kalau ada */}
                {task.deadline && (
                  <span className="text-xs text-slate-500">{task.deadline}</span>
                )}
              </label>
              {/* [E] TAMPILAN: tombol Edit */}
              <Button variant="ghost" onClick={() => mulaiEdit(task)}>Edit</Button>
              {/* [C] TAMPILAN: tombol Hapus */}
              <Button variant="danger" onClick={() => hapusTask(task.id)}>Hapus</Button>
            </li>
          ))}
        </ul>
      )}

      {/* [E] TAMPILAN: modal edit (muncul kalau `editing` bukan null) */}
      <Modal open={editing !== null} onClose={() => setEditing(null)}>
        <form onSubmit={simpanEdit} className="space-y-3">
          <h2 className="font-bold">Edit Tugas</h2>
          <input
            value={editJudul}
            onChange={(e) => setEditJudul(e.target.value)}
            className="w-full rounded border px-2 py-1"
          />
          <input
            type="date"
            value={editDeadline}
            onChange={(e) => setEditDeadline(e.target.value)}
            className="w-full rounded border px-2 py-1"
          />
          <Button type="submit">Simpan</Button>
        </form>
      </Modal>

      <Toast message={toastMsg} onDone={tutupToast} />
    </div>
  );
}
```

**Aturan urutan di file ini (hafalin, ini sumber error paling sering):**

```
1. IMPORT
2. const API
3. function ProjectDetail() {
4.    STATE          <- semua useState
5.    tutupToast, useEffect
6.    FUNGSI         <- tambahTask, toggleTask, hapusTask, mulaiEdit, simpanEdit, visibleTasks
7.    if (loading) return ...   <- early return
8.    return ( TAMPILAN )
   }
```

Semua hook dan fungsi harus **di atas** `if (loading) return ...`. Kalau ada `useState` yang ditaruh di bawahnya, muncul error `Rendered more hooks than during the previous render`.

### 5b. Sambungin route di `App.jsx`

**File:** `src/App.jsx`

**Versi 1 — cuma halaman project** (kalau kamu gak butuh login lagi). **Isi lengkap**, ganti seluruh isinya:

```jsx
import { Routes, Route } from "react-router-dom";
import Layout from "./Layout.jsx";
import ProjectList from "./pages/ProjectList.jsx";
import ProjectDetail from "./pages/ProjectDetail.jsx";

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<ProjectList />} />
        <Route path="projects/:id" element={<ProjectDetail />} />
      </Route>
    </Routes>
  );
}
```

**Versi 2 — tetap punya login dari sesi 8** (halaman project ikut dijaga `ProtectedRoute`). **Isi lengkap**, ganti seluruh isinya:

```jsx
import { Routes, Route, Navigate } from "react-router-dom";
import { useAuth } from "./context/AuthContext.jsx";
import Layout from "./Layout.jsx";
import ProjectList from "./pages/ProjectList.jsx";
import ProjectDetail from "./pages/ProjectDetail.jsx";
import Login from "./pages/Login.jsx";
import Dashboard from "./pages/Dashboard.jsx";

// Ditulis DI LUAR App (aturan sesi 8)
function ProtectedRoute({ children }) {
  const { user } = useAuth();
  if (!user) return <Navigate to="/login" />;
  return children;
}

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route
          index
          element={
            <ProtectedRoute>
              <ProjectList />
            </ProtectedRoute>
          }
        />
        <Route
          path="projects/:id"
          element={
            <ProtectedRoute>
              <ProjectDetail />
            </ProtectedRoute>
          }
        />
        <Route path="login" element={<Login />} />
        <Route
          path="dashboard"
          element={
            <ProtectedRoute>
              <Dashboard />
            </ProtectedRoute>
          }
        />
      </Route>
    </Routes>
  );
}
```

Pilih **salah satu** versi. Kalau ragu, pakai Versi 1.

### 5c. Navbar

Route `/tasks` udah gak ada di `App.jsx` versi ini, jadi link "Tugas" bakal nyasar. Ganti navbar supaya nunjuk ke halaman project.

**File:** `src/components/Navbar.jsx`

**Isi lengkap** (ganti seluruh isinya; ini navbar dari sesi 8 dengan link diganti jadi "Project"):

```jsx
import { Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

export default function Navbar() {
  const { user, logout } = useAuth();

  return (
    <nav className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
      <span className="text-lg font-bold">Portofolio Vanya</span>
      <ul className="flex items-center gap-4 text-sm">
        <li>
          <Link to="/">Project</Link>
        </li>
        <li>
          {user ? (
            <span className="flex items-center gap-4">
              <span>Halo, {user.nama}</span>
              <button onClick={logout} className="text-red-500 hover:text-red-400">
                Logout
              </button>
            </span>
          ) : (
            <Link to="/login">Login</Link>
          )}
        </li>
      </ul>
    </nav>
  );
}
```

**Kalau kamu belum bikin `AuthContext` (sesi 8) atau pakai Versi 1 di 5b tanpa login:** hapus baris `import { useAuth }` dan `const { user, logout } = useAuth();`, dan ganti seluruh `<li>{user ? ... }</li>` (blok kedua) dengan tidak ada apa-apa. Sisakan cuma `<li><Link to="/">Project</Link></li>`.

### Cek langkah 5

- [ ] Buka `http://localhost:5173/` → muncul "Project Saya" dengan 2 link project. (Kalau pakai Versi 2, login dulu lewat `/login`.)
- [ ] Klik "Website Portofolio" → URL jadi `/projects/1`, muncul "Tugas Project #1" dengan 2 tugas, form tambah, kolom cari, tombol filter.
- [ ] Buka `/projects/2` → 1 tugas.
- [ ] Klik "Project" di navbar → balik ke daftar project.
- [ ] Matiin `npm run api`, refresh `/projects/1` → muncul `Error: ...`, bukan halaman blank. (Nyalain lagi setelah tes.)

---

## Langkah 6 — Bedah Kode: Penjelasan per Fitur

Kodenya udah jadi di langkah 5a. Sekarang kita bedah satu fitur per kali. Untuk tiap huruf: **cari penanda `[X]` di file-mu**, baca bagian itu, lalu tes di browser.

### [A] Tambah Tugas — POST

**Ada di 2 tempat:** STATE (`judulBaru`) dan FUNGSI (`tambahTask`) dan TAMPILAN (form di atas list).

| Bagian | Apa yang dikerjakan |
|---|---|
| STATE `judulBaru` | Nyimpen teks yang lagi diketik di kolom "Tugas baru...". Awalnya `""` |
| TAMPILAN `<input value={judulBaru} onChange={...}>` | Kolom input "dikontrol" state (sesi 5): tiap ketik, `judulBaru` ikut update |
| TAMPILAN `<form onSubmit={tambahTask}>` | Submit form (klik Tambah atau tekan Enter) → panggil `tambahTask` |
| FUNGSI `tambahTask` | Kirim data ke server, terima balasan, masukin ke list |

**Baca isi `tambahTask`:**
- `e.preventDefault()` — cegah halaman reload (sesi 5).
- `Number(id)` — `id` dari URL itu string (`"1"`), tapi di `db.json` `projectId` angka. Ubah biar tipenya sama.
- `headers: { "Content-Type": "application/json" }` — kasih tau server bahwa body-nya JSON.
- `JSON.stringify(...)` — ubah objek jadi teks JSON (body request cuma bisa teks).
- `const newTask = await res.json()` — pakai balasan server (bukan bikin objek sendiri) supaya `id` yang dipakai adalah id asli dari server.
- `[...tasks, newTask]` — array baru (immutable, sesi 4).

**Cek [A]:**
- [ ] Ketik tugas → Tambah (atau Enter) → muncul di list, toast "Tugas ditambahkan" hilang sendiri setelah ±2 detik.
- [ ] Buka `http://localhost:3001/tasks` di tab lain → tugas baru ada di sana. Refresh halaman React → tetap ada.
- [ ] Halaman **gak** reload/lompat pas submit (kalau iya, cek langkah 1b dan restart `npm run dev`).

### [B] Toggle Selesai — PATCH + Optimistic Update

**Ada di 2 tempat:** FUNGSI (`toggleTask`) dan TAMPILAN (checkbox di `<li>`).

**Kenapa "optimistic"?** Sesi 6: kamu nunggu server jawab dulu baru update tampilan → ada jeda. Di sini kebalikan: **update tampilan duluan** (optimis: "pasti berhasil"), kirim request di belakang. Kalau ternyata gagal, kembalikan (`snapshot`) dan kasih tau user. Hasilnya UI terasa instan.

**Baca isi `toggleTask`:**
- `const snapshot = tasks;` — foto keadaan sebelum diubah. Aman karena `setTasks(tasks.map(...))` bikin array **baru**, `snapshot` tetap nunjuk array lama.
- `setTasks(tasks.map(...))` — ubah tampilan duluan.
- `fetch(... "PATCH" ...)` — kirim perubahan ke server. `PATCH` = ubah sebagian field (di sini cuma `selesai`).
- `if (!res.ok) throw new Error();` — `fetch` gak error sendiri buat status 404/500; harus dicek manual.
- `catch { setTasks(snapshot); ... }` — kalau gagal, balikin.

**Cek [B]:**
- [ ] Klik checkbox → tugas langsung ke-coret. Refresh → status tetap (tersimpan).
- [ ] **Tes rollback:** matiin `npm run api`, klik checkbox → tugas sempat ke-coret, lalu balik lagi + toast "Gagal update, dibatalkan". Nyalain API lagi.

### [C] Hapus dengan Konfirmasi — DELETE

**Ada di 2 tempat:** FUNGSI (`hapusTask`) dan TAMPILAN (tombol Hapus merah).

**Baca isi `hapusTask`:**
- `confirm(...)` = dialog bawaan browser (OK/Cancel). Cancel → `return` → gak ada yang dihapus.
- `.filter((t) => t.id !== taskId)` — array baru tanpa tugas itu (immutable).

**Cek [C]:**
- [ ] Klik Hapus → muncul dialog. Cancel → gak terjadi apa-apa. OK → tugas hilang, toast "Tugas dihapus".
- [ ] Cek `localhost:3001/tasks` → tugas itu beneran ilang.

### [D] Cari & Filter — Derived State

**Ada di 3 tempat:** STATE (`filter`, `search`), DATA TURUNAN (`visibleTasks`), dan TAMPILAN (kolom cari, tombol filter, pesan kosong).

**Kenapa `visibleTasks` bukan `useState`?** Karena bisa dihitung dari `tasks` + `filter` + `search` (sesi 4: derived state). Kalau disimpan sendiri, tiap kamu tambah/hapus/toggle kamu harus inget update dua state — dan pasti suatu saat lupa.

**Baca isi `visibleTasks`:**
- Dua `.filter()` dirantai: yang pertama nyaring status (`aktif`/`selesai`/`semua`), yang kedua nyaring teks.
- `.toLowerCase()` di dua sisi biar pencarian gak peka huruf besar-kecil.
- `.includes(...)` — teks mengandung potongan itu. String kosong cocok ke semuanya, jadi search kosong = tampilkan semua.
- Data asli (`tasks`) **tidak diubah**. Filter cuma mengatur apa yang ditampilkan.
- Di TAMPILAN, list-nya dirender dari `visibleTasks.map`, **bukan** `tasks.map`.

**Cek [D]:**
- [ ] Ketik sebagian judul di "Cari tugas..." → list menyusut.
- [ ] Klik "aktif" → cuma yang belum selesai. "selesai" → cuma yang selesai. "semua" → semua.
- [ ] Cari kata yang gak ada → muncul "Gak ada tugas yang cocok.".

### [E] Edit Tugas lewat Modal — PATCH

**Ada di 4 tempat:** STATE (`editing`, `editJudul`, `editDeadline`), FUNGSI (`mulaiEdit`, `simpanEdit`), dan TAMPILAN (tombol Edit, deadline di list, `<Modal>`).

**Cara kerjanya:**
1. Klik **Edit** → `mulaiEdit(task)` jalan: `editing` diisi tugas itu, dan kolom modal diisi judul + tanggal lama.
2. `<Modal open={editing !== null}>` — modal terbuka selama `editing` bukan `null`.
3. Klik **Simpan** → `simpanEdit` kirim PATCH, update list, lalu `setEditing(null)` nutup modal.
4. Klik **Tutup** → `setEditing(null)` doang, gak ada yang berubah.

Field di modal dikontrol state **terpisah** (`editJudul`, `editDeadline`), bukan langsung `task.judul`. Makanya ngetik di modal gak langsung ngubah list sebelum kamu klik Simpan.

**Cek [E]:**
- [ ] Tiap tugas punya tombol **Edit** (abu-abu, di kiri tombol Hapus).
- [ ] Klik Edit → modal muncul dengan judul & tanggal terisi.
- [ ] Ubah judul + tanggal → Simpan → list berubah (tanggal muncul di samping judul), modal nutup, toast "Tugas diperbarui". Refresh → tetap.
- [ ] Klik Edit lalu **Tutup** → gak ada perubahan.

### Kalau ada yang error di halaman ini

| Gejala | Biasanya penyebabnya |
|---|---|
| `Button is not defined` / `Modal is not defined` / `EmptyState is not defined` | Ada baris `import` di bagian atas `ProjectDetail.jsx` yang kehapus — cocokin 4 import bertanda `IMPORT` |
| `Rendered more hooks than during the previous render` | Ada `useState`/`useEffect` yang ditaruh SETELAH `if (loading) return ...` |
| `tambahTask is not defined` (atau fungsi lain) | Fungsinya kehapus, atau ada di luar `function ProjectDetail() { ... }` |
| Tugas dari project lain ikut muncul | `?projectId=${id}` di URL fetch kehapus |
| Tugas baru muncul di project yang salah | `projectId: Number(id)` di body POST kehapus |
| Halaman lompat/reload tiap klik | Vite belum ngabaikan `db.json` (Langkah 1b), atau belum restart `npm run dev` |
| Toast gak pernah hilang / hilang telat | `onDone` dikirim sebagai fungsi baru tiap render — pakai `tutupToast` (`useCallback`) |
| Klik Edit, modal kosong | `mulaiEdit` gak nyalin nilai ke `editJudul`/`editDeadline` |
| Kode error tapi gak ketemu salahnya | **Samain seluruh file-mu dengan kode di 5a**, baris demi baris |

---

## Langkah 7 — Tes Alur Lengkap

Jalanin skenario ini dari awal:

| # | Kamu lakuin | Harus terjadi |
|---|---|---|
| 1 | Buka `/` | 2 project tampil |
| 2 | Klik "Website Portofolio" | 2 tugas tampil |
| 3 | Tambah tugas "Tes" | Muncul di list + toast |
| 4 | Centang tugas "Tes" | Ke-coret |
| 5 | Filter "aktif" | "Tes" hilang dari list (karena udah selesai) |
| 6 | Filter "selesai" | Cuma "Tes" |
| 7 | Edit "Tes" → ganti judul + isi tanggal | Judul & tanggal berubah |
| 8 | Hapus "Tes" (OK) | Hilang + toast |
| 9 | Kembali ke `/`, klik "Belajar React" | Cuma 1 tugas (project berbeda, data terpisah) |
| 10 | Refresh di tiap langkah | Data konsisten dengan `localhost:3001` |

## Checkpoint Closed-Book (Kecil)

Tambahin **1 field baru** end-to-end, tanpa bantuan:
1. Field `prioritas` bisa diedit dari form (tambah `<select>` di form tambah task).
2. Data prioritas kekirim ke json-server (POST body — gantiin `prioritas: "normal"` yang hardcode).
3. Prioritas ditampilkan di list task (badge kecil).

**Strategi:** (1) mulai dari state — perlu state `prioritasBaru` (taruh di bagian STATE). (2) tambah `<select>` di form TAMPILAN `[A]`. (3) pakai `prioritasBaru` di body `tambahTask` `[A]`. (4) tampilkan `task.prioritas` di `<li>`.

## Latihan Tambahan (Kalau Masih Ada Waktu)

- **Loading skeleton:** ganti `<p>Loading...</p>` jadi beberapa `<div className="animate-pulse ...">` yang bentuknya mirip card asli.
- **Sort:** dropdown urut berdasarkan deadline atau prioritas. Petunjuk: bikin state `sortBy`, lalu tambahin `.sort(...)` di rantai `visibleTasks` — tapi ingat `.sort()` **mengubah array aslinya**, jadi salin dulu (`[...hasilFilter].sort(...)`) — ini persis konsep immutability sesi 4.
- **Tambah project baru** dari halaman `ProjectList` (form + POST ke `/projects`).
- Bonus opsional: `npm run build`, terus deploy hasil build ke **Vercel** (project ini masih pakai `json-server`/data lokal doang, jadi cuma buat lihat hasilnya online — bukan deploy backend beneran; itu nanti di akhir banget, sesudah project Golang).

Lanjut ke [10-laravel-connect-demo.md](10-laravel-connect-demo.md).
