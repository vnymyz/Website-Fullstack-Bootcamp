# Sesi 7 — Router (Multi Halaman)

Tujuan: bikin SPA berasa multi-halaman pakai `react-router-dom`.

**Hasil akhir sesi ini:**
- Beberapa "halaman" dengan URL beda: `/`, `/tasks`, `/tasks/1`, `/login`, `/dashboard`, dan halaman 404.
- Navbar yang sama di semua halaman (lewat `Layout` + `Outlet`) — pindah halaman tanpa reload penuh.
- `/dashboard` yang "dijaga": belum login → dilempar ke `/login` (masih pakai nilai palsu, dibikin beneran di sesi 8).

**Sebelum mulai:** sesi 6 selesai, `npm run dev` jalan. Router gak butuh json-server, jadi `npm run api` boleh mati.

**Peta langkah:**

| Langkah | Isi |
|---|---|
| Konsep | `BrowserRouter`, `Routes`/`Route`, `Link` vs `<a>` |
| 1 | Pasang router di `main.jsx` |
| 2 | Bikin halaman-halaman |
| 3 | Perbarui `Navbar`, bikin `Layout` dengan `Outlet` (satu navbar aja) |
| 4 | Daftar route di `App.jsx` |
| 5 | Protected route (stub) |

---

## Konsep Dasar

- `<BrowserRouter>` — bungkus seluruh app, ngaktifin routing.
- `<Routes>` + `<Route path="..." element={...} />` — daftar "halaman mana buat URL mana".
- `<Link to="...">` vs `<a href="...">` — `Link` gak bikin browser reload halaman (tetep SPA), `<a>` biasa bikin full reload (kayak balik ke cara PHP).

**Kenapa ini masih disebut "SPA"?** Cuma ada satu `index.html`. Pas kamu pindah dari `/` ke `/tasks`, React **nukar component** yang ditampilin dan ngubah URL di address bar (lewat History API browser) — tanpa minta halaman baru ke server.

**Bandingin sama PHP:** di PHP, `tasks.php` dan `index.php` itu dua file terpisah, dan pindah = browser minta file baru ke server. Di React, `/tasks` dan `/` itu dua **component** di satu app; "pindah halaman" = ganti component yang aktif.

---

## Langkah 1 — Setup Router

### 1a. Pasang paket

Di `learn-react/`:

```
npm install react-router-dom
```

### 1b. Bungkus `<App />` dengan `<BrowserRouter>` di `src/main.jsx`

```jsx
import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import "./index.css";
import App from "./App.jsx";

createRoot(document.getElementById("root")).render(
  <StrictMode>
    <BrowserRouter>
      <App />
    </BrowserRouter>
  </StrictMode>
);
```

**Ini wajib.** Semua component router (`Routes`, `Link`, `Navigate`, `useNavigate`, ...) cuma bisa jalan **di dalam** `<BrowserRouter>`. Kalau lupa, semua halaman blank dan Console nampilin:

```
Uncaught Error: useRoutes() may be used only in the context of a <Router> component.
```

### Cek

- [ ] Halaman lama tetap tampil normal (belum ada route apa pun, tapi gak ada error).
- [ ] Console (`F12`) bersih.

---

## Langkah 2 — Halaman-halaman

Bikin folder `src/pages/`, lalu file-file di bawah ini satu per satu.

### 2a. `src/pages/Home.jsx`

```jsx
import { Link } from "react-router-dom";

export default function Home() {
  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold">Beranda</h1>
      <Link to="/tasks" className="text-blue-600 underline">Lihat Daftar Tugas</Link>
    </div>
  );
}
```

### 2b. `src/pages/TaskList.jsx`

```jsx
import { Link } from "react-router-dom";

const tasks = [
  { id: 1, judul: "Belajar Router" },
  { id: 2, judul: "Belajar useParams" },
];

export default function TaskList() {
  return (
    <div className="p-6">
      <h1 className="text-xl font-bold">Daftar Tugas</h1>
      <ul>
        {tasks.map((t) => (
          <li key={t.id}>
            <Link to={`/tasks/${t.id}`} className="text-blue-600 underline">{t.judul}</Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
```

`` `/tasks/${t.id}` `` = URL dinamis: `/tasks/1`, `/tasks/2`.

### 2c. `src/pages/TaskDetail.jsx`

```jsx
import { useParams, useNavigate } from "react-router-dom";

export default function TaskDetail() {
  const { id } = useParams(); // ambil ":id" dari URL
  const navigate = useNavigate();

  function handleDelete() {
    // pura-pura hapus, terus redirect balik ke list
    alert(`Tugas ${id} dihapus (simulasi)`);
    navigate("/tasks");
  }

  return (
    <div className="p-6">
      <h1 className="text-xl font-bold">Detail Tugas #{id}</h1>
      <button onClick={handleDelete} className="mt-4 rounded bg-red-600 px-3 py-1 text-white">
        Hapus & Kembali ke List
      </button>
    </div>
  );
}
```

**Baca per bagian:**
- `useParams()` — hook yang balikin bagian dinamis dari URL. Kalau route-nya `tasks/:id` dan URL-nya `/tasks/2`, hasilnya `{ id: "2" }`. **Perhatian: nilainya selalu string**, bukan angka.
- `useNavigate()` — hook yang ngasih fungsi buat pindah halaman lewat kode (contoh: setelah aksi selesai). `navigate("/tasks")` = pindah ke `/tasks`.

### 2d. `src/pages/NotFound.jsx`

```jsx
export default function NotFound() {
  return <div className="p-6"><h1>404 — Halaman gak ketemu</h1></div>;
}
```

### 2e. Halaman sementara buat Protected Route (langkah 5): `Login.jsx` dan `Dashboard.jsx`

Biar demo protected route di langkah 5 bisa jalan (kalau gak ada `/login`, kamu bakal dilempar ke halaman 404), bikin dua halaman kecil ini. **Login-nya masih pura-pura** — dibikin beneran di sesi 8.

`src/pages/Login.jsx`:

```jsx
export default function Login() {
  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold">Login</h1>
      <p className="text-slate-500">(halaman sementara — form login dibikin di sesi 8)</p>
    </div>
  );
}
```

`src/pages/Dashboard.jsx`:

```jsx
export default function Dashboard() {
  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold">Dashboard</h1>
      <p>Halaman ini cuma bisa diakses kalau sudah login.</p>
    </div>
  );
}
```

### Cek

- [ ] 6 file ada di `src/pages/`: `Home`, `TaskList`, `TaskDetail`, `NotFound`, `Login`, `Dashboard`.
- [ ] Belum ada yang tampil di browser — normal, belum didaftarin di route.

---

## Langkah 3 — Navbar + Layout dengan `Outlet` (Satu Navbar Aja!)

Masalah: navbar mau muncul di **semua** halaman. Nulis navbar di tiap halaman itu boros. Solusinya: **Layout** = kerangka yang dipakai bareng, dengan "slot" buat isi halaman.

**Jebakan yang sering kejadian: navbar jadi dobel.** Dari sesi 2 kamu udah punya component `Navbar.jsx` (Beranda / Tentang / Kontak). Kalau di sesi ini kamu bikin navbar baru langsung di `Layout` **dan** `Navbar.jsx` lama juga ikut kepasang, hasilnya dua bar hitam bertumpuk di atas halaman:

```
┌──────────────────────────────────────┐
│ Beranda   Tugas                      │   <- navbar dari Layout
├──────────────────────────────────────┤
│ Portofolio Vanya   Beranda Tentang…  │   <- navbar dari Navbar.jsx
└──────────────────────────────────────┘
```

Aturannya: **cuma boleh ada satu navbar, dan dipasang di satu tempat: `Layout.jsx`.** Caranya, kita pakai `Navbar.jsx` dari sesi 2 (biar tampilannya sama), ubah linknya jadi Beranda dan Tugas, lalu `Layout` cuma manggil `<Navbar />` — **tanpa** nulis `<nav>` sendiri.

### 3a. Perbarui `Navbar.jsx`

Link di navbar harus pakai `<Link>` (bukan teks biasa) biar klik-nya pindah halaman tanpa reload. Tambah juga `import`-nya.

**File:** `src/components/Navbar.jsx`

**Isi lengkap** (ganti seluruh isinya). Bagian `BARU` = yang berubah dari versi sesi 2:

```jsx
import { Link } from "react-router-dom"; // BARU: buat link pindah halaman

// Component = fungsi JS biasa yang return JSX.
export default function Navbar() {
  return (
    <nav className="flex items-center justify-between bg-slate-900 px-6 py-4 text-white">
      <span className="text-lg font-bold">Portofolio Vanya</span>
      <ul className="flex gap-4 text-sm">
        {/* BARU: cuma dua link, dan pakai <Link> */}
        <li>
          <Link to="/">Beranda</Link>
        </li>
        <li>
          <Link to="/tasks">Tugas</Link>
        </li>
      </ul>
    </nav>
  );
}
```

**Yang berubah dari sesi 2:** (1) ada `import { Link }` di paling atas, (2) daftar `<li>` sekarang cuma **Beranda** dan **Tugas** (Tentang dan Kontak dihapus dulu), (3) isinya dibungkus `<Link to="...">`.

### 3b. Bikin `Layout.jsx`

**File:** `src/Layout.jsx`

**Isi lengkap** (file baru, atau ganti seluruh isinya kalau udah ada):

```jsx
import { Outlet } from "react-router-dom";
import Navbar from "./components/Navbar.jsx";

export default function Layout() {
  return (
    <div>
      <Navbar />
      {/* Outlet = "slot" tempat halaman anak dirender */}
      <Outlet />
    </div>
  );
}
```

**Baca per bagian:**
- `<Navbar />` — **satu-satunya** tempat navbar dipasang di seluruh app.
- `<Outlet />` — tempat React nyelipin halaman yang sesuai URL (`Home`, `TaskList`, ...). Navbar tetap, isi di `Outlet` yang berganti.
- `Layout` **gak** nulis `<nav>` sendiri dan gak meng-import `Link`.

### 3c. Pastikan `Navbar` gak dipasang di tempat lain

Cari apakah `<Navbar />` masih nyangkut di file lain. Di VS Code tekan `Ctrl+Shift+F` (cari di seluruh project), ketik `<Navbar` lalu Enter.

- [ ] **Harus muncul cuma di satu file:** `src/Layout.jsx`.
- [ ] Kalau ada di `src/App.jsx` (sisa dari sesi 2), **hapus** baris `<Navbar />` dan baris `import Navbar ...` di `App.jsx`. Di langkah 4 `App.jsx` ditulis ulang, jadi ini otomatis beres kalau kamu ngikutin langkah 4.
- [ ] Kalau ada di halaman lain (`Home.jsx`, dst), hapus juga.

### Cek langkah 3

- [ ] Belum ada yang tampil di browser kalau `App.jsx` belum diganti (langkah 4) — normal.
- [ ] `Ctrl+Shift+F` untuk `<nav` (huruf kecil): harus cuma ketemu di `src/components/Navbar.jsx`. Kalau ketemu juga di `Layout.jsx`, itu sisa navbar lama — hapus.

---

## Langkah 4 — Daftar Route di `App.jsx`

Ganti isi `src/App.jsx` (sementara tanpa protected route dulu):

```jsx
import { Routes, Route } from "react-router-dom";
import Layout from "./Layout.jsx";
import Home from "./pages/Home.jsx";
import TaskList from "./pages/TaskList.jsx";
import TaskDetail from "./pages/TaskDetail.jsx";
import NotFound from "./pages/NotFound.jsx";
import Login from "./pages/Login.jsx";
import Dashboard from "./pages/Dashboard.jsx";

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Home />} />
        <Route path="tasks" element={<TaskList />} />
        <Route path="tasks/:id" element={<TaskDetail />} />
        <Route path="login" element={<Login />} />
        <Route path="dashboard" element={<Dashboard />} />
        <Route path="*" element={<NotFound />} />
      </Route>
    </Routes>
  );
}
```

`index` = route default pas path-nya persis `/`. `path="*"` = catch-all, jalan kalau gak ada route lain yang cocok (404).

**Route bersarang:** semua `<Route>` di dalam `<Route path="/" element={<Layout />}>` dirender **di dalam `<Outlet />`-nya `Layout`**. Itulah kenapa navbar muncul di semua halaman.

**`path` tanpa `/` di depan** (`"tasks"`) = relatif terhadap route induk (`/`), jadi hasilnya `/tasks`.

### Cek

Buka satu per satu (ketik di address bar, atau klik link):

- [ ] `/` → "Beranda" + link.
- [ ] `/tasks` → daftar 2 tugas; klik salah satu → `/tasks/1` dengan "Detail Tugas #1".
- [ ] Klik "Hapus & Kembali ke List" → muncul alert, lalu balik ke `/tasks`.
- [ ] `/login`, `/dashboard` → halaman sementaranya tampil.
- [ ] `/ngasal` → "404 — Halaman gak ketemu".
- [ ] Navbar tampil di semua halaman, dan klik "Beranda"/"Tugas" **gak reload** halaman.
- [ ] **Cuma ada SATU navbar** di atas halaman (bar hitam "Portofolio Vanya" dengan link Beranda dan Tugas). Kalau ada dua bar bertumpuk, balik ke Langkah 3c.

---

## Langkah 5 — Protected Route (Stub Dulu, Token Beneran di Sesi 10)

**Ide:** sebagian halaman cuma boleh dibuka kalau user udah login. Bikin "satpam": component pembungkus yang ngecek dulu, baru nampilin halamannya atau nendang ke `/login`.

### 5a. Tulis `ProtectedRoute` di `App.jsx` — di LUAR function `App`

```jsx
import { Routes, Route, Navigate } from "react-router-dom";

function ProtectedRoute({ children }) {
  const isLoggedIn = false; // <- stub, nanti sesi 8 dari Context, sesi 10 dari token
  if (!isLoggedIn) return <Navigate to="/login" />;
  return children;
}

export default function App() {
  return (
    <Routes>
      {/* ...route lain... */}
    </Routes>
  );
}
```

**Penting:** tulis `ProtectedRoute` **di luar** `App` (di atasnya). Kalau ditulis di dalam `App`, dia dibikin ulang tiap `App` render dan state di dalamnya ke-reset — VS Code juga bakal nandain merah: `Components created during render will reset their state each time they are created`.

**Baca per bagian:**
- `{ children }` — halaman yang dibungkus (`<Dashboard />`).
- `<Navigate to="/login" />` — component yang langsung mindahin ke URL lain waktu dirender (import `Navigate` dari `react-router-dom`).
- `return children;` — kalau lolos, tampilkan halaman aslinya.
- `isLoggedIn = false` = **stub**: nilai palsu yang di-hardcode. Belum ada login beneran.

### 5b. Bungkus route `dashboard`

```jsx
<Route
  path="dashboard"
  element={
    <ProtectedRoute>
      <Dashboard />
    </ProtectedRoute>
  }
/>
```

### Cek

- [ ] Buka `/dashboard` → **otomatis dilempar ke `/login`**.
- [ ] Ubah sementara `isLoggedIn = true` → `/dashboard` sekarang kebuka. Balikin ke `false` setelah tes.
- [ ] Route lain (`/tasks`, dst) tetap kebuka tanpa login. **Cuma route yang dibungkus `<ProtectedRoute>` yang dijaga** — mau ngunci halaman lain, bungkus dengan cara yang sama.

**Ini bukan sistem login beneran.** `false` di-hardcode. Di sesi 8 kamu ganti dengan data dari Context (`user`); di sesi 10 dengan token dari backend.

---

## Latihan

1. Tambah halaman `/about`.
2. Bikin nested route: `/tasks/:id/edit` yang render form edit (boleh dummy dulu).
3. Coba klik `<Link>` vs bikin satu `<a href="/tasks">` biasa — buka DevTools Network tab, perhatiin bedanya (full page load muncul di `<a>`, gak muncul di `<Link>`).

**Petunjuk soal 3:** di Network tab, centang "Preserve log" dan lihat: klik `<Link>` gak nambah request dokumen `localhost`, sedangkan `<a>` bikin request dokumen baru + semua JS di-download ulang.

## Catatan buat Kamu

1. Jelasin ke diri sendiri: kenapa `<a href="/tasks">` bikin SPA-nya "rusak" (reload penuh), padahal secara visual hasilnya kelihatan sama kayak `<Link>`?
2. Tambah tombol "Kembali" di `TaskDetail.jsx` pakai `navigate(-1)` (mundur ke halaman sebelumnya).
3. Kenapa `useParams()` ngasih `id` berupa string? Apa yang bakal salah kalau kamu bandingin `id === 1` (angka)?

## Kalau Error

| Gejala | Biasanya penyebabnya |
|---|---|
| Semua halaman blank + `useRoutes() may be used only in the context of a <Router>` | `<BrowserRouter>` belum dipasang di `main.jsx` |
| Halaman blank, gak ada error | Lupa `<Outlet />` di `Layout`, atau `element` route salah |
| **Navbar muncul dua kali** (dua bar bertumpuk) | `<Navbar />` dipasang di dua tempat, atau `Layout` masih punya `<nav>` sendiri di samping `<Navbar />`. Sisain satu di `Layout.jsx` aja (Langkah 3) |
| Link di navbar tidak bisa diklik / halaman reload | Link di `Navbar.jsx` masih teks biasa atau `<a href>`, bukan `<Link to>` |
| `/login` malah 404 | Halaman `Login` belum didaftarin di `Routes` |
| Klik link → halaman reload penuh | Pakai `<a href>` alih-alih `<Link to>` |
| Refresh di `/tasks/1` muncul "Cannot GET" (di server produksi) | Server harus ngarahin semua URL ke `index.html` — di Vite dev otomatis, urusan deploy nanti |
| `Components created during render will reset their state` | `ProtectedRoute` ditulis di dalam `App` — pindah ke luar |
| `Navigate is not defined` | Lupa import `Navigate` dari `react-router-dom` |
| `id` dari `useParams` gak cocok dengan data | `id` string, data angka — bandingin dengan `Number(id)` atau `String(t.id)` |

Lanjut ke [08-custom-hook-dan-context.md](08-custom-hook-dan-context.md).
