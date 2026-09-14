# Sesi 7 — Router (Multi Halaman)

Tujuan: bikin SPA berasa multi-halaman pakai `react-router-dom`.

## Install

```
npm install react-router-dom
```

## Konsep Dasar

- `<BrowserRouter>` — bungkus seluruh app, ngaktifin routing.
- `<Routes>` + `<Route path="..." element={...} />` — daftar "halaman mana buat URL mana".
- `<Link to="...">` vs `<a href="...">` — `Link` gak bikin browser reload halaman (tetep SPA), `<a>` biasa bikin full reload (kayak balik ke cara PHP).

## Langkah 1 — Setup Router

`src/main.jsx`:

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

## Langkah 2 — Halaman-halaman

Bikin folder `src/pages/`, lalu:

`src/pages/Home.jsx`:
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

`src/pages/TaskList.jsx`:
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

`src/pages/TaskDetail.jsx`:
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

`src/pages/NotFound.jsx`:
```jsx
export default function NotFound() {
  return <div className="p-6"><h1>404 — Halaman gak ketemu</h1></div>;
}
```

## Langkah 3 — Layout dengan `Outlet`

`src/Layout.jsx`:
```jsx
import { Outlet, Link } from "react-router-dom";

export default function Layout() {
  return (
    <div>
      <nav className="flex gap-4 bg-slate-900 p-4 text-white">
        <Link to="/">Beranda</Link>
        <Link to="/tasks">Tugas</Link>
      </nav>
      {/* Outlet = "slot" tempat halaman anak dirender */}
      <Outlet />
    </div>
  );
}
```

## Langkah 4 — Daftar Route di `App.jsx`

```jsx
import { Routes, Route } from "react-router-dom";
import Layout from "./Layout.jsx";
import Home from "./pages/Home.jsx";
import TaskList from "./pages/TaskList.jsx";
import TaskDetail from "./pages/TaskDetail.jsx";
import NotFound from "./pages/NotFound.jsx";

export default function App() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Home />} />
        <Route path="tasks" element={<TaskList />} />
        <Route path="tasks/:id" element={<TaskDetail />} />
        <Route path="*" element={<NotFound />} />
      </Route>
    </Routes>
  );
}
```

`index` = route default pas path-nya persis `/`. `path="*"` = catch-all, jalan kalau gak ada route lain yang cocok (404).

## Protected Route (Stub Dulu, Token Beneran di Sesi 10)

```jsx
function ProtectedRoute({ children }) {
  const isLoggedIn = false; // <- stub, nanti sesi 10 ini beneran cek token
  if (!isLoggedIn) return <Navigate to="/login" />;
  return children;
}

// pemakaian:
<Route path="dashboard" element={<ProtectedRoute><Dashboard /></ProtectedRoute>} />
```

(Import `Navigate` dari `react-router-dom`.)

## Latihan

1. Tambah halaman `/about`.
2. Bikin nested route: `/tasks/:id/edit` yang render form edit (boleh dummy dulu).
3. Coba klik `<Link>` vs bikin satu `<a href="/tasks">` biasa — buka DevTools Network tab, perhatiin bedanya (full page load muncul di `<a>`, gak muncul di `<Link>`).

## Catatan buat Kamu

1. Jelasin ke diri sendiri: kenapa `<a href="/tasks">` bikin SPA-nya "rusak" (reload penuh), padahal secara visual hasilnya kelihatan sama kayak `<Link>`?
2. Tambah tombol "Kembali" di `TaskDetail.jsx` pakai `navigate(-1)` (mundur ke halaman sebelumnya).

Lanjut ke [08-custom-hook-dan-context.md](08-custom-hook-dan-context.md).
