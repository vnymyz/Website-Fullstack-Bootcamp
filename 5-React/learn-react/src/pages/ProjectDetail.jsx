import { useState, useEffect, useCallback } from "react";
import { useParams } from "react-router-dom";
import Button from "../components/Button.jsx"; // [A][C][E] IMPORT: dipakai tombol
import Modal from "../components/Modal.jsx"; // [E] IMPORT: jendela edit
import Toast from "../components/Toast.jsx";
import EmptyState from "../components/EmptyState.jsx"; // [D] IMPORT: pesan "gak ada tugas yang cocok"

const API = "http://localhost:3001/tasks";

export default function ProjectDetail() {
  const { id } = useParams(); // ambil ":id" dari URL /projects/:id

  // ───────────── STATE (semua useState di sini, di ATAS if (loading)) ─────────────
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [toastMsg, setToastMsg] = useState("");
  const [judulBaru, setJudulBaru] = useState(""); // [A] STATE: isi kolom "Tugas baru..."
  const [filter, setFilter] = useState("semua"); // [D] STATE: tombol filter yang aktif
  const [search, setSearch] = useState(""); // [D] STATE: isi kolom "Cari tugas..."
  const [editing, setEditing] = useState(null); // [E] STATE: tugas yang lagi diedit (null = tidak ada)
  const [editJudul, setEditJudul] = useState(""); // [E] STATE: isi kolom judul di modal
  const [editDeadline, setEditDeadline] = useState(""); // [E] STATE: isi kolom tanggal di modal

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
    setTasks([...tasks, newTask]); // update state lokal juga
    setJudulBaru("");
    setToastMsg("Tugas ditambahkan");
  }

  // [B] FUNGSI: toggle selesai (PATCH), optimistic update
  async function toggleTask(taskId, selesaiSekarang) {
    const snapshot = tasks; // simpan buat rollback kalau gagal
    // OPTIMISTIC UPDATE: ubah tampilan DULUAN, baru kirim request
    setTasks(
      tasks.map((t) =>
        t.id === taskId ? { ...t, selesai: !selesaiSekarang } : t,
      ),
    );
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
        t.id === editing.id
          ? { ...t, judul: editJudul, deadline: editDeadline }
          : t,
      ),
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
            <li
              key={task.id}
              className="flex items-center justify-between gap-2 rounded border p-2"
            >
              {/* [B] TAMPILAN: checkbox toggle + judul (dicoret kalau selesai) */}
              <label className="flex flex-1 items-center gap-2">
                <input
                  type="checkbox"
                  checked={task.selesai}
                  onChange={() => toggleTask(task.id, task.selesai)}
                />
                <span
                  className={task.selesai ? "line-through text-slate-400" : ""}
                >
                  {task.judul}
                </span>
                {/* [E] TAMPILAN: deadline kalau ada */}
                {task.deadline && (
                  <span className="text-xs text-slate-500">
                    {task.deadline}
                  </span>
                )}
              </label>
              {/* [E] TAMPILAN: tombol Edit */}
              <Button variant="ghost" onClick={() => mulaiEdit(task)}>
                Edit
              </Button>
              {/* [C] TAMPILAN: tombol Hapus */}
              <Button variant="danger" onClick={() => hapusTask(task.id)}>
                Hapus
              </Button>
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
