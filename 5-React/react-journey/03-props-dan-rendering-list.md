# Sesi 3 — Props & Rendering List

Tujuan: paham cara ngirim data ke component (props), render array data jadi list JSX, dan conditional rendering.

## Kenapa `key` Penting Banget?

React pakai `key` buat "ngenalin" tiap elemen antar render, biar tau mana yang baru, mana yang dihapus, mana yang cuma pindah posisi. Kalau kamu pakai index array sebagai `key`, dan urutan/isi array berubah (hapus item, acak urutan, dsb), React bisa salah "nempelin" elemen lama ke posisi yang salah — efeknya state ketuker antar baris. Aturan gampangnya: **selalu pakai id unik yang stabil** (dari database/data, bukan posisi array).

## Kenapa `0 && <Komponen />` Bahaya?

`&&` di JS itu return operand kiri kalau falsy. `0` itu falsy, tapi `0` BUKAN `false` — jadi `0 && <p>Halo</p>` hasilnya `0`, dan React bakal nampilin angka `0` di layar (bukan nyembunyiin apa-apa). Solusinya: pastikan kondisi di kiri `&&` itu boolean asli, misal `jumlah > 0 && <p>...</p>`, bukan `jumlah && <p>...</p>`.

## Langkah 1 — Component dengan Props

`src/components/TaskCard.jsx`:

```jsx
// Props = "parameter" buat component, dikirim kayak atribut HTML:
// <TaskCard judul="..." selesai={true} />
export default function TaskCard({ judul, prioritas = "normal", selesai }) {
  // prioritas = "normal" -> default value, dipakai kalau prop-nya gak dikirim.
  return (
    <div className="rounded-lg border border-slate-200 p-4">
      <h3 className={selesai ? "text-slate-400 line-through" : "text-slate-800"}>
        {judul}
      </h3>
      <span className="text-xs uppercase text-slate-500">{prioritas}</span>
    </div>
  );
}
```

## Langkah 2 — Data Terpisah

`src/data/tasks.js`:

```js
const tasks = [
  { id: 1, judul: "Setup Vite + React", prioritas: "tinggi", selesai: true },
  { id: 2, judul: "Belajar JSX", prioritas: "tinggi", selesai: true },
  { id: 3, judul: "Belajar props & list", prioritas: "normal", selesai: false },
  { id: 4, judul: "Belajar useState", prioritas: "tinggi", selesai: false },
  { id: 5, judul: "Icip-icip Tailwind", prioritas: "rendah", selesai: false },
];

export default tasks;
```

## Langkah 3 — Render List + Conditional di `App.jsx`

```jsx
import TaskCard from "./components/TaskCard.jsx";
import tasks from "./data/tasks.js";

export default function App() {
  const belumSelesai = tasks.filter((t) => !t.selesai);

  return (
    <main className="mx-auto max-w-xl px-6 py-8">
      <h1 className="mb-4 text-2xl font-bold">Daftar Tugas</h1>

      {/* .map() ubah array data jadi array elemen JSX.
          `key` WAJIB, harus id yang stabil & unik -- BUKAN index array. */}
      <div className="grid gap-3">
        {tasks.map((task) => (
          <TaskCard
            key={task.id}
            judul={task.judul}
            prioritas={task.prioritas}
            selesai={task.selesai}
          />
        ))}
      </div>

      {belumSelesai.length > 0 && (
        <p className="mt-4 text-sm text-amber-600">
          Masih ada {belumSelesai.length} tugas yang belum selesai.
        </p>
      )}

      <p className="mt-2 text-sm text-slate-500">
        {tasks.length === 0 ? "Belum ada tugas." : `Total: ${tasks.length} tugas`}
      </p>
    </main>
  );
}
```

## Latihan — Product/Task Card Grid

Bikin `src/components/ProductCard.jsx` + `src/data/products.js`:

1. Array data produk (minimal 6 item), tiap produk punya: `id`, `nama`, `harga`, `stok`.
2. Render semua produk pakai `.map()`, `key` pakai `id`.
3. Kalau `stok === 0`, tampilkan badge "Habis" (conditional rendering).
4. Di bawah grid, tampilkan total produk yang stoknya masih ada (`stok > 0`), pakai `&&`.

## Bug Hunt — Cari Sendiri Dulu!

Copy kode di bawah jadi component sementara (`src/BugHuntList.jsx`), import & render di `App.jsx`, coba jalanin. Ada **3 bug** sengaja ditaruh. Klik tombol "Hapus" dan "Acak Urutan", perhatiin yang aneh. Jangan buka jawaban di bawah sebelum coba sendiri.

```jsx
import { useState } from "react";

const dataAwal = [
  { id: 1, nama: "Nasi Goreng", harga: 15000 },
  { id: 2, nama: "Mie Ayam", harga: 12000 },
  { id: 3, nama: "Es Teh", harga: 5000 },
];

export default function BugHuntList() {
  const [items, setItems] = useState(dataAwal);

  function hapus(id) {
    setItems(items.filter((item) => item.id !== id));
  }

  function acakUrutan() {
    setItems([...items].sort(() => Math.random() - 0.5));
  }

  return (
    <div className="mx-auto max-w-md p-6">
      <button onClick={acakUrutan} className="mb-4 rounded bg-slate-800 px-3 py-1 text-white">
        Acak Urutan
      </button>

      <ul className="space-y-2">
        {/* BUG 1 di sini */}
        {items.map((item, index) => (
          <li key={index} className="flex justify-between rounded border p-2">
            {/* BUG 2 di sini */}
            <span>{item}</span>
            <button onClick={() => hapus(item.id)}>Hapus</button>
          </li>
        ))}
      </ul>

      {/* BUG 3 di sini */}
      <p className="mt-4 text-sm text-slate-500">
        Nama-nama menu: {items.map((item) => {
          item.nama.toUpperCase();
        })}
      </p>
    </div>
  );
}
```

<details>
<summary>Klik buat lihat jawaban (coba sendiri dulu!)</summary>

**Bug 1: `key={index}` bukan `key={item.id}`**

```jsx
// Salah: key={index}   →   Benar: key={item.id}
```
Kenapa masalah: klik "Acak Urutan"/"Hapus" item di tengah — kalau ada state per-item, state itu bisa "ketuker" ke baris lain, karena React ngenalin elemen berdasarkan `key`, dan index berubah tiap urutan berubah.

**Bug 2: `{item}` bukan `{item.nama}`**

```jsx
// Salah: <span>{item}</span>   →   Benar: <span>{item.nama}</span>
```
Kenapa masalah: React gak bisa render object JS langsung sebagai children. Error di console: "Objects are not valid as a React child".

**Bug 3: `.map()` tanpa `return`**

```jsx
// Salah:
items.map((item) => { item.nama.toUpperCase(); })
// Benar:
items.map((item) => item.nama.toUpperCase())
```
Kenapa masalah: arrow function dengan `{}` butuh `return` eksplisit. Tanpa itu, tiap elemen hasil `.map()` jadi `undefined`.

**Pelajaran penting:** ketiga bug ini gampang lolos review sekilas (kodenya "kelihatan" jalan). Ini contoh persis kenapa kode hasil AI/copy-paste harus dibaca teliti, bukan cuma dicek "muncul atau nggak".

</details>

## Catatan buat Kamu

1. Ganti `tasks.length` jadi 0 (sementara, buat testing) — pastikan kondisi "0 tugas" cuma nampilin "Belum ada tugas.", bukan malah dobel.
2. Coba render `<TaskCard judul="Belajar React" />` TANPA kirim prop `prioritas`. Apa yang muncul? Kenapa?

Lanjut ke [04-state-dan-event.md](04-state-dan-event.md).
