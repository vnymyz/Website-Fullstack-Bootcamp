import { useParams, useNavigate } from "react-router-dom";

export default function TaskDetail() {
  const { id } = useParams(); // ambil ":id" dari URL
  const navigate = useNavigate();

  function handleDelete() {
    // pura-pura hapus, terus redirect balik ke list
    alert(`Tugas ${id} dihapus (simulasi)`);
    navigate("/tasks");
  }

  return (
    <div className="p-6">
      <h1 className="text-xl font-bold">Detail Tugas #{id}</h1>
      <button
        onClick={handleDelete}
        className="mt-4 rounded bg-red-600 px-3 py-1 text-white"
      >
        Hapus & Kembali ke List
      </button>
    </div>
  );
}
