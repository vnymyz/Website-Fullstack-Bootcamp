import { useState, useEffect, useCallback } from "react";
import { useParams } from "react-router-dom";
import button from "../components/Button.jsx";
import Modal from "../components/Modal.jsx";
import Toast from "../components/Toast.jsx";
import EmptyState from "../components/EmptyState.jsx";

const API = "http://localhost:3001/tasks";

export default function ProjectDetail() {
  const { id } = useParams(); // ambil ":id" dari URL /projects/:id
  const [tasks, setTasks] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [toastMsg, setToastMsg] = useState("");

  // fungsi stabil buat Toast (lihat penjelasan di Langkah 3d)
  const tutupToast = useCallback(() => setToastMsg(""), []);

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
  }, [id]); // jalan lagi kalau id di URL berubah

  if (loading) return <p className="p-6">Loading...</p>;
  if (error) return <p className="p-6 text-red-500">Error: {error}</p>;

  return (
    <div className="mx-auto max-w-lg p-6">
      <h1 className="mb-4 text-xl font-bold">Tugas Project #{id}</h1>
      <pre>{JSON.stringify(tasks, null, 2)}</pre>
      <Toast message={toastMsg} onDone={tutupToast} />
    </div>
  );
}
