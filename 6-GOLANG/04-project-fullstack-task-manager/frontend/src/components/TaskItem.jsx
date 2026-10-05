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
