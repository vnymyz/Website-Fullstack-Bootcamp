# Sesi 4 — State & Event (Paling Penting!)

Ini sesi paling krusial di seluruh materi React. Jangan buru-buru, pahami bener-bener sebelum lanjut.

**Hasil akhir sesi ini:**
- Component `Counter` dan `TodoList` yang beneran interaktif (tambah, toggle, hapus, filter).
- Kamu ngerti kenapa state harus diubah lewat `setState` dengan nilai **baru**, bukan dimodifikasi langsung.
- Kamu udah nemuin 2 bug (mutasi state + stale closure) sendiri.
- Kamu lulus checkpoint closed-book "Daftar Belanja".

**Sebelum mulai:** sesi 3 selesai, kamu paham props, `.map()`, dan `key`.

**Peta langkah:**

| Langkah | Isi |
|---|---|
| Konsep | Apa itu state, mutasi vs immutable, `onClick={fn}`, batching, derived state |
| 1 | `Counter` — state paling sederhana |
| 2 | `TodoList` — state array + immutability + derived state |
| 3 | Pasang di `App.jsx` |
| Bug Hunt | 2 bug tentang state |
| Checkpoint | Daftar Belanja, closed-book |

---

## Konsep Dasar

### Apa itu State?

**Props** = data dari luar (dikasih pemanggil, read-only). **State** = data milik component itu sendiri yang **bisa berubah** seiring waktu (angka counter, isi input, daftar todo).

Kalau state berubah, React **render ulang** component itu (jalanin fungsinya lagi dari atas) dan update tampilan. Variabel biasa (`let count = 0`) gak punya kemampuan ini: kamu ubah nilainya, tapi React gak tau, jadi tampilan gak berubah.

```jsx
const [count, setCount] = useState(0);
//     ↑        ↑                  ↑
//  nilai      fungsi buat       nilai awal
//  sekarang   ngubah nilainya
```

### Kenapa Gak Boleh `state.x = y` Langsung?

React nentuin "perlu re-render atau nggak" dengan ngecek **apakah reference-nya berubah** (bukan ngecek isinya satu-satu, itu mahal). Kalau kamu ubah objek/array yang lama secara langsung (mutasi), reference-nya TETAP SAMA — React nganggep gak ada yang berubah, walau isinya sebenarnya udah beda. Makanya kita SELALU bikin salinan baru:

```js
// Array: pakai spread, .map(), .filter() -- BUKAN .push(), .splice()
setItems([...items, itemBaru]);
setItems(items.filter(i => i.id !== id));
setItems(items.map(i => i.id === id ? { ...i, done: true } : i));

// Objek: pakai spread
setUser({ ...user, name: "baru" });
```

**Contekan: mana yang aman, mana yang mutasi**

| Mau ngapain | Aman (bikin baru) | JANGAN (mutasi) |
|---|---|---|
| Tambah ke array | `[...arr, baru]` | `arr.push(baru)` |
| Hapus dari array | `arr.filter(...)` | `arr.splice(...)` |
| Ubah satu item | `arr.map(... ? {...i, x: 1} : i)` | `arr[0].x = 1` |
| Ubah properti objek | `{ ...obj, x: 1 }` | `obj.x = 1` |
| Urutkan | `[...arr].sort(...)` | `arr.sort(...)` (ngubah aslinya!) |

### `onClick={fn}` vs `onClick={fn()}`

```jsx
<button onClick={naikkan}>Tambah</button>   {/* BENAR: kasih referensi fungsi */}
<button onClick={naikkan()}>Tambah</button> {/* SALAH: fungsi kepanggil LANGSUNG saat render */}
```

Bayangin `onClick={naikkan}` itu kayak ngasih nomor telepon (dipanggil NANTI pas dibutuhkan). `onClick={naikkan()}` itu kayak nelfon LANGSUNG SEKARANG, terus yang kamu kasih ke `onClick` cuma HASIL dari telfon itu (biasanya `undefined`).

Kalau fungsinya butuh argumen, bungkus pakai arrow function:

```jsx
<button onClick={() => hapus(todo.id)}>Hapus</button>
```

### Kenapa State Update "Async"/Batched?

```js
setCount(count + 1);
setCount(count + 1);
// count TETAP nambah cuma 1, bukan 2!
```

Karena `count` di kedua baris itu masih nilai yang SAMA (nilai pas function ini mulai jalan) — React belum sempet re-render di antara baris 1 dan 2. Solusinya, pakai **functional update** kalau update berikutnya butuh nilai TERBARU:

```js
setCount(c => c + 1);
setCount(c => c + 1);
// Sekarang beneran nambah 2, karena tiap `c` ambil nilai TERBARU
```

### Kenapa Bikin "Derived State" (Bukan State Terpisah)?

Kalau ada nilai yang BISA dihitung dari state lain (contoh: `todos.filter(t => !t.done).length`), jangan simpan itu di `useState` sendiri. Kalau disimpan terpisah, kamu harus inget update KEDUA state itu bareng-bareng tiap ada perubahan — gampang lupa, dan gampang jadi gak sinkron ("basi"). Cukup hitung langsung tiap render.

---

## Langkah 1 — Counter

Mulai dari yang paling kecil biar polanya kelihatan jelas.

**1a.** Bikin `src/components/Counter.jsx`:

```jsx
import { useState } from "react";

export default function Counter() {
  const [count, setCount] = useState(0);
  // useState(0) -> nilai awal 0. Return array 2 elemen:
  // [nilai_sekarang, fungsi_buat_ubah_nilai].

  function naikkan() {
    setCount(count + 1);
  }

  return (
    <div className="rounded-lg border p-4 text-center">
      <p className="mb-2 text-3xl font-bold">{count}</p>
      <button onClick={naikkan} className="rounded bg-blue-600 px-4 py-1 text-white">
        Tambah
      </button>
    </div>
  );
}
```

**1b. Baca per bagian:**

| Bagian | Artinya |
|---|---|
| `import { useState } from "react"` | Ambil hook `useState`. Pakai kurung kurawal karena bukan `export default` |
| `const [count, setCount] = useState(0)` | Bikin state `count` mulai dari 0 |
| `function naikkan() { setCount(count + 1) }` | Panggil `setCount` dengan nilai **baru**. Setelah ini React render ulang |
| `{count}` | Tampilin nilai state sekarang |
| `onClick={naikkan}` | Klik tombol → jalanin `naikkan` (referensi fungsi, tanpa `()`) |

**1c.** Test cepat: pasang di `App.jsx` (`<Counter />`), simpan.

### Cek

- [ ] Angka 0 tampil. Klik "Tambah" → jadi 1, 2, 3, ... tanpa reload halaman.
- [ ] Refresh halaman → balik ke 0 (state gak disimpan — itu normal; penyimpanan permanen baru sesi 6 dan 8).

**Eksperimen 1 menit:** ganti `onClick={naikkan}` jadi `onClick={naikkan()}`. Apa yang terjadi? (Petunjuk: lihat Console — error `Too many re-renders`.) Balikin lagi setelah tes.

---

## Langkah 2 — Todo List (Immutability + Derived State)

Ini component yang lebih besar. **Bangun bertahap** (kode lengkap ada di 2f sebagai pembanding).

Bikin `src/components/TodoList.jsx`.

### 2a. State awal + tampilan list statis

```jsx
import { useState } from "react";

export default function TodoList() {
  const [todos, setTodos] = useState([
    { id: 1, text: "Belajar useState", done: false },
    { id: 2, text: "Belajar immutability", done: false },
  ]);

  return (
    <div className="mx-auto max-w-md space-y-4 p-4">
      <ul className="space-y-1">
        {todos.map((todo) => (
          <li key={todo.id} className="rounded border px-2 py-1">
            {todo.text}
          </li>
        ))}
      </ul>
    </div>
  );
}
```

**Cek 2a:** pasang `<TodoList />` di `App.jsx` → 2 todo tampil.

### 2b. Tambah todo (input controlled + immutable add)

Tambah state input dan fungsi `tambah`:

```jsx
const [input, setInput] = useState("");

function tambah() {
  if (!input.trim()) return;
  // IMMUTABLE UPDATE: array baru pakai spread, bukan todos.push(...)
  setTodos([...todos, { id: Date.now(), text: input, done: false }]);
  setInput("");
}
```

JSX-nya (taruh di atas `<ul>`):

```jsx
<div className="flex gap-2">
  <input
    value={input}
    onChange={(e) => setInput(e.target.value)}
    placeholder="Tugas baru..."
    className="flex-1 rounded border px-2 py-1"
  />
  <button onClick={tambah} className="rounded bg-blue-600 px-3 py-1 text-white">
    Tambah
  </button>
</div>
```

**Baca per bagian:**
- `value={input}` + `onChange={(e) => setInput(e.target.value)}` — input "dikontrol" state (dibahas penuh di sesi 5). Tiap ketik, `input` ikut update.
- `if (!input.trim()) return;` — kalau kosong/spasi doang, jangan tambah.
- `[...todos, {...}]` — array **baru**: semua todo lama + satu item baru di ujung.
- `Date.now()` — angka milidetik sekarang, dipakai sebagai `id` unik sementara (nanti id asli dari database).
- `setInput("")` — kosongkan kolom input setelah tambah.

**Cek 2b:**
- [ ] Ketik teks → Tambah → muncul di list, kolom input kosong lagi.
- [ ] Klik Tambah dengan input kosong → gak terjadi apa-apa.

### 2c. Toggle selesai (`.map` immutable)

```jsx
function toggle(id) {
  setTodos(todos.map((t) => (t.id === id ? { ...t, done: !t.done } : t)));
}
```

Di `<li>`, bungkus teks jadi `<span>` yang bisa diklik:

```jsx
<span
  onClick={() => toggle(todo.id)}
  className={todo.done ? "cursor-pointer line-through text-slate-400" : "cursor-pointer"}
>
  {todo.text}
</span>
```

**Baca per bagian:** `.map` jalan ke tiap todo. Kalau `id`-nya cocok → bikin objek **baru** `{ ...t, done: !t.done }` (salin semua properti `t`, tapi `done` dibalik). Kalau gak cocok → kembalikan `t` apa adanya. Hasilnya array baru.

**Cek 2c:** klik teks todo → ke-coret; klik lagi → normal.

### 2d. Hapus todo (`.filter`)

```jsx
function hapus(id) {
  setTodos(todos.filter((t) => t.id !== id));
}
```

Tombol di `<li>` (sejajarkan dengan flex):

```jsx
<li key={todo.id} className="flex items-center justify-between rounded border px-2 py-1">
  <span ...>{todo.text}</span>
  <button onClick={() => hapus(todo.id)} className="text-red-500 text-sm">Hapus</button>
</li>
```

**Cek 2d:** klik Hapus → item hilang.

### 2e. Filter (derived state)

Tambah state `filter` dan hitung `visibleTodos` **tiap render** (bukan disimpan di state):

```jsx
const [filter, setFilter] = useState("semua"); // "semua" | "aktif" | "selesai"

// DERIVED STATE: dihitung tiap render, gak disimpan sebagai state sendiri.
const visibleTodos = todos.filter((t) => {
  if (filter === "aktif") return !t.done;
  if (filter === "selesai") return t.done;
  return true;
});
```

JSX tombol filter (di atas `<ul>`), dan **ganti `todos.map` jadi `visibleTodos.map`**:

```jsx
<div className="flex gap-2 text-sm">
  {["semua", "aktif", "selesai"].map((f) => (
    <button
      key={f}
      onClick={() => setFilter(f)}
      className={filter === f ? "font-bold underline" : "text-slate-500"}
    >
      {f}
    </button>
  ))}
</div>
```

**Kenapa `visibleTodos` bukan `useState`?** Karena bisa dihitung dari `todos` + `filter`. Kalau disimpan sendiri, tiap kamu toggle/hapus/tambah kamu harus inget update dua state — dan pasti suatu saat lupa.

**Cek 2e:** centang satu todo → klik "aktif" (todo itu hilang dari list), "selesai" (cuma dia), "semua" (dua-duanya).

### 2f. Kode lengkap `TodoList.jsx` (buat dicocokin)

```jsx
import { useState } from "react";

export default function TodoList() {
  const [todos, setTodos] = useState([
    { id: 1, text: "Belajar useState", done: false },
    { id: 2, text: "Belajar immutability", done: false },
  ]);
  const [input, setInput] = useState("");
  const [filter, setFilter] = useState("semua"); // "semua" | "aktif" | "selesai"

  function tambah() {
    if (!input.trim()) return;
    // IMMUTABLE UPDATE: array baru pakai spread, bukan todos.push(...)
    setTodos([...todos, { id: Date.now(), text: input, done: false }]);
    setInput("");
  }

  function toggle(id) {
    setTodos(todos.map((t) => (t.id === id ? { ...t, done: !t.done } : t)));
  }

  function hapus(id) {
    setTodos(todos.filter((t) => t.id !== id));
  }

  // DERIVED STATE: dihitung tiap render, gak disimpan sebagai state sendiri.
  const visibleTodos = todos.filter((t) => {
    if (filter === "aktif") return !t.done;
    if (filter === "selesai") return t.done;
    return true;
  });

  return (
    <div className="mx-auto max-w-md space-y-4 p-4">
      <div className="flex gap-2">
        <input
          value={input}
          onChange={(e) => setInput(e.target.value)}
          placeholder="Tugas baru..."
          className="flex-1 rounded border px-2 py-1"
        />
        <button onClick={tambah} className="rounded bg-blue-600 px-3 py-1 text-white">
          Tambah
        </button>
      </div>

      <div className="flex gap-2 text-sm">
        {["semua", "aktif", "selesai"].map((f) => (
          <button
            key={f}
            onClick={() => setFilter(f)}
            className={filter === f ? "font-bold underline" : "text-slate-500"}
          >
            {f}
          </button>
        ))}
      </div>

      <ul className="space-y-1">
        {visibleTodos.map((todo) => (
          <li key={todo.id} className="flex items-center justify-between rounded border px-2 py-1">
            <span
              onClick={() => toggle(todo.id)}
              className={todo.done ? "cursor-pointer line-through text-slate-400" : "cursor-pointer"}
            >
              {todo.text}
            </span>
            <button onClick={() => hapus(todo.id)} className="text-red-500 text-sm">
              Hapus
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
}
```

---

## Langkah 3 — Pasang di `App.jsx`

```jsx
import Counter from "./components/Counter.jsx";
import TodoList from "./components/TodoList.jsx";

export default function App() {
  return (
    <main className="mx-auto max-w-xl space-y-8 px-6 py-8">
      <h1 className="text-2xl font-bold">State & Event</h1>
      <Counter />
      <TodoList />
    </main>
  );
}
```

### Cek

- [ ] Counter dan TodoList tampil berdua, dan **state-nya terpisah**: klik Tambah di Counter gak ngubah todo, dan sebaliknya. (Tiap component punya state sendiri.)

---

## Bug Hunt — Cari Sendiri Dulu!

Copy jadi component sementara (`src/BugHuntState.jsx`), render di `App.jsx`. Ikuti instruksi di komentar `BUG`, catat gejalanya dulu, baru buka jawaban.

```jsx
import { useState } from "react";

export default function BugHuntState() {
  const [todos, setTodos] = useState([
    { id: 1, text: "Nyuci baju", done: false },
    { id: 2, text: "Beli galon", done: false },
  ]);
  const [count, setCount] = useState(0);

  // BUG 1: klik salah satu todo, apa tampilan langsung berubah pas diklik PERTAMA KALI?
  function toggleTodo(id) {
    const todo = todos.find((t) => t.id === id);
    todo.done = !todo.done;
    setTodos(todos);
  }

  // BUG 2: klik "Tunda +1" 3x cepat berturut-turut, tunggu 3 detik.
  // Harusnya count nambah 3, tapi count cuma nambah 1.
  function tundaTambahSatu() {
    setTimeout(() => {
      setCount(count + 1);
    }, 3000);
  }

  return (
    <div className="mx-auto max-w-md space-y-4 p-4">
      <ul className="space-y-1">
        {todos.map((todo) => (
          <li
            key={todo.id}
            onClick={() => toggleTodo(todo.id)}
            className={todo.done ? "cursor-pointer line-through" : "cursor-pointer"}
          >
            {todo.text}
          </li>
        ))}
      </ul>
      <div>
        <p className="text-2xl">{count}</p>
        <button onClick={tundaTambahSatu} className="rounded bg-slate-800 px-3 py-1 text-white">
          Tunda +1 (3 detik)
        </button>
      </div>
    </div>
  );
}
```

<details>
<summary>Klik buat lihat jawaban (coba sendiri dulu!)</summary>

**Bug 1: Mutasi state langsung**

```jsx
// Salah
const todo = todos.find((t) => t.id === id);
todo.done = !todo.done;
setTodos(todos);
// Benar
setTodos(todos.map((t) => (t.id === id ? { ...t, done: !t.done } : t)));
```
Kenapa masalah: `todo.done = ...` langsung ubah objek DI DALAM array lama (mutasi). `setTodos(todos)` dipanggil dengan array yang reference-nya SAMA PERSIS. React ngecek pakai `===`, jadi mikir gak ada perubahan.

**Bug 2: Stale closure di `setTimeout`**

```jsx
// Salah
setTimeout(() => { setCount(count + 1); }, 3000);
// Benar
setTimeout(() => { setCount((c) => c + 1); }, 3000);
```
Kenapa masalah: tiap `tundaTambahSatu` dipanggil, dia "menutup" (closure) nilai `count` SAAT ITU. Klik 3x cepat = 3 pemanggilan, semua "inget" `count` yang sama, jadi ketiganya nge-set jadi `1`, bukan `3`. Functional update (`c => c + 1`) ambil nilai TERBARU pas update itu beneran jalan.

**Aturan praktis:** update state itu selalu bikin nilai/objek/array BARU. Kalau update berikutnya bergantung ke nilai sebelumnya (terutama di `setTimeout`/event beruntun), pakai `setX(prev => ...)`.

</details>

## Checkpoint Closed-Book

**Aturan: gak boleh liat kode sebelumnya, gak boleh tanya AI, gak boleh liat catatan.** Boleh liat dokumentasi resmi React (react.dev) kalau lupa nama fungsi/hook.

Dari `App.jsx` yang dikosongin sementara (atau branch/copy terpisah), bikin halaman "Daftar Belanja":

1. Input teks + tombol "Tambah" — nambah item baru ke list.
2. Tiap item bisa di-klik buat toggle "sudah dibeli" (efek visual, misal line-through).
3. Tiap item punya tombol "Hapus".
4. Tampilkan teks "X dari Y item sudah dibeli" (dihitung dari data, bukan di-hardcode).

Yang dinilai: pakai `useState` dengan benar, update array immutable, `key` bener (id bukan index), dan paham KENAPA kodenya begitu kalau ditanya tutor.

**Strategi ngerjain (biar gak buntu):** (1) tulis dulu state apa aja yang dibutuhin di atas kertas (`items`, `input`), (2) tampilkan list statis, (3) tambah satu fitur per kali dan tes di browser sebelum lanjut, (4) baru bagian "X dari Y" — ini derived state, jangan bikin `useState` buat itu.

## Catatan buat Kamu

1. Klik tombol Tambah di Counter 2x super cepat. Ganti `naikkan()` jadi `setCount(count+1); setCount(count+1);` — hasilnya nambah 2 atau 1? Kenapa?
2. Coba ganti `toggle()` di `TodoList` jadi versi "salah" (mutasi langsung) — lihat gejalanya sebelum balikin lagi ke versi benar.
3. Tambah tombol "Hapus yang udah selesai" di `TodoList` — pakai `.filter()`.

## Kalau Error

| Gejala | Biasanya penyebabnya |
|---|---|
| `Too many re-renders` | Nulis `onClick={fungsi()}` (kepanggil saat render) atau manggil `setState` langsung di badan component |
| Klik tapi tampilan gak berubah | State dimutasi langsung (`todos.push`, `todo.done = ...`) alih-alih bikin salinan baru |
| Input gak bisa diketik | Punya `value={...}` tapi lupa `onChange` |
| `setX is not a function` | Urutan destructuring kebalik: harus `[nilai, setNilai]` |
| Hook dipanggil di dalam `if`/loop | Pindahin ke top level function component (aturan hooks) |
| Counter nambah 1 padahal dipanggil 2x | Batching — pakai `setCount(c => c + 1)` |
| Todo baru id-nya sama | Jangan pakai `todos.length + 1` sebagai id (bentrok setelah hapus); pakai `Date.now()` atau id dari server |

Lanjut ke [05-form-dan-controlled-input.md](05-form-dan-controlled-input.md).
