# 15 - Dashboard dan CRUD Task (UI)

## Tujuan Pembelajaran
- Membuat komponen yang bisa dipakai ulang (form, item, filter).
- Memuat data dengan `useEffect`, mengelola state list, filter, dan pagination.
- Menghubungkan UI dengan operasi CRUD backend.

## Langkah

### 1. TaskFilter
Buat `frontend/src/components/TaskFilter.jsx`:

```jsx
// File: frontend/src/components/TaskFilter.jsx
const FILTERS = [
  { value: '', label: 'Semua' },
  { value: 'todo', label: 'Akan Dikerjakan' },
  { value: 'in_progress', label: 'Sedang Dikerjakan' },
  { value: 'done', label: 'Selesai' },
]

export default function TaskFilter({ value, onChange }) {
  return (
    <div className="filters">
      {FILTERS.map((f) => (
        <button
          key={f.value}
          className={`chip ${value === f.value ? 'chip-active' : ''}`}
          onClick={() => onChange(f.value)}
        >
          {f.label}
        </button>
      ))}
    </div>
  )
}
```

### 2. TaskForm
Buat `frontend/src/components/TaskForm.jsx`:

```jsx
// File: frontend/src/components/TaskForm.jsx
import { useState } from 'react'

const EMPTY = { title: '', description: '', status: 'todo', due_date: '' }

// Dipakai untuk membuat task baru (tanpa prop "initial") maupun mengubah task (dengan "initial").
export default function TaskForm({ initial, onSubmit, onCancel }) {
  const [form, setForm] = useState(
    initial
      ? {
          title: initial.title,
          description: initial.description || '',
          status: initial.status,
          due_date: initial.due_date ? initial.due_date.slice(0, 10) : '',
        }
      : EMPTY,
  )
  const [submitting, setSubmitting] = useState(false)

  const handleChange = (e) => setForm({ ...form, [e.target.name]: e.target.value })

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSubmitting(true)
    try {
      await onSubmit(form)
      if (!initial) setForm(EMPTY)
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <form className="card form" onSubmit={handleSubmit}>
      <h3>{initial ? 'Ubah Task' : 'Tambah Task'}</h3>

      <label>Judul</label>
      <input name="title" value={form.title} onChange={handleChange} required maxLength={200} />

      <label>Deskripsi</label>
      <textarea name="description" value={form.description} onChange={handleChange} rows={3} />

      <div className="row">
        <div>
          <label>Status</label>
          <select name="status" value={form.status} onChange={handleChange}>
            <option value="todo">Akan Dikerjakan</option>
            <option value="in_progress">Sedang Dikerjakan</option>
            <option value="done">Selesai</option>
          </select>
        </div>
        <div>
          <label>Tenggat</label>
          <input type="date" name="due_date" value={form.due_date} onChange={handleChange} />
        </div>
      </div>

      <div className="actions">
        <button className="btn" type="submit" disabled={submitting}>
          {submitting ? 'Menyimpan...' : 'Simpan'}
        </button>
        {onCancel && (
          <button className="btn btn-outline" type="button" onClick={onCancel}>
            Batal
          </button>
        )}
      </div>
    </form>
  )
}
```

**Penjelasan**
Satu komponen untuk dua kebutuhan: tambah (tanpa `initial`) dan ubah (dengan `initial`). Date dari backend berbentuk `2026-12-31T00:00:00Z`, sehingga kita potong 10 karakter pertama (`slice(0, 10)`) agar cocok dengan `<input type="date">`.

### 3. TaskItem
Buat `frontend/src/components/TaskItem.jsx`:

```jsx
// File: frontend/src/components/TaskItem.jsx
const STATUS_LABEL = {
  todo: 'Akan Dikerjakan',
  in_progress: 'Sedang Dikerjakan',
  done: 'Selesai',
}

export default function TaskItem({ task, onEdit, onDelete, onStatusChange }) {
  return (
    <div className="card task">
      <div className="task-head">
        <h4 className={task.status === 'done' ? 'done' : ''}>{task.title}</h4>
        <span className={`badge badge-${task.status}`}>{STATUS_LABEL[task.status]}</span>
      </div>

      {task.description && <p className="muted">{task.description}</p>}
      {task.due_date && (
        <p className="muted">Tenggat: {new Date(task.due_date).toLocaleDateString('id-ID')}</p>
      )}

      <div className="actions">
        <select value={task.status} onChange={(e) => onStatusChange(task, e.target.value)}>
          <option value="todo">Akan Dikerjakan</option>
          <option value="in_progress">Sedang Dikerjakan</option>
          <option value="done">Selesai</option>
        </select>
        <button className="btn btn-outline" onClick={() => onEdit(task)}>Ubah</button>
        <button className="btn btn-danger" onClick={() => onDelete(task)}>Hapus</button>
      </div>
    </div>
  )
}
```

### 4. Dashboard
Buat `frontend/src/pages/Dashboard.jsx`:

```jsx
// File: frontend/src/pages/Dashboard.jsx
import { useCallback, useEffect, useState } from 'react'
import { getErrorMessage } from '../api/axios.js'
import { createTask, deleteTask, getTasks, updateTask } from '../api/tasks.js'
import TaskFilter from '../components/TaskFilter.jsx'
import TaskForm from '../components/TaskForm.jsx'
import TaskItem from '../components/TaskItem.jsx'

const LIMIT = 5

export default function Dashboard() {
  const [tasks, setTasks] = useState([])
  const [status, setStatus] = useState('')
  const [page, setPage] = useState(1)
  const [total, setTotal] = useState(0)
  const [editing, setEditing] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const totalPages = Math.max(1, Math.ceil(total / LIMIT))

  const loadTasks = useCallback(async () => {
    setLoading(true)
    setError('')
    try {
      const res = await getTasks({ status, page, limit: LIMIT })
      setTasks(res.data.data.items)
      setTotal(res.data.data.total)
    } catch (err) {
      setError(getErrorMessage(err))
    } finally {
      setLoading(false)
    }
  }, [status, page])

  useEffect(() => {
    loadTasks()
  }, [loadTasks])

  const handleFilter = (value) => {
    setStatus(value)
    setPage(1)
  }

  const toPayload = (form) => ({
    title: form.title,
    description: form.description,
    status: form.status,
    due_date: form.due_date,
  })

  const handleCreate = async (form) => {
    try {
      await createTask(toPayload(form))
      setPage(1)
      await loadTasks()
    } catch (err) {
      setError(getErrorMessage(err))
    }
  }

  const handleUpdate = async (form) => {
    try {
      await updateTask(editing.id, toPayload(form))
      setEditing(null)
      await loadTasks()
    } catch (err) {
      setError(getErrorMessage(err))
    }
  }

  const handleStatusChange = async (task, newStatus) => {
    try {
      await updateTask(task.id, {
        title: task.title,
        description: task.description,
        status: newStatus,
        due_date: task.due_date ? task.due_date.slice(0, 10) : '',
      })
      await loadTasks()
    } catch (err) {
      setError(getErrorMessage(err))
    }
  }

  const handleDelete = async (task) => {
    if (!window.confirm(`Hapus task "${task.title}"?`)) return
    try {
      await deleteTask(task.id)
      // Jika item terakhir di halaman dihapus, mundur satu halaman.
      if (tasks.length === 1 && page > 1) setPage(page - 1)
      else await loadTasks()
    } catch (err) {
      setError(getErrorMessage(err))
    }
  }

  return (
    <div className="dashboard">
      <section>
        {editing ? (
          <TaskForm
            key={editing.id}
            initial={editing}
            onSubmit={handleUpdate}
            onCancel={() => setEditing(null)}
          />
        ) : (
          <TaskForm onSubmit={handleCreate} />
        )}
      </section>

      <section>
        <h2>Daftar Task ({total})</h2>
        <TaskFilter value={status} onChange={handleFilter} />

        {error && <div className="alert">{error}</div>}
        {loading && <p>Memuat...</p>}
        {!loading && tasks.length === 0 && <p className="muted">Belum ada task.</p>}

        {tasks.map((task) => (
          <TaskItem
            key={task.id}
            task={task}
            onEdit={setEditing}
            onDelete={handleDelete}
            onStatusChange={handleStatusChange}
          />
        ))}

        <div className="pagination">
          <button className="btn btn-outline" disabled={page <= 1} onClick={() => setPage(page - 1)}>
            Sebelumnya
          </button>
          <span>Halaman {page} / {totalPages}</span>
          <button className="btn btn-outline" disabled={page >= totalPages} onClick={() => setPage(page + 1)}>
            Berikutnya
          </button>
        </div>
      </section>
    </div>
  )
}
```

**Penjelasan**
- `loadTasks` dibungkus `useCallback` dan bergantung pada `status` dan `page`. `useEffect` memanggilnya otomatis setiap kali filter atau halaman berubah.
- `handleFilter` mengembalikan ke halaman 1 saat filter berganti.
- Mode ubah: `editing` berisi task yang sedang diubah. `key={editing.id}` memaksa form di-*reset* untuk task yang berbeda.
- `window.confirm` meminta konfirmasi sebelum menghapus.
- Jika item terakhir di halaman 2 dihapus, kita mundur ke halaman 1 agar tidak menampilkan halaman kosong.
- Ubah status cepat lewat dropdown di `TaskItem` memanggil `updateTask` dengan data task yang ada.

## Cek Hasil
```powershell
npm run dev
```
Coba: tambah task, ubah, ganti status, filter, hapus, dan pagination (tambahkan lebih dari 5 task).

## Kesalahan Umum
- Perubahan tidak muncul -> lupa `await loadTasks()` setelah operasi.
- Loop request tak berujung -> dependency `useEffect` salah (fungsi dibuat ulang tiap render). Karena itu kita memakai `useCallback`.
- Tanggal bergeser satu hari -> zona waktu. Untuk latihan ini kita menampilkan `toLocaleDateString`; lihat latihan berikut.

## Latihan
1. Tambahkan kolom pencarian judul (butuh perubahan backend, lihat latihan di `09-crud-task.md`).
2. Tampilkan task yang lewat tenggat dengan warna merah.
3. Tambahkan notifikasi "Berhasil disimpan" yang hilang setelah 3 detik.

## Catatan Instruktur
Estimasi 90 menit. Bagi menjadi dua sesi: komponen dulu, dashboard setelahnya.
