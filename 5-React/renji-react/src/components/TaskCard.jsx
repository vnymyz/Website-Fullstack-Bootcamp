// Props = "parameter" buat component, dikirim kayak atribut HTML:
// <TaskCard judul="..." selesai={true} />
export default function TaskCard({ judul, prioritas = "normal", selesai }) {
  // prioritas = "normal" -> default value, dipakai kalau prop-nya gak dikirim.
  return (
    <div className="rounded-lg border border-slate-200 p-4">
      <h3
        className={selesai ? "text-slate-400 line-through" : "text-slate-200"}
      >
        {judul}
      </h3>
      <span className="text-xs uppercase text-slate-500">{prioritas}</span>
    </div>
  );
}
