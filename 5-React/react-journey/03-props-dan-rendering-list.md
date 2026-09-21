# Sesi 3 — Props & Rendering List

Tujuan: paham cara ngirim data ke component (props), render array data jadi list JSX, dan conditional rendering.

**Hasil akhir sesi ini:**
- Component `TaskCard` yang nerima data lewat props.
- Data tugas di file terpisah (`src/data/tasks.js`), dirender jadi daftar pakai `.map()`.
- Pesan yang muncul/hilang tergantung kondisi (conditional rendering).
- Kamu udah nemuin 3 bug di bug hunt.

**Sebelum mulai:** sesi 2 selesai — Tailwind jalan, folder `src/components/` udah ada, `npm run dev` jalan.

**Peta langkah:**

| Langkah | Isi |
|---|---|
| Konsep | Props, `key`, bahaya `0 &&` |
| 1 | Bikin `TaskCard` (nerima props) |
| 2 | Bikin data terpisah `tasks.js` |
| 3 | Render list + conditional di `App.jsx` |
| Latihan | Grid produk |
| Bug Hunt | 3 bug sengaja |

---

## Konsep

### Props = "parameter" buat component

Kalau fungsi biasa: `hitung(a, b)`. Component: `<TaskCard judul="..." selesai={true} />`. Atribut yang kamu tulis di tag itu dikumpulin jadi satu objek bernama **props**, lalu dikirim ke component.

```jsx
// Pemanggil:
<TaskCard judul="Belajar React" prioritas="tinggi" selesai={false} />

// Penerima -- destructuring props langsung di parameter:
function TaskCard({ judul, prioritas, selesai }) { ... }
```

Aturan penulisan nilai prop:
- **String** → pakai tanda kutip: `judul="Belajar React"`
- **Selain string** (angka, boolean, array, objek, variabel) → pakai kurung kurawal: `selesai={true}`, `harga={15000}`, `judul={task.judul}`

Props itu **read-only**: component gak boleh ngubah props yang diterimanya. Kalau data perlu berubah, itu urusan *state* (sesi 4).

### Kenapa `key` Penting Banget?

React pakai `key` buat "ngenalin" tiap elemen antar render, biar tau mana yang baru, mana yang dihapus, mana yang cuma pindah posisi. Kalau kamu pakai index array sebagai `key`, dan urutan/isi array berubah (hapus item, acak urutan, dsb), React bisa salah "nempelin" elemen lama ke posisi yang salah — efeknya state ketuker antar baris. Aturan gampangnya: **selalu pakai id unik yang stabil** (dari database/data, bukan posisi array).

### Kenapa `0 && <Komponen />` Bahaya?

`&&` di JS itu return operand kiri kalau falsy. `0` itu falsy, tapi `0` BUKAN `false` — jadi `0 && <p>Halo</p>` hasilnya `0`, dan React bakal nampilin angka `0` di layar (bukan nyembunyiin apa-apa). Solusinya: pastikan kondisi di kiri `&&` itu boolean asli, misal `jumlah > 0 && <p>...</p>`, bukan `jumlah && <p>...</p>`.

### 3 Cara Conditional Rendering

| Cara | Kapan dipakai | Contoh |
|---|---|---|
| `kondisi && <X />` | Tampilkan atau jangan sama sekali | `{belumSelesai.length > 0 && <p>...</p>}` |
| `kondisi ? <A /> : <B />` | Pilih salah satu dari dua | `{tasks.length === 0 ? "Kosong" : "Ada"}` |
| `if (...) return ...` | Early return sebelum JSX utama | `if (loading) return <p>Loading...</p>;` |

---

## Langkah 1 — Component dengan Props

**1a.** Bikin file `src/components/TaskCard.jsx`:

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

**Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `{ judul, prioritas = "normal", selesai }` | Ambil 3 prop. `prioritas` punya nilai default `"normal"` kalau pemanggil gak ngirim |
| `className={selesai ? "..." : "..."}` | Ternary di dalam `{}`: kalau `selesai` true → coret & abu-abu, kalau false → normal |
| `{judul}` | Tampilkan isi prop `judul` |

**1b.** Test cepat. Sementara di `App.jsx`, tulis:

```jsx
import TaskCard from "./components/TaskCard.jsx";

export default function App() {
  return (
    <main className="mx-auto max-w-xl px-6 py-8">
      <TaskCard judul="Belajar props" prioritas="tinggi" selesai={false} />
      <TaskCard judul="Sudah beres" selesai={true} />
    </main>
  );
}
```

### Cek

- [ ] Dua kartu muncul. Kartu kedua judulnya ke-coret dan abu-abu.
- [ ] Kartu kedua nampilin `NORMAL` (dari default value, karena gak ngirim `prioritas`).

---

## Langkah 2 — Data Terpisah

Data biasanya gak ditulis langsung di dalam JSX. Kita taruh di file sendiri (nantinya diganti data dari API, sesi 6).

**2a.** Bikin folder `src/data/`, lalu file `src/data/tasks.js`:

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

Perhatiin: tiap tugas punya `id` unik. Ini yang nanti dipakai sebagai `key`.

### Cek

- [ ] File `src/data/tasks.js` ada dan diakhiri `export default tasks;`.

---

## Langkah 3 — Render List + Conditional di `App.jsx`

**3a.** Ganti isi `src/App.jsx`:

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

**3b. Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `tasks.filter((t) => !t.selesai)` | Ambil cuma tugas yang belum selesai. Hasilnya array baru, `tasks` asli gak berubah |
| `tasks.map((task) => (<TaskCard ... />))` | Untuk tiap tugas, bikin satu `<TaskCard>`. Hasilnya array elemen JSX, React tampilin berurutan |
| `key={task.id}` | Penanda unik tiap kartu. WAJIB, dan harus `id`, bukan index |
| `belumSelesai.length > 0 && (...)` | Kalau ada yang belum selesai, tampilin pesan. `> 0` bikin kondisinya boolean asli |
| `` `Total: ${tasks.length} tugas` `` | Template string: sisipin nilai ke dalam teks pakai `${...}` |

### Cek

- [ ] 5 kartu muncul. Dua pertama ke-coret.
- [ ] Muncul teks kuning "Masih ada 3 tugas yang belum selesai."
- [ ] Muncul "Total: 5 tugas".
- [ ] Console gak ada warning `Each child in a list should have a unique "key" prop`.

### Eksperimen (2 menit)

Sementara ubah `const tasks = [...]` di `data/tasks.js` jadi `const tasks = [];`. Yang harus terjadi: pesan kuning hilang, muncul "Belum ada tugas.". Balikin datanya setelah tes.

---

## Latihan — Product/Task Card Grid

Bikin `src/components/ProductCard.jsx` + `src/data/products.js`. Kerjain berurutan:

1. Array data produk (minimal 6 item), tiap produk punya: `id`, `nama`, `harga`, `stok`. Cek dulu: `console.log(products)` muncul di Console.
2. Bikin `ProductCard` yang nerima props `nama`, `harga`, `stok`. Pasang 1 dulu di `App.jsx` pakai data hardcode, pastikan tampil.
3. Render semua produk pakai `.map()`, `key` pakai `id`.
4. Kalau `stok === 0`, tampilkan badge "Habis" (conditional rendering).
5. Di bawah grid, tampilkan total produk yang stoknya masih ada (`stok > 0`), pakai `&&`.

## Bug Hunt — Cari Sendiri Dulu!

Copy kode di bawah jadi component sementara (`src/BugHuntList.jsx`), import & render di `App.jsx`, coba jalanin. Ada **3 bug** sengaja ditaruh. Klik tombol "Hapus" dan "Acak Urutan", perhatiin yang aneh. Jangan buka jawaban di bawah sebelum coba sendiri.

**Cara nyari bug yang bener:** (1) jalanin dan lihat gejalanya, (2) buka Console buat baca pesan error, (3) baca kode tepat di baris yang dikomentari `BUG`, (4) tebak, baru cek jawaban.

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
3. Coba ganti `key={task.id}` jadi `key={index}` di `App.jsx` (`tasks.map((task, index) => ...)`). Kelihatan bedanya sekarang? (Belum kelihatan — baru kerasa di sesi 4 kalau ada state per item. Tulis prediksi kamu kenapa.)

## Kalau Error

| Gejala | Biasanya penyebabnya |
|---|---|
| Warning `Each child in a list should have a unique "key" prop` | `.map()` lupa `key` di elemen terluar yang di-return |
| `Objects are not valid as a React child` | Render objek utuh (`{item}`) alih-alih field-nya (`{item.nama}`) |
| Layar nampilin angka `0` yang gak diharapkan | Pakai `angka && <X/>` — ganti jadi `angka > 0 && <X/>` |
| `Cannot read properties of undefined (reading 'map')` | Variabel yang di-`.map()` `undefined` — cek import dan nama variabel |
| Kartu muncul tapi kosong | Nama prop di pemanggil beda sama yang diterima (`judul` vs `title`) |
| `.map()` hasilnya kosong padahal data ada | Arrow function pakai `{}` tanpa `return` |

Lanjut ke [04-state-dan-event.md](04-state-dan-event.md).
