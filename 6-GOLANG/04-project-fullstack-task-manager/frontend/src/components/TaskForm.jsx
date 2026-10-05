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
