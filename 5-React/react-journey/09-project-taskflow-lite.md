# Sesi 9 — Project TaskFlow Lite

Tujuan: gabungin semua materi sesi 2-8 jadi satu aplikasi yang koheren. Ini **bukan** project portofolio/serius — itu nanti di stage Golang, pakai database beneran. Project ini murni buat ngelatih fundamental React kamu.

Kerjain ini juga di `learn-react/` — boleh timpa/rapihin ulang file dari sesi-sesi sebelumnya, ini memang titik di mana semuanya disatuin.

## Fitur yang Dibangun

- Halaman daftar project → klik project → halaman detail berisi task-task di project itu.
- CRUD lengkap buat task: tambah, edit, hapus, toggle status selesai, prioritas, deadline.
- Search, filter (semua/aktif/selesai), sort (by deadline/prioritas).
- Component reusable: `Modal`, `Button`, `EmptyState`, `Toast`.
- Loading skeleton, konfirmasi sebelum hapus.
- Optimistic update: pas toggle status, tampilan berubah LANGSUNG (sebelum API confirm), rollback kalau request gagal.

## Langkah 1 — Rapihin `db.json`

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

Jalanin `npm run api` (json-server) + `npm run dev` di dua terminal, seperti sesi 6.

## Langkah 2 — Struktur Folder

```
src/
  components/
    Button.jsx
    Modal.jsx
    EmptyState.jsx
    Toast.jsx
    TaskCard.jsx
  hooks/
    useFetch.js          <- dari sesi 8
  pages/
    ProjectList.jsx
    ProjectDetail.jsx
  Layout.jsx              <- dari sesi 7
  App.jsx
```

## Langkah 3 — Component Reusable Dulu

`src/components/Button.jsx`:
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

`src/components/EmptyState.jsx`:
```jsx
export default function EmptyState({ text }) {
  return <p className="py-8 text-center text-sm text-slate-400">{text}</p>;
}
```

`src/components/Modal.jsx`:
```jsx
export default function Modal({ open, onClose, children }) {
  if (!open) return null;
  return (
    <div className="fixed inset-0 flex items-center justify-center bg-black/40">
      <div className="rounded-lg bg-white p-6 shadow-lg">
        {children}
        <Button variant="ghost" onClick={onClose}>Tutup</Button>
      </div>
    </div>
  );
}
```

`src/components/Toast.jsx`:
```jsx
import { useEffect } from "react";

export default function Toast({ message, onDone }) {
  useEffect(() => {
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

## Langkah 4 — Halaman Daftar Project

`src/pages/ProjectList.jsx`:
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

## Langkah 5 — Halaman Detail Project + CRUD Task

`src/pages/ProjectDetail.jsx` — ini bagian paling banyak, gabungin semua konsep sebelumnya:

```jsx
import { useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import Button from "../components/Button.jsx";
import Toast from "../components/Toast.jsx";
import EmptyState from "../components/EmptyState.jsx";

const API = "http://localhost:3001/tasks";

export default function ProjectDetail() {
  const { id } = useParams();
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState("semua");
  const [search, setSearch] = useState("");
  const [toastMsg, setToastMsg] = useState("");
  const [judulBaru, setJudulBaru] = useState("");

  useEffect(() => {
    async function load() {
      setLoading(true);
      const res = await fetch(`${API}?projectId=${id}`);
      setTasks(await res.json());
      setLoading(false);
    }
    load();
  }, [id]);

  async function tambahTask() {
    if (!judulBaru.trim()) return;
    const res = await fetch(API, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ projectId: Number(id), judul: judulBaru, prioritas: "normal", selesai: false, deadline: "" }),
    });
    const newTask = await res.json();
    setTasks([...tasks, newTask]);
    setJudulBaru("");
    setToastMsg("Tugas ditambahkan");
  }

  async function toggleTask(taskId, selesaiSekarang) {
    // OPTIMISTIC UPDATE: ubah tampilan DULUAN, baru kirim request.
    const snapshot = tasks; // simpan buat rollback kalau gagal
    setTasks(tasks.map((t) => (t.id === taskId ? { ...t, selesai: !selesaiSekarang } : t)));

    try {
      const res = await fetch(`${API}/${taskId}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ selesai: !selesaiSekarang }),
      });
      if (!res.ok) throw new Error();
    } catch {
      setTasks(snapshot); // ROLLBACK -- balikin ke keadaan sebelum optimistic update
      setToastMsg("Gagal update, dibatalkan");
    }
  }

  async function hapusTask(taskId) {
    if (!confirm("Yakin hapus tugas ini?")) return;
    await fetch(`${API}/${taskId}`, { method: "DELETE" });
    setTasks(tasks.filter((t) => t.id !== taskId));
    setToastMsg("Tugas dihapus");
  }

  const visibleTasks = tasks
    .filter((t) => {
      if (filter === "aktif") return !t.selesai;
      if (filter === "selesai") return t.selesai;
      return true;
    })
    .filter((t) => t.judul.toLowerCase().includes(search.toLowerCase()));

  if (loading) return <p className="p-6">Loading...</p>;

  return (
    <div className="mx-auto max-w-lg p-6">
      <h1 className="mb-4 text-xl font-bold">Tugas Project #{id}</h1>

      <div className="mb-4 flex gap-2">
        <input value={judulBaru} onChange={(e) => setJudulBaru(e.target.value)} placeholder="Tugas baru..." className="flex-1 rounded border px-2 py-1" />
        <Button onClick={tambahTask}>Tambah</Button>
      </div>

      <input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Cari tugas..." className="mb-2 w-full rounded border px-2 py-1" />

      <div className="mb-4 flex gap-2 text-sm">
        {["semua", "aktif", "selesai"].map((f) => (
          <button key={f} onClick={() => setFilter(f)} className={filter === f ? "font-bold underline" : "text-slate-500"}>{f}</button>
        ))}
      </div>

      {visibleTasks.length === 0 ? (
        <EmptyState text="Gak ada tugas yang cocok." />
      ) : (
        <ul className="space-y-2">
          {visibleTasks.map((task) => (
            <li key={task.id} className="flex items-center justify-between rounded border p-2">
              <span onClick={() => toggleTask(task.id, task.selesai)} className={task.selesai ? "cursor-pointer line-through text-slate-400" : "cursor-pointer"}>
                {task.judul}
              </span>
              <Button variant="danger" onClick={() => hapusTask(task.id)}>Hapus</Button>
            </li>
          ))}
        </ul>
      )}

      <Toast message={toastMsg} onDone={() => setToastMsg("")} />
    </div>
  );
}
```

## Langkah 6 — Sambungin Route

`src/App.jsx`:
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

## Checkpoint Closed-Book (Kecil)

Tambahin **1 field baru** end-to-end, tanpa bantuan:
1. Field `prioritas` bisa diedit dari form (tambah `<select>` di form tambah task).
2. Data prioritas kekirim ke json-server (POST body).
3. Prioritas ditampilkan di list task (badge kecil).

## Latihan Tambahan (Kalau Masih Ada Waktu)

- Loading skeleton: ganti `<p>Loading...</p>` jadi beberapa `<div className="animate-pulse ...">` yang bentuknya mirip card asli.
- Sort task by deadline atau prioritas (dropdown sort).
- Bonus opsional: `npm run build`, terus deploy hasil build ke **Vercel** (project ini masih pakai `json-server`/data lokal doang, jadi cuma buat lihat hasilnya online — bukan deploy backend beneran; itu nanti di akhir banget, sesudah project Golang).

Lanjut ke [10-laravel-connect-demo.md](10-laravel-connect-demo.md).
