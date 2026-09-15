import { Link } from "react-router-dom";

const tasks = [
  { id: 1, judul: "Belajar Router" },
  { id: 2, judul: "Belajar useParams" },
];

export default function TaskList() {
  return (
    <div className="p-6">
      <h1 className="text-xl font-bold">Daftar Tugas</h1>
      <ul>
        {tasks.map((t) => (
          <li key={t.id}>
            <Link to={`/tasks/${t.id}`} className="text-blue-600 underline">
              {t.judul}
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
