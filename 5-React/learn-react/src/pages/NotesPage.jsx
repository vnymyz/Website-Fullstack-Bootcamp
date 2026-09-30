import { useState, useEffect } from "react";
import { useAuth } from "../context/AuthContext.jsx";

const API = "http://localhost:8000/api";

// [A] HELPER: bungkus fetch + 3 header wajib + penanganan 401.
// Fungsi biasa di LUAR component.
async function apiRequest(token, logout, path, options = {}) {
  const res = await fetch(`${API}${path}`, {
    ...options,
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
      Authorization: `Bearer ${token}`, // "tiket masuk" yang diminta auth:sanctum
    },
  });
  if (res.status === 401) {
    logout(); // token invalid/expired -> paksa logout, ProtectedRoute nendang ke /login
    throw new Error("Sesi habis, silakan login lagi");
  }
  if (!res.ok) throw new Error("Permintaan gagal");
  return res.status === 204 ? null : res.json(); // 204 = sukses tanpa isi (DELETE)
}

export default function NotesPage() {
  const { token, logout } = useAuth();

  // ───────────── STATE (semua useState di ATAS if (loading)) ─────────────
  const [notes, setNotes] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [form, setForm] = useState({ judul: "", isi: "" }); // [C] isi form tambah/edit
  const [editingId, setEditingId] = useState(null); // [C] null = mode tambah, ada isi = mode edit

  // ───────────── [B] AMBIL DAFTAR NOTES (GET) ─────────────
  useEffect(() => {
    async function load() {
      try {
        setLoading(true);
        const json = await apiRequest(token, logout, "/notes");
        setNotes(json.data); // Laravel Resource ngebungkus dalam "data"
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, [token, logout]);

  // ───────────── [C] TAMBAH / EDIT ─────────────
  function handleChange(e) {
    const { name, value } = e.target;
    setForm({ ...form, [name]: value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    if (!form.judul.trim() || !form.isi.trim()) return;
    setError("");
    try {
      if (editingId) {
        // mode edit -> PUT
        const json = await apiRequest(token, logout, `/notes/${editingId}`, {
          method: "PUT",
          body: JSON.stringify(form),
        });
        setNotes(notes.map((n) => (n.id === editingId ? json.data : n)));
      } else {
        // mode tambah -> POST
        const json = await apiRequest(token, logout, "/notes", {
          method: "POST",
          body: JSON.stringify(form),
        });
        setNotes([...notes, json.data]);
      }
      setForm({ judul: "", isi: "" });
      setEditingId(null);
    } catch (err) {
      setError(err.message);
    }
  }

  function mulaiEdit(note) {
    setEditingId(note.id);
    setForm({ judul: note.judul, isi: note.isi });
  }

  function batalEdit() {
    setEditingId(null);
    setForm({ judul: "", isi: "" });
  }

  // ───────────── [D] HAPUS ─────────────
  async function hapusNote(id) {
    if (!confirm("Yakin hapus catatan ini?")) return;
    setError("");
    try {
      await apiRequest(token, logout, `/notes/${id}`, { method: "DELETE" });
      setNotes(notes.filter((n) => n.id !== id));
    } catch (err) {
      setError(err.message);
    }
  }

  // ───────────── EARLY RETURN ─────────────
  if (loading) return <p className="p-6">Loading...</p>;

  // ───────────── TAMPILAN ─────────────
  return (
    <div className="mx-auto max-w-lg p-6">
      <h1 className="mb-4 text-2xl font-bold">Catatan Saya</h1>
      {error && <p className="mb-2 text-sm text-red-500">{error}</p>}

      {/* [C] form tambah/edit: label tombol berubah sesuai mode */}
      <form
        onSubmit={handleSubmit}
        className="mb-4 space-y-2 rounded border p-3"
      >
        <input
          name="judul"
          value={form.judul}
          onChange={handleChange}
          placeholder="Judul"
          className="w-full rounded border px-2 py-1"
        />
        <textarea
          name="isi"
          value={form.isi}
          onChange={handleChange}
          placeholder="Isi catatan"
          className="w-full rounded border px-2 py-1"
        />
        <div className="flex gap-2">
          <button
            type="submit"
            className="rounded bg-blue-600 px-3 py-1 text-white"
          >
            {editingId ? "Simpan Perubahan" : "Tambah"}
          </button>
          {editingId && (
            <button
              type="button"
              onClick={batalEdit}
              className="text-sm text-slate-500"
            >
              Batal
            </button>
          )}
        </div>
      </form>

      {/* [B] list notes */}
      <ul className="space-y-2">
        {notes.map((n) => (
          <li key={n.id} className="rounded border p-2">
            <p className="font-semibold">{n.judul}</p>
            <p className="text-sm text-slate-600">{n.isi}</p>
            <div className="mt-1 flex gap-3 text-sm">
              {/* [C] tombol Edit */}
              <button onClick={() => mulaiEdit(n)} className="text-blue-600">
                Edit
              </button>
              {/* [D] tombol Hapus */}
              <button onClick={() => hapusNote(n.id)} className="text-red-500">
                Hapus
              </button>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
}
