import { useState, useEffect } from "react";
import TaskForm from "./components/TaskForm.jsx";
import Navbar from "./components/Navbar.jsx";
import Card from "./components/Card.jsx";
import Footer from "./components/Footer.jsx";
import TaskCard from "./components/TaskCard.jsx";
import Counter from "./components/Counter.jsx";
import TodoList from "./components/TodoList.jsx";
// (Claude menghapus baris `import tasks from "./data/tasks.js"` di sini,
//  karena bentrok dengan `const [tasks, setTasks]` di bawah.)

// const api
const API_URL = "http://localhost:3001/tasks";

export default function App() {
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  // state buat form "Daftar Tugas (dari API)" -- WAJIB di atas early return (rules of hooks)
  const [judulBaru, setJudulBaru] = useState("");
  const [prioritasBaru, setPrioritasBaru] = useState("normal");
  const [editingId, setEditingId] = useState(null); // id task yang lagi diedit
  const [editJudul, setEditJudul] = useState("");
  // kondisi task
  const belumSelesai = tasks.filter((t) => !t.selesai);

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

  // task = object dari TaskForm: { judul, prioritas, deadline, penting }
  async function tambahTask(task) {
    const res = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ...task, selesai: false }),
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

  // submit form tambah: preventDefault biar halaman gak reload
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

  // PATCH judul task
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
    <>
      <Navbar />
      <main className="mx-auto max-w-3xl px-6 py-8">
        <h1 className="mb-6 text-2xl font-bold text-slate-900">
          Belajar JSX & Component
        </h1>
        <div className="grid gap-4 sm:grid-cols-2">
          <Card title="HTML/CSS/JS">
            <ul className="list-inside list-disc">
              <li>Sudah paham DOM manipulation</li>
              <li>Sudah paham event listener</li>
            </ul>
          </Card>

          <Card title="PHP + MySQL">
            <ul className="list-inside list-disc">
              <li>Sudah paham CRUD</li>
              <li>Sudah paham session & auth</li>
            </ul>
          </Card>
        </div>
        {/* task card with data tasks */}
        <h1 className="mb-4 text-2xl font-bold">Daftar Tugas</h1>
        {/* .map() ubah array data jadi array elemen JSX.
          `key` WAJIB, harus id yang stabil & unik -- BUKAN index array. */}
        <div className="grid gap-3">
          {tasks.map((task) => (
            <TaskCard
              key={task.id}
              judul={task.judul}
              prioritas={task.prioritas}
              selesai={task.selesai}
            />
          ))}
        </div>
        {belumSelesai.length > 0 && (
          <p className="mt-4 text-sm text-amber-600">
            Masih ada {belumSelesai.length} tugas yang belum selesai.
          </p>
        )}
        <p className="mt-2 text-sm text-slate-500">
          {tasks.length === 0
            ? "Belum ada tugas."
            : `Total: ${tasks.length} tugas`}
        </p>
        {/* counter and todolist */}
        <h1 className="text-2xl font-bold">State & Event</h1>
        <Counter />
        <TodoList />
        {/* task form */}
        <h1 className="text-2xl font-bold">Form Tugas</h1>
        <TaskForm onAddTask={tambahTask} />
        <ul className="space-y-1 text-sm">
          {tasks.map((t) => (
            <li key={t.id}>
              {t.judul} — {t.prioritas} {t.penting && "⭐"}
              {/* BARIS BARU (ditambah Claude): tampilkan tanggal deadline dari TaskForm.
                  Kalau task punya deadline, cetak " — 21/9/2026" setelah prioritas. */}
              {t.deadline &&
                ` — ${new Date(t.deadline).toLocaleDateString("id-ID")}`}
            </li>
          ))}
        </ul>

        {/* json server fetch */}
        <h1 className="mb-4 text-2xl font-bold">Daftar Tugas (dari API)</h1>

        {/* form tambah task (POST) */}
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
          <button
            type="submit"
            className="rounded bg-blue-600 px-3 py-1 text-white"
          >
            Tambah
          </button>
        </form>

        <ul className="space-y-2">
          {tasks.map((task) => (
            <li
              key={task.id}
              className="flex items-center justify-between gap-2 rounded border p-2"
            >
              {editingId === task.id ? (
                /* mode edit (PATCH judul) */
                <form
                  onSubmit={(e) => simpanEdit(e, task.id)}
                  className="flex flex-1 gap-2"
                >
                  <input
                    value={editJudul}
                    onChange={(e) => setEditJudul(e.target.value)}
                    className="flex-1 rounded border px-2 py-1"
                    autoFocus
                  />
                  <button type="submit" className="text-sm text-blue-600">
                    Simpan
                  </button>
                  <button
                    type="button"
                    onClick={() => setEditingId(null)}
                    className="text-sm text-slate-500"
                  >
                    Batal
                  </button>
                </form>
              ) : (
                <>
                  {/* checkbox -> toggleTask (PATCH selesai) */}
                  <label className="flex flex-1 items-center gap-2">
                    <input
                      type="checkbox"
                      checked={task.selesai}
                      onChange={() => toggleTask(task.id, task.selesai)}
                    />
                    <span
                      className={
                        task.selesai ? "line-through text-slate-400" : ""
                      }
                    >
                      {task.judul} —{" "}
                      <span className="text-xs uppercase">
                        {task.prioritas}
                      </span>
                    </span>
                  </label>
                  {/* tombol Edit -> masuk mode edit */}
                  <button
                    onClick={() => mulaiEdit(task)}
                    className="text-sm text-blue-600"
                  >
                    Edit
                  </button>
                  {/* tombol Hapus -> hapusTask (DELETE) */}
                  <button
                    onClick={() => hapusTask(task.id)}
                    className="text-sm text-red-500"
                  >
                    Hapus
                  </button>
                </>
              )}
            </li>
          ))}
        </ul>
      </main>
      <Footer />
    </>
  );
}
