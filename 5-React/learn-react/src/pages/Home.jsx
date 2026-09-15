import { Link } from "react-router-dom";

export default function Home() {
  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold">Beranda</h1>
      <Link to="/tasks" className="text-blue-600 underline">
        Lihat Daftar Tugas
      </Link>
    </div>
  );
}
