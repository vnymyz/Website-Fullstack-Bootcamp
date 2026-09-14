# Sesi 5 — Form & Controlled Input

Tujuan: controlled forms, cara SPA menangani input dibanding PHP yang reload halaman tiap submit.

## Controlled Input = `value` + `onChange`

Di React, `<input>` "dikontrol" oleh state — nilainya SELALU berasal dari `value={state}`, dan tiap kali user ngetik, `onChange` update state itu. Ini beda sama HTML biasa yang `<input>`-nya nyimpen nilai sendiri di DOM.

```jsx
const [nama, setNama] = useState("");

<input value={nama} onChange={(e) => setNama(e.target.value)} />
```

## Satu State Object buat Form Panjang

Daripada `useState` terpisah untuk tiap field, pakai SATU object state + pola `e.target.name`:

```jsx
const [form, setForm] = useState({ judul: "", prioritas: "normal", deadline: "" });

function handleChange(e) {
  const { name, value } = e.target;
  setForm({ ...form, [name]: value }); // computed property name: [name]
}

<input name="judul" value={form.judul} onChange={handleChange} />
<input name="deadline" type="date" value={form.deadline} onChange={handleChange} />
```

Satu fungsi `handleChange` dipakai buat SEMUA input, asal tiap `<input>` punya `name` yang cocok sama key di object `form`.

## `onSubmit` + `preventDefault()`

```jsx
function handleSubmit(e) {
  e.preventDefault(); // WAJIB -- browser default-nya reload halaman pas form disubmit
  console.log(form);
}

<form onSubmit={handleSubmit}>...</form>
```

**Bandingin sama PHP:** form PHP biasa submit → browser reload → `$_POST` isinya data form → server proses → `header("Location: ...")` buat redirect. Di React, gak ada reload sama sekali — semua diproses di JS, `preventDefault()` yang nyetop browser dari reload defaultnya.

## Langkah — Form Task Lengkap dengan Validasi

`src/components/TaskForm.jsx`:

```jsx
import { useState } from "react";

const initialForm = { judul: "", prioritas: "normal", deadline: "", penting: false };

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

      <select name="prioritas" value={form.prioritas} onChange={handleChange} className="w-full rounded border px-2 py-1">
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
        {errors.deadline && <p className="text-sm text-red-500">{errors.deadline}</p>}
      </div>

      <label className="flex items-center gap-2 text-sm">
        <input type="checkbox" name="penting" checked={form.penting} onChange={handleChange} />
        Tandai sebagai penting
      </label>

      <button type="submit" className="rounded bg-blue-600 px-4 py-1 text-white">
        Simpan Tugas
      </button>
    </form>
  );
}
```

Perhatiin: checkbox pakai `checked`, bukan `value`, dan `e.target.checked` bukan `e.target.value`.

Pasang di `App.jsx`, gabungin sama `TodoList` dari sesi sebelumnya kalau mau (opsional):

```jsx
import { useState } from "react";
import TaskForm from "./components/TaskForm.jsx";

export default function App() {
  const [tasks, setTasks] = useState([]);

  function handleAddTask(task) {
    setTasks([...tasks, { ...task, id: Date.now() }]);
  }

  return (
    <main className="mx-auto max-w-md space-y-6 px-6 py-8">
      <h1 className="text-2xl font-bold">Form Tugas</h1>
      <TaskForm onAddTask={handleAddTask} />
      <ul className="space-y-1 text-sm">
        {tasks.map((t) => (
          <li key={t.id}>{t.judul} — {t.prioritas} {t.penting && "⭐"}</li>
        ))}
      </ul>
    </main>
  );
}
```

## Latihan — Sticky Validating Form

Bikin form task dengan requirement yang SAMA kayak form PHP kamu di `3-PHP/php-journey/2-Forms-and-Superglobals/` (buka lagi filenya buat bandingin):

- Semua field wajib divalidasi, error muncul di bawah field yang salah.
- Kalau ada error, data yang UDAH DIKETIK tetap ada (sticky) — di React ini otomatis karena state gak direset kalau validasi gagal (bandingin: di PHP, kamu manual `value="<?= $_POST['x'] ?? '' ?>"`).
- Setelah submit sukses, form direset kosong lagi.

## Catatan buat Kamu

1. Bandingin baris kode "sticky form" di React (otomatis, karena state) vs versi PHP kamu (manual echo tiap value). Tulis 2-3 kalimat bedanya.
2. Tambah field baru: `deskripsi` (textarea), validasi minimal 10 karakter.
3. Coba hapus `e.preventDefault()`, submit form, lihat apa yang kejadian (halaman reload).

Lanjut ke [06-useeffect-dan-fetch.md](06-useeffect-dan-fetch.md).
