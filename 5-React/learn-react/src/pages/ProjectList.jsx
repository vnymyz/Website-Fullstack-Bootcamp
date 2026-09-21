import { Link } from "react-router-dom";
import useFetch from "../hooks/useFetch.js";
import EmptyState from "../components/EmptyState.jsx";

export default function ProjectList() {
  const {
    data: projects,
    loading,
    error,
  } = useFetch("http://localhost:3001/projects");

  if (loading) return <p className="p-6">Loading...</p>;
  if (error) return <p className="p-6 text-red-500">Error: {error}</p>;
  if (projects.length === 0) return <EmptyState text="Belum ada project." />;

  return (
    <div className="p-6">
      <h1 className="mb-4 text-2xl font-bold">Project Saya</h1>
      <ul className="space-y-2">
        {projects.map((p) => (
          <li key={p.id}>
            <Link to={`/projects/${p.id}`} className="text-blue-600 underline">
              {p.nama}
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
