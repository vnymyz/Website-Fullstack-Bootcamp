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
