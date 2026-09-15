import { useState } from "react";

export default function TodoList() {
  const [todos, setTodos] = useState([
    { id: 1, text: "Belajar useState", done: false },
    { id: 2, text: "Belajar immutability", done: false },
  ]);
  const [input, setInput] = useState("");
  const [filter, setFilter] = useState("semua"); // "semua" | "aktif" | "selesai"

  function tambah() {
    if (!input.trim()) return;
    // IMMUTABLE UPDATE: array baru pakai spread, bukan todos.push(...)
    setTodos([...todos, { id: Date.now(), text: input, done: false }]);
    setInput("");
  }

  function toggle(id) {
    setTodos(todos.map((t) => (t.id === id ? { ...t, done: !t.done } : t)));
  }

  function hapus(id) {
    setTodos(todos.filter((t) => t.id !== id));
  }

  // DERIVED STATE: dihitung tiap render, gak disimpan sebagai state sendiri.
  const visibleTodos = todos.filter((t) => {
    if (filter === "aktif") return !t.done;
    if (filter === "selesai") return t.done;
    return true;
  });

  return (
    <div className="mx-auto max-w-md space-y-4 p-4">
      <div className="flex gap-2">
        <input
          value={input}
          onChange={(e) => setInput(e.target.value)}
          placeholder="Tugas baru..."
          className="flex-1 rounded border px-2 py-1"
        />
        <button
          onClick={tambah}
          className="rounded bg-blue-600 px-3 py-1 text-white"
        >
          Tambah
        </button>
      </div>

      <div className="flex gap-2 text-sm">
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

      <ul className="space-y-1">
        {visibleTodos.map((todo) => (
          <li
            key={todo.id}
            className="flex items-center justify-between rounded border px-2 py-1"
          >
            <span
              onClick={() => toggle(todo.id)}
              className={
                todo.done
                  ? "cursor-pointer line-through text-slate-400"
                  : "cursor-pointer"
              }
            >
              {todo.text}
            </span>
            <button
              onClick={() => hapus(todo.id)}
              className="text-red-500 text-sm"
            >
              Hapus
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
}
