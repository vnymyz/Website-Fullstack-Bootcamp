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
