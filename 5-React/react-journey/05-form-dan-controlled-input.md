# Sesi 5 — Form & Controlled Input

Tujuan: controlled forms, cara SPA menangani input dibanding PHP yang reload halaman tiap submit.

**Hasil akhir sesi ini:**
- `TaskForm` dengan input teks, dropdown, tanggal, dan checkbox.
- Validasi: pesan error muncul di bawah field yang salah, dan data yang udah diketik tetap ada.
- Data form naik ke `App.jsx` lewat props (fungsi `onAddTask`), lalu ditampilkan sebagai list — termasuk tanggal deadline-nya.

**Sebelum mulai:** sesi 4 selesai — kamu paham `useState`, immutable update, dan `onClick={fn}`.

**Peta langkah:**

| Langkah | Isi |
|---|---|
| Konsep | Controlled input, satu state object, `preventDefault` |
| 1 | Bikin kerangka `TaskForm` (satu field dulu) |
| 2 | Satu state object buat semua field |
| 3 | Field lain: select, date, checkbox |
| 4 | Validasi |
| 5 | Kirim data ke `App.jsx` dan tampilkan |
| Latihan | Form dengan validasi ala PHP |

---

## Konsep

### Controlled Input = `value` + `onChange`

Di React, `<input>` "dikontrol" oleh state — nilainya SELALU berasal dari `value={state}`, dan tiap kali user ngetik, `onChange` update state itu. Ini beda sama HTML biasa yang `<input>`-nya nyimpen nilai sendiri di DOM.

```jsx
const [nama, setNama] = useState("");

<input value={nama} onChange={(e) => setNama(e.target.value)} />
```

Alurnya tiap kali user ngetik satu huruf:
1. Browser jalanin `onChange`, ngasih event `e`.
2. `e.target.value` = isi input sekarang.
3. `setNama(...)` update state.
4. React render ulang, `value={nama}` nampilin nilai baru.

Satu-satunya "sumber kebenaran" adalah state, bukan DOM.

### Satu State Object buat Form Panjang

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

**`[name]: value` itu apa?** Kurung siku di sisi kiri artinya "pakai *isi variabel* `name` sebagai nama key". Kalau `name` = `"judul"`, maka `{ [name]: value }` sama dengan `{ judul: value }`.

### `onSubmit` + `preventDefault()`

```jsx
function handleSubmit(e) {
  e.preventDefault(); // WAJIB -- browser default-nya reload halaman pas form disubmit
  console.log(form);
}

<form onSubmit={handleSubmit}>...</form>
```

**Bandingin sama PHP:** form PHP biasa submit → browser reload → `$_POST` isinya data form → server proses → `header("Location: ...")` buat redirect. Di React, gak ada reload sama sekali — semua diproses di JS, `preventDefault()` yang nyetop browser dari reload defaultnya.

### Beda Jenis Input

| Jenis | Yang dibaca | `value` atau `checked`? |
|---|---|---|
| `<input type="text">`, `<select>`, `<textarea>`, `<input type="date">` | `e.target.value` | `value={...}` |
| `<input type="checkbox">` | `e.target.checked` (true/false) | `checked={...}` |

---

## Langkah 1 — Kerangka `TaskForm` (Satu Field Dulu)

Bangun bertahap: satu field, tes, baru tambah.

**1a.** Bikin `src/components/TaskForm.jsx`:

```jsx
import { useState } from "react";

export default function TaskForm() {
  const [judul, setJudul] = useState("");

  function handleSubmit(e) {
    e.preventDefault();
    console.log("Submit:", judul);
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-3 rounded-lg border p-4">
      <input
        value={judul}
        onChange={(e) => setJudul(e.target.value)}
        placeholder="Judul tugas"
        className="w-full rounded border px-2 py-1"
      />
      <button type="submit" className="rounded bg-blue-600 px-4 py-1 text-white">
        Simpan Tugas
      </button>
    </form>
  );
}
```

**1b.** Pasang di `App.jsx`:

```jsx
import TaskForm from "./components/TaskForm.jsx";

export default function App() {
  return (
    <main className="mx-auto max-w-md space-y-6 px-6 py-8">
      <h1 className="text-2xl font-bold">Form Tugas</h1>
      <TaskForm />
    </main>
  );
}
```

### Cek

- [ ] Ketik di input → teks muncul (state kepakai).
- [ ] Klik Simpan → Console nampilin `Submit: <yang kamu ketik>`, **halaman gak reload**.
- [ ] (Eksperimen) Hapus `e.preventDefault()`, submit → halaman reload dan teks hilang. Balikin lagi.

---

## Langkah 2 — Satu State Object buat Semua Field

Ganti `useState("")` tunggal jadi satu object, dan pakai satu `handleChange`.

Tulis ulang bagian atas `TaskForm.jsx`:

```jsx
import { useState } from "react";

const initialForm = { judul: "", prioritas: "normal", deadline: "", penting: false };

export default function TaskForm({ onAddTask }) {
  const [form, setForm] = useState(initialForm);

  function handleChange(e) {
    const { name, value, type, checked } = e.target;
    setForm({ ...form, [name]: type === "checkbox" ? checked : value });
  }
  // ... handleSubmit dan JSX di langkah berikutnya
}
```

**Baca per bagian:**
- `initialForm` ditaruh **di luar** component — nilai awal form, dan dipakai lagi buat reset setelah submit.
- `{ ...form, [name]: nilai }` — salin form lama, ganti cuma field yang lagi diketik (immutable update dari sesi 4).
- `type === "checkbox" ? checked : value` — checkbox pakai `checked`, sisanya pakai `value`.

---

## Langkah 3 — Field Lain: Select, Date, Checkbox

Ganti JSX `return` jadi form lengkap. Tambah `name` di **setiap** input, dan bacanya lewat `form.<nama>`:

```jsx
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
```

Perhatiin: checkbox pakai `checked`, bukan `value`, dan `e.target.checked` bukan `e.target.value`.

**Sementara**, `handleSubmit` cuma nge-log:

```jsx
function handleSubmit(e) {
  e.preventDefault();
  console.log(form);
}
```

### Cek

- [ ] Ubah tiap field → klik Simpan → Console nampilin objek lengkap: `{ judul: "...", prioritas: "tinggi", deadline: "2026-...", penting: true }`.
- [ ] Semua field ke-update independen (ngetik judul gak ngereset tanggal).

---

## Langkah 4 — Validasi

Aturan: field wajib gak boleh kosong. Pesan error muncul **di bawah field yang salah**.

**4a.** Tambah state `errors` (di bawah state `form`):

```jsx
const [errors, setErrors] = useState({});
```

**4b.** Tambah fungsi `validate`. Fungsi ini **cuma ngumpulin pesan error** dan nge-return objeknya:

```jsx
function validate() {
  const newErrors = {};
  if (!form.judul.trim()) newErrors.judul = "Judul wajib diisi";
  if (!form.deadline) newErrors.deadline = "Deadline wajib diisi";
  return newErrors;
}
```

**4c.** Ganti `handleSubmit`:

```jsx
function handleSubmit(e) {
  e.preventDefault();
  const validationErrors = validate();
  if (Object.keys(validationErrors).length > 0) {
    setErrors(validationErrors);
    return; // stop -- jangan lanjut kalau ada error
  }
  onAddTask(form);
  setForm(initialForm); // reset setelah submit sukses
  setErrors({});
}
```

**4d.** Tampilkan pesan error di bawah field-nya (di dalam `<div>` pembungkus tiap input):

```jsx
{errors.judul && <p className="text-sm text-red-500">{errors.judul}</p>}
...
{errors.deadline && <p className="text-sm text-red-500">{errors.deadline}</p>}
```

**Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `Object.keys(validationErrors).length > 0` | Ada berapa key di objek error? Lebih dari 0 = ada yang salah |
| `return;` di dalam `if` | Keluar dari `handleSubmit` — kode setelahnya (`onAddTask`, reset) gak jalan |
| `errors.judul && <p>...</p>` | Tampilin pesan cuma kalau ada error buat field itu |
| `setForm(initialForm)` | Kosongin form — cuma setelah sukses |

**Kenapa data yang udah diketik tetap ada kalau validasi gagal?** Karena kita `return` sebelum `setForm(initialForm)`. State form gak disentuh, jadi `value={form.judul}` tetap nampilin apa yang diketik. Ini yang di PHP disebut "sticky form" — di React otomatis.

### Cek

- [ ] Klik Simpan dengan semua kosong → dua pesan merah muncul.
- [ ] Isi judul saja → pesan judul hilang saat submit berikutnya, pesan deadline masih ada, dan **judul yang kamu ketik tetap di kolom**.

---

## Langkah 5 — Kirim Data ke `App.jsx` dan Tampilkan

`TaskForm` gak nyimpen daftar tugas — dia cuma ngumpulin input. Daftar tugasnya dimiliki `App.jsx`. Data naik lewat **prop berupa fungsi** (`onAddTask`).

**5a.** Di `App.jsx`:

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
          <li key={t.id}>
            {t.judul} — {t.prioritas} {t.penting && "⭐"}
            {t.deadline &&
              ` — ${new Date(t.deadline).toLocaleDateString("id-ID")}`}
          </li>
        ))}
      </ul>
    </main>
  );
}
```

**Baca per bagian:**
- `onAddTask={handleAddTask}` — kirim **fungsi** ke `TaskForm`. Di `TaskForm`, `onAddTask(form)` manggil fungsi itu dan ngirim data form naik ke `App`.
- `{ ...task, id: Date.now() }` — salin semua field form, tambah `id`.
- `t.penting && "⭐"` — tampilin bintang cuma kalau `penting` true.
- `t.deadline && ...` — tampilin tanggal cuma kalau ada. `new Date("2026-09-21").toLocaleDateString("id-ID")` mengubah string tanggal jadi format Indonesia (`21/9/2026`).

**Arah data:** data turun lewat props (`onAddTask` turun ke form), event naik lewat pemanggilan fungsi (`onAddTask(form)` naik ke `App`). Ini pola "lifting state up".

### Cek

- [ ] Isi form lengkap → Simpan → muncul di list di bawahnya dengan judul, prioritas, bintang (kalau dicentang), dan tanggal.
- [ ] Setelah submit, form kosong lagi.
- [ ] Tambah 3 tugas → semuanya tampil, urutan sesuai dimasukin.

### Kode lengkap `TaskForm.jsx` (buat dicocokin)

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

---

## Latihan — Sticky Validating Form

Bikin form task dengan requirement yang SAMA kayak form PHP kamu di `3-PHP/php-journey/2-Forms-and-Superglobals/` (buka lagi filenya buat bandingin):

- Semua field wajib divalidasi, error muncul di bawah field yang salah.
- Kalau ada error, data yang UDAH DIKETIK tetap ada (sticky) — di React ini otomatis karena state gak direset kalau validasi gagal (bandingin: di PHP, kamu manual `value="<?= $_POST['x'] ?? '' ?>"`).
- Setelah submit sukses, form direset kosong lagi.

## Catatan buat Kamu

1. Bandingin baris kode "sticky form" di React (otomatis, karena state) vs versi PHP kamu (manual echo tiap value). Tulis 2-3 kalimat bedanya.
2. Tambah field baru: `deskripsi` (textarea), validasi minimal 10 karakter.
3. Coba hapus `e.preventDefault()`, submit form, lihat apa yang kejadian (halaman reload).
4. Bikin pesan error hilang begitu user mulai ngetik di field itu (petunjuk: di `handleChange`, hapus key `name` dari `errors`).

## Kalau Error

| Gejala | Biasanya penyebabnya |
|---|---|
| Input gak bisa diketik | Ada `value` tapi lupa `onChange` (atau `name`-nya gak cocok sama key di state) |
| Ngetik satu field, field lain ikut kereset | `setForm({ [name]: value })` tanpa `...form` di depan |
| Checkbox gak bisa dicentang | Pakai `value={...}` alih-alih `checked={...}`, atau baca `e.target.value` alih-alih `e.target.checked` |
| Halaman reload pas submit | Lupa `e.preventDefault()` di `handleSubmit` |
| Warning `A component is changing an uncontrolled input to be controlled` | Nilai awal state `undefined` — pastikan `initialForm` punya semua field (`""` untuk teks, `false` untuk checkbox) |
| `onAddTask is not a function` | `TaskForm` dipakai tanpa prop `onAddTask` — kirim dari `App.jsx` |
| Tanggal tampil `Invalid Date` | `deadline` kosong tapi tetap diformat — bungkus dengan `t.deadline && ...` |
| Form gak ke-reset setelah submit | Lupa `setForm(initialForm)`, atau `return` terlalu awal |

Lanjut ke [06-useeffect-dan-fetch.md](06-useeffect-dan-fetch.md).
