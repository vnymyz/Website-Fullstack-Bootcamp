// `children` itu prop spesial: apapun yang kamu taruh DI ANTARA
// <Card>...</Card> pas dipakai, otomatis masuk ke sini sebagai `children`.
export default function Card({ title, children }) {
  return (
    <div className="rounded-lg border border-slate-200 p-4 shadow-sm">
      <h3 className="mb-2 font-semibold text-slate-800">{title}</h3>
      <div className="text-sm text-slate-600">{children}</div>
    </div>
  );
}
