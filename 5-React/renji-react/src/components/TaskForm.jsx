import { useState } from "react";

const initialForm = {
  judul: "",
  prioritas: "normal",
  deadline: "",
  penting: false,
};

export default function TaskForm({ onAddTask }) {
  const [form, setForm] = useState(initialForm);
  const [errors, setErrors] = useState({});

  function handleChange(e) {
    const { name, value, type, checked } = e.target;
    setForm({ ...form, [name]: type === "checkbox" ? checked : value });
  }

  function validate() {
    const newErrors = {};
    if (!form.judul.trim()) newErrors.judul = "Judul wajib diisi";
    if (!form.deadline) newErrors.deadline = "Deadline wajib diisi";
    return newErrors;
  }

  function handleSubmit(e) {
    e.preventDefault();
    const validationErrors = validate();
    if (Object.keys(validationErrors).length > 0) {
      setErrors(validationErrors);
      return;
    }
    onAddTask(form);
    setForm(initialForm); // reset setelah submit sukses
    setErrors({});
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-3 rounded-lg border p-4">
      <div>
        <input
          name="judul"
          value={form.judul}
          onChange={handleChange}
          placeholder="Judul tugas"
          className="w-full rounded border px-2 py-1"
        />
        {errors.judul && <p className="text-sm text-red-500">{errors.judul}</p>}
      </div>

      <select
        name="prioritas"
        value={form.prioritas}
        onChange={handleChange}
        className="w-full rounded border px-2 py-1"
      >
        <option value="rendah">Rendah</option>
        <option value="normal">Normal</option>
        <option value="tinggi">Tinggi</option>
      </select>

      <div>
        <input
          type="date"
          name="deadline"
          value={form.deadline}
          onChange={handleChange}
          className="w-full rounded border px-2 py-1"
        />
        {errors.deadline && (
          <p className="text-sm text-red-500">{errors.deadline}</p>
        )}
      </div>

      <label className="flex items-center gap-2 text-sm">
        <input
          type="checkbox"
          name="penting"
          checked={form.penting}
          onChange={handleChange}
        />
        Tandai sebagai penting
      </label>

      <button
        type="submit"
        className="rounded bg-blue-600 px-4 py-1 text-white"
      >
        Simpan Tugas
      </button>
    </form>
  );
}
