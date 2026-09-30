# 03 — Layout, Navbar & Routing

**Hasil akhir:** kerangka website: navbar di atas, isi halaman di tengah, footer di bawah, dan pindah halaman `/`, `/movie/:id`, `/favorites` jalan. Isi halamannya masih placeholder.

**Sebelum mulai:** [02](02-api-layer-tmdb.md) selesai (`src/api/tmdb.js` ada).

## Konsep singkat

`Layout` = kerangka yang **selalu tampil** (navbar + footer). `<Outlet />` = lubang di tengah tempat halaman yang cocok dengan URL dimunculkan. Jadi navbar gak perlu ditulis ulang di tiap halaman.

```
<Layout>
   Navbar        <- selalu ada
   <Outlet />    <- diganti HomePage / MovieDetailPage / FavoritesPage sesuai URL
   Footer        <- selalu ada
</Layout>
```

## Langkah 1 — Hook judul tab

Judul tab browser ikut berubah sesuai halaman. Ini detail kecil yang bikin website terasa rapi.

**File: `src/hooks/useDocumentTitle.js`**

```js
import { useEffect } from "react";

// Ganti judul tab browser sesuai halaman yang lagi dibuka
export default function useDocumentTitle(title) {
  useEffect(() => {
    document.title = title ? `${title} | React Movie` : "React Movie";
  }, [title]);
}
```

## Langkah 2 — Navbar

Versi awal, belum ada badge jumlah favorit (itu ditambah di file 09).

**File: `src/components/Navbar.jsx`**

```jsx
import { Link, NavLink } from "react-router-dom";
import { Film, Heart } from "lucide-react";

const linkClass = ({ isActive }) =>
  `flex items-center gap-1.5 text-sm font-medium transition-colors ${
    isActive ? "text-white" : "text-gray-400 hover:text-white"
  }`;

export default function Navbar() {
  return (
    <header className="sticky top-0 z-40 border-b border-border bg-bg/80 backdrop-blur">
      <nav className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4">
        <Link to="/" className="flex items-center gap-2 text-lg font-bold">
          <Film size={22} className="text-accent" aria-hidden="true" />
          React<span className="text-accent">Movie</span>
        </Link>

        <div className="flex items-center gap-6">
          <NavLink to="/" end className={linkClass}>
            Beranda
          </NavLink>
          <NavLink to="/favorites" className={linkClass}>
            <Heart size={16} aria-hidden="true" />
            Favorit
          </NavLink>
        </div>
      </nav>
    </header>
  );
}
```

| Bagian | Artinya |
|---|---|
| `NavLink` | Seperti `Link`, tapi tahu apakah dia sedang aktif (`isActive`). Dipakai buat menandai menu yang sedang dibuka |
| `end` di link Beranda | Tanpa `end`, `/` dianggap aktif di **semua** halaman (karena semua URL diawali `/`) |
| `sticky top-0 z-40` | Navbar nempel di atas saat scroll, dan berada di atas konten lain |
| `bg-bg/80 backdrop-blur` | Latar 80% pekat + efek blur. Konten yang lewat di belakangnya tampak samar |
| `max-w-7xl mx-auto px-4` | Lebar konten dibatasi dan diletakkan di tengah, jadi tidak melebar di layar besar |
| `aria-hidden="true"` di ikon | Ikon hanya hiasan, jadi pembaca layar (screen reader) melewatkannya |

## Langkah 3 — Layout

**File: `src/Layout.jsx`**

```jsx
import { Outlet } from "react-router-dom";
import Navbar from "./components/Navbar.jsx";

export default function Layout() {
  return (
    <div className="flex min-h-screen flex-col">
      <Navbar />

      <main id="konten" className="mx-auto w-full max-w-7xl flex-1 px-4 py-8">
        <Outlet />
      </main>

      <footer className="border-t border-border py-6 text-center text-xs text-gray-500">
        Data film dari{" "}
        <a
          href="https://www.themoviedb.org/"
          target="_blank"
          rel="noreferrer"
          className="underline hover:text-white"
        >
          TMDB
        </a>
        . Aplikasi ini tidak disetujui atau disertifikasi oleh TMDB.
      </footer>
    </div>
  );
}
```

`min-h-screen flex flex-col` + `flex-1` di `<main>` = footer selalu menempel di dasar layar walau isi halaman sedikit.

Teks "Data film dari TMDB" bukan hiasan: syarat penggunaan API TMDB meminta kamu menyebut sumber datanya.

## Langkah 4 — Halaman placeholder

Tiga halaman sementara supaya route bisa dites. Isinya diganti di file berikutnya.

**File: `src/pages/HomePage.jsx`**

```jsx
import useDocumentTitle from "../hooks/useDocumentTitle.js";

export default function HomePage() {
  useDocumentTitle("Beranda");
  return <h1 className="text-3xl font-bold">Beranda (placeholder)</h1>;
}
```

**File: `src/pages/MovieDetailPage.jsx`**

```jsx
import { useParams } from "react-router-dom";

export default function MovieDetailPage() {
  const { id } = useParams();
  return <h1 className="text-3xl font-bold">Detail film ID: {id} (placeholder)</h1>;
}
```

**File: `src/pages/FavoritesPage.jsx`**

```jsx
import useDocumentTitle from "../hooks/useDocumentTitle.js";

export default function FavoritesPage() {
  useDocumentTitle("Favorit");
  return <h1 className="text-3xl font-bold">Favorit (placeholder)</h1>;
}
```

## Langkah 5 — Halaman 404 (belum ada component `EmptyState`)

Halaman 404 versi final memakai `EmptyState` (dibuat di file 04). Untuk sekarang pakai versi sederhana:

**File: `src/pages/NotFoundPage.jsx`**

```jsx
import { Link } from "react-router-dom";
import useDocumentTitle from "../hooks/useDocumentTitle.js";

export default function NotFoundPage() {
  useDocumentTitle("Halaman tidak ditemukan");

  return (
    <div className="py-16 text-center">
      <h1 className="text-3xl font-bold">404 - Halaman tidak ditemukan</h1>
      <p className="mt-2 text-gray-400">Alamat yang kamu buka tidak ada. Mungkin salah ketik?</p>
      <Link
        to="/"
        className="mt-6 inline-block rounded-lg bg-accent px-5 py-2 text-sm font-semibold transition hover:opacity-90"
      >
        Kembali ke Beranda
      </Link>
    </div>
  );
}
```

## Langkah 6 — Daftarkan route di `App.jsx`

**File: `src/App.jsx`** (ganti seluruh isi, buang kode test dari file 02)

```jsx
import { Routes, Route } from "react-router-dom";
import Layout from "./Layout.jsx";
import HomePage from "./pages/HomePage.jsx";
import MovieDetailPage from "./pages/MovieDetailPage.jsx";
import FavoritesPage from "./pages/FavoritesPage.jsx";
import NotFoundPage from "./pages/NotFoundPage.jsx";

export default function App() {
  return (
    <Routes>
      <Route element={<Layout />}>
        <Route path="/" element={<HomePage />} />
        <Route path="/movie/:id" element={<MovieDetailPage />} />
        <Route path="/favorites" element={<FavoritesPage />} />
        <Route path="*" element={<NotFoundPage />} />
      </Route>
    </Routes>
  );
}
```

| Bagian | Artinya |
|---|---|
| `<Route element={<Layout />}>` tanpa `path` | "Route pembungkus". Semua route di dalamnya tampil di dalam `<Outlet />` milik `Layout` |
| `:id` di `/movie/:id` | Bagian URL yang berubah-ubah. Dibaca dengan `useParams()` |
| `path="*"` | Menangkap semua URL yang tidak cocok dengan route lain (404) |

## Langkah 7 — Pasang `BrowserRouter` di `main.jsx`

**File: `src/main.jsx`** (ganti seluruh isi)

```jsx
import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./App.jsx";
import "./index.css";

createRoot(document.getElementById("root")).render(
  <StrictMode>
    <BrowserRouter>
      <App />
    </BrowserRouter>
  </StrictMode>,
);
```

## Cek

- [ ] `/` → navbar + "Beranda (placeholder)". Tab browser: "Beranda | React Movie".
- [ ] Klik "Favorit" → URL jadi `/favorites`, menu Favorit terang, Beranda meredup.
- [ ] Ketik manual `/movie/550` → tulisan "Detail film ID: 550".
- [ ] Ketik `/asal-asalan` → halaman 404 dengan tombol kembali.
- [ ] Footer selalu di dasar layar. Scroll (perkecil jendela) → navbar tetap menempel di atas.
- [ ] Console bersih dari error.

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| `useRoutes() may be used only in the context of a <Router>` | `BrowserRouter` belum dipasang | Cek `main.jsx` (Langkah 7) |
| Halaman blank, tidak ada error | `<Outlet />` lupa dipasang di `Layout` | Tambahkan di dalam `<main>` |
| Menu Beranda terang di semua halaman | Lupa atribut `end` | `<NavLink to="/" end ...>` |
| Refresh di `/favorites` jadi "Cannot GET" | Dev server salah, atau nanti di hosting tanpa pengaturan SPA | Di `npm run dev` tidak terjadi. Di hosting, atur "semua URL ke index.html" (dibahas saat deploy nanti) |
| Warna `bg-bg` tidak terbaca | `@theme` di `index.css` belum benar | Ulang Langkah 5 file 01 |
| `Failed to resolve import "./pages/..."` | Nama file / huruf besar-kecil beda | Samakan persis, termasuk ekstensi `.jsx` |

Lanjut ke [04-movie-card-dan-grid.md](04-movie-card-dan-grid.md).
