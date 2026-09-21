import { useState } from "react";
import { Link } from "react-router-dom";
import TaskForm from "../components/TaskForm.jsx";
import TaskCard from "../components/TaskCard.jsx";

export default function TaskList() {
  const [tasks, setTasks] = useState([
    { id: 1, judul: "Belajar Router" },
    { id: 2, judul: "Belajar useParams" },
  ]);

  function addTask(form) {
    setTasks([...tasks, { id: Date.now(), ...form }]);
  }

  return (
    <div className="mx-auto max-w-md space-y-4 p-6">
      <h1 className="text-xl font-bold">Daftar Tugas</h1>
      <TaskForm onAddTask={addTask} />
      <ul className="space-y-2">
        {tasks.map((t) => (
          <li key={t.id}>
            <Link to={`/tasks/${t.id}`} className="block">
              <TaskCard
                judul={t.judul}
                prioritas={t.prioritas}
                deadline={t.deadline}
              />
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
