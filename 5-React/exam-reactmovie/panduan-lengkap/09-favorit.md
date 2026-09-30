# 09 — Fitur Favorit (Love / Like Film)

**Hasil akhir:** ikon hati di setiap kartu dan di halaman detail. Klik → film tersimpan. Halaman `/favorites` menampilkan semua film yang di-love, dan navbar menunjukkan jumlahnya. Data tetap ada walau browser di-refresh atau ditutup. **Tanpa login, tanpa database.**

**Sebelum mulai:** [08](08-detail-film.md) selesai.

## Konsep singkat

Tanpa login, di mana favorit disimpan? Di **`localStorage`** browser. Data hidup di komputer user sendiri.

```
klik ♥ di MovieCard ─┐
klik ♥ di Detail ────┼─> toggleFavorite(movie) ─> FavoritesContext ─> useLocalStorage ─> localStorage["favorites"]
                     │                                   │
                     │                                   └─> semua component yang memakai useFavorites() ikut render ulang
                     └─ Navbar (badge angka), FavoritesPage (daftar), semua tombol ♥ (isi/kosong)
```

Kenapa perlu **Context**? Tombol hati ada di kartu, halaman detail, dan navbar butuh jumlahnya, padahal letaknya di cabang component yang berbeda-beda. Tanpa Context kamu harus mengoper `favorites` lewat props berlapis-lapis (*prop drilling*). Context = papan pengumuman yang bisa dibaca dari mana saja (sesi 8).

Data yang disimpan **secukupnya**: `id, title, poster_path, vote_average, release_date`. Cukup untuk menggambar `MovieCard` di halaman Favorit tanpa fetch ulang.

Batasan yang perlu kamu tahu (sampaikan saat ujian lisan): `localStorage` per browser dan per perangkat. Favorit di laptop tidak muncul di HP. Kalau butuh lintas perangkat, perlu login dan database, itu materi tahap backend.

## Langkah 1 — Hook `useLocalStorage`

**File: `src/hooks/useLocalStorage.js`**

```js
import { useState } from "react";

// useState yang nilainya otomatis disimpan di localStorage,
// jadi gak hilang waktu halaman di-refresh.
export default function useLocalStorage(key, initialValue) {
  const [value, setValue] = useState(() => {
    try {
      const saved = localStorage.getItem(key);
      return saved ? JSON.parse(saved) : initialValue;
    } catch {
      return initialValue; // data rusak / localStorage diblokir
    }
  });

  function setStoredValue(newValue) {
    setValue(newValue);
    try {
      localStorage.setItem(key, JSON.stringify(newValue));
    } catch {
      // penyimpanan penuh / diblokir: abaikan, app tetap jalan
    }
  }

  return [value, setStoredValue];
}
```

| Bagian | Artinya |
|---|---|
| `useState(() => {...})` | *Lazy initial state*: fungsi ini hanya dijalankan **sekali** saat pertama render, bukan tiap render |
| `JSON.parse` / `JSON.stringify` | `localStorage` hanya bisa menyimpan **teks**. Array/objek diubah jadi teks dulu, lalu dikembalikan |
| `try/catch` | Kalau user memblokir penyimpanan atau datanya rusak, aplikasi tidak crash |

## Langkah 2 — Context favorit

**File: `src/context/FavoritesContext.jsx`**

```jsx
import { createContext, useContext } from "react";
import useLocalStorage from "../hooks/useLocalStorage.js";

const FavoritesContext = createContext(null);

export function FavoritesProvider({ children }) {
  const [favorites, setFavorites] = useLocalStorage("favorites", []);

  function isFavorite(id) {
    return favorites.some((f) => f.id === id);
  }

  function toggleFavorite(movie) {
    if (isFavorite(movie.id)) {
      setFavorites(favorites.filter((f) => f.id !== movie.id));
    } else {
      // Simpan data secukupnya aja (bukan seluruh objek dari TMDB)
      const { id, title, poster_path, vote_average, release_date } = movie;
      setFavorites([...favorites, { id, title, poster_path, vote_average, release_date }]);
    }
  }

  return (
    <FavoritesContext.Provider value={{ favorites, isFavorite, toggleFavorite }}>
      {children}
    </FavoritesContext.Provider>
  );
}

// Satu file boleh ngekspor Provider + hook-nya; ESLint fast-refresh protes, kita matikan khusus baris ini
// eslint-disable-next-line react-refresh/only-export-components
export function useFavorites() {
  const ctx = useContext(FavoritesContext);
  if (!ctx) throw new Error("useFavorites harus dipakai di dalam <FavoritesProvider>");
  return ctx;
}
```

| Bagian | Artinya |
|---|---|
| `useLocalStorage("favorites", [])` | Key `"favorites"`, nilai awal array kosong. Buka DevTools → **Application** → **Local Storage** untuk melihatnya |
| `favorites.some(...)` | `true` kalau ada film dengan `id` itu |
| `filter(f => f.id !== movie.id)` | Menghapus dengan membuat array **baru** tanpa film tersebut |
| `[...favorites, {...}]` | Menambah dengan membuat array **baru** (immutability, sesi 4). Jangan pernah `favorites.push(...)` |
| Destructuring `{ id, title, ... } = movie` | Mengambil hanya 5 field. Objek film dari TMDB punya belasan field yang tidak kita butuhkan |
| `throw new Error` di `useFavorites` | Kalau lupa membungkus dengan Provider, kamu dapat pesan jelas, bukan error aneh `Cannot destructure ... of null` |

## Langkah 3 — Tombol hati

**File: `src/components/FavoriteButton.jsx`**

```jsx
import { Heart } from "lucide-react";
import { useFavorites } from "../context/FavoritesContext.jsx";

// showLabel = true -> tombol lebar dengan teks (dipakai di halaman detail)
export default function FavoriteButton({ movie, showLabel = false, className = "" }) {
  const { isFavorite, toggleFavorite } = useFavorites();
  const active = isFavorite(movie.id);

  return (
    <button
      type="button"
      onClick={() => toggleFavorite(movie)}
      aria-pressed={active}
      aria-label={active ? `Hapus ${movie.title} dari favorit` : `Tambah ${movie.title} ke favorit`}
      className={`flex items-center gap-2 rounded-full bg-black/60 backdrop-blur transition hover:bg-black/80 focus-visible:outline-2 focus-visible:outline-accent ${
        showLabel ? "px-5 py-2.5 text-sm font-semibold" : "p-2"
      } ${className}`}
    >
      <Heart
        size={18}
        fill={active ? "currentColor" : "none"}
        className={active ? "text-accent" : "text-white"}
        aria-hidden="true"
      />
      {showLabel && (active ? "Tersimpan di Favorit" : "Tambah ke Favorit")}
    </button>
  );
}
```

`aria-pressed` memberi tahu pembaca layar bahwa ini tombol *toggle* (tekan = nyala, tekan lagi = mati). `aria-label` wajib karena tombol ikon tidak punya teks.

## Langkah 4 — Pasang hati di `MovieCard`

**File: `src/components/MovieCard.jsx`** (ganti seluruh isi)

```jsx
import { Link } from "react-router-dom";
import { Film } from "lucide-react";
import RatingBadge from "./RatingBadge.jsx";
import FavoriteButton from "./FavoriteButton.jsx"; // [BARU]
import { imageUrl } from "../api/tmdb.js";
import { formatYear } from "../utils/format.js";

export default function MovieCard({ movie }) {
  const poster = imageUrl(movie.poster_path);

  return (
    <article className="relative">
      <Link
        to={`/movie/${movie.id}`}
        className="group block overflow-hidden rounded-xl border border-border bg-surface transition hover:-translate-y-1 hover:border-accent/60 focus-visible:outline-2 focus-visible:outline-accent"
      >
        <div className="relative aspect-[2/3] bg-border">
          {poster ? (
            <img
              src={poster}
              alt={`Poster ${movie.title}`}
              loading="lazy"
              className="h-full w-full object-cover transition duration-300 group-hover:scale-105"
            />
          ) : (
            <div className="flex h-full items-center justify-center text-gray-500">
              <Film size={40} aria-hidden="true" />
            </div>
          )}
          <div className="absolute left-2 top-2">
            <RatingBadge value={movie.vote_average} />
          </div>
        </div>
        <div className="p-3">
          <h3 className="line-clamp-1 text-sm font-semibold">{movie.title}</h3>
          <p className="text-xs text-gray-400">{formatYear(movie.release_date)}</p>
        </div>
      </Link>

      {/* [BARU] Tombol hati SAUDARA dari Link (bukan di dalamnya), karena <button> di dalam <a> itu HTML tidak valid */}
      <FavoriteButton movie={movie} className="absolute right-2 top-2" />
    </article>
  );
}
```

Kenapa tombol tidak ditaruh di dalam `<Link>`? Klik hati akan ikut memicu pindah halaman, dan `<button>` di dalam `<a>` melanggar aturan HTML. Solusinya: keduanya bersaudara di dalam `<article relative>`, hati diposisikan `absolute` di atas kartu.

## Langkah 5 — Pasang hati di halaman detail

Ini file yang sama dengan file 08. Bedanya cuma dua tempat yang ditandai `[BARU 09]`: import `FavoriteButton` dan tombolnya (komentar dari file 08 sudah dibuka).

**File: `src/pages/MovieDetailPage.jsx`** (ganti seluruh isi)

```jsx
import { useEffect } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { ArrowLeft, Clock, Calendar, User } from "lucide-react";
import useFetch from "../hooks/useFetch.js";
import useDocumentTitle from "../hooks/useDocumentTitle.js";
import { movieDetailUrl, imageUrl } from "../api/tmdb.js";
import { formatYear, formatRuntime } from "../utils/format.js";
import RatingBadge from "../components/RatingBadge.jsx";
import FavoriteButton from "../components/FavoriteButton.jsx"; // [BARU 09]
import MovieGrid from "../components/MovieGrid.jsx";
import ErrorState from "../components/ErrorState.jsx";

// Kerangka halaman saat loading
function DetailSkeleton() {
  return (
    <div className="flex animate-pulse flex-col gap-8 md:flex-row" aria-busy="true">
      <div className="aspect-[2/3] w-full max-w-xs rounded-xl bg-border" />
      <div className="flex-1 space-y-4">
        <div className="h-8 w-2/3 rounded bg-border" />
        <div className="h-4 w-1/3 rounded bg-border" />
        <div className="h-24 rounded bg-border" />
      </div>
    </div>
  );
}

export default function MovieDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { data: movie, loading, error, refetch } = useFetch(movieDetailUrl(id));

  useDocumentTitle(movie?.title);

  // Pindah dari film ke "film mirip" -> mulai dari atas lagi
  useEffect(() => {
    window.scrollTo({ top: 0 });
  }, [id]);

  const backButton = (
    <button
      type="button"
      onClick={() => navigate(-1)}
      className="mb-6 inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white focus-visible:outline-2 focus-visible:outline-accent"
    >
      <ArrowLeft size={16} aria-hidden="true" /> Kembali
    </button>
  );

  if (error) {
    return (
      <div>
        {backButton}
        <ErrorState message={error} onRetry={refetch} />
      </div>
    );
  }

  if (loading || !movie) {
    return (
      <div>
        {backButton}
        <DetailSkeleton />
      </div>
    );
  }

  const poster = imageUrl(movie.poster_path);
  const backdrop = imageUrl(movie.backdrop_path, "w1280");
  const trailer = movie.videos?.results.find((v) => v.site === "YouTube" && v.type === "Trailer");
  const cast = movie.credits?.cast.slice(0, 12) ?? [];
  const similar = movie.similar?.results.slice(0, 5) ?? [];

  return (
    <div>
      {backButton}

      {/* Header: backdrop + poster + info utama */}
      <section className="relative overflow-hidden rounded-2xl border border-border">
        {backdrop && (
          <img
            src={backdrop}
            alt=""
            className="absolute inset-0 h-full w-full object-cover opacity-20"
          />
        )}
        <div className="relative flex flex-col gap-8 bg-gradient-to-t from-bg via-bg/80 to-transparent p-6 md:flex-row md:p-10">
          {poster ? (
            <img
              src={poster}
              alt={`Poster ${movie.title}`}
              className="w-full max-w-xs rounded-xl shadow-2xl md:w-64"
            />
          ) : (
            <div className="flex aspect-[2/3] w-full max-w-xs items-center justify-center rounded-xl bg-border text-gray-500 md:w-64">
              Tanpa poster
            </div>
          )}

          <div className="flex-1 space-y-4">
            <div>
              <h1 className="text-3xl font-bold tracking-tight md:text-4xl">{movie.title}</h1>
              {movie.tagline && <p className="mt-1 italic text-gray-400">"{movie.tagline}"</p>}
            </div>

            <div className="flex flex-wrap items-center gap-4 text-sm text-gray-300">
              <RatingBadge value={movie.vote_average} />
              <span className="flex items-center gap-1.5">
                <Calendar size={14} aria-hidden="true" /> {formatYear(movie.release_date)}
              </span>
              <span className="flex items-center gap-1.5">
                <Clock size={14} aria-hidden="true" /> {formatRuntime(movie.runtime)}
              </span>
              <span className="text-gray-500">{movie.vote_count.toLocaleString("id-ID")} suara</span>
            </div>

            <ul className="flex flex-wrap gap-2">
              {movie.genres.map((g) => (
                <li key={g.id} className="rounded-full border border-border px-3 py-1 text-xs text-gray-300">
                  {g.name}
                </li>
              ))}
            </ul>

            <div>
              <h2 className="mb-1 text-lg font-semibold">Sinopsis</h2>
              <p className="max-w-2xl leading-relaxed text-gray-300">
                {movie.overview || "Sinopsis belum tersedia untuk film ini."}
              </p>
            </div>

            {/* [BARU 09] */}
            <FavoriteButton movie={movie} showLabel className="border border-border" />
          </div>
        </div>
      </section>

      {/* Trailer */}
      {trailer && (
        <section className="mt-10">
          <h2 className="mb-4 text-xl font-semibold">Trailer</h2>
          <div className="aspect-video overflow-hidden rounded-xl border border-border">
            <iframe
              src={`https://www.youtube-nocookie.com/embed/${trailer.key}`}
              title={`Trailer ${movie.title}`}
              allow="accelerometer; encrypted-media; picture-in-picture"
              allowFullScreen
              loading="lazy"
              className="h-full w-full"
            />
          </div>
        </section>
      )}

      {/* Pemeran */}
      {cast.length > 0 && (
        <section className="mt-10">
          <h2 className="mb-4 text-xl font-semibold">Pemeran</h2>
          <ul className="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-6">
            {cast.map((person) => {
              const photo = imageUrl(person.profile_path, "w185");
              return (
                <li key={person.cast_id ?? person.credit_id} className="text-center">
                  <div className="mx-auto aspect-square w-full overflow-hidden rounded-full bg-border">
                    {photo ? (
                      <img src={photo} alt={person.name} loading="lazy" className="h-full w-full object-cover" />
                    ) : (
                      <div className="flex h-full items-center justify-center text-gray-500">
                        <User aria-hidden="true" />
                      </div>
                    )}
                  </div>
                  <p className="mt-2 line-clamp-1 text-sm font-medium">{person.name}</p>
                  <p className="line-clamp-1 text-xs text-gray-500">{person.character}</p>
                </li>
              );
            })}
          </ul>
        </section>
      )}

      {/* Film mirip */}
      {similar.length > 0 && (
        <section className="mt-10">
          <h2 className="mb-4 text-xl font-semibold">Film mirip</h2>
          <MovieGrid movies={similar} />
        </section>
      )}

      <div className="mt-10">
        <Link to="/" className="text-sm text-gray-400 hover:text-white">
          ← Jelajahi film lainnya
        </Link>
      </div>
    </div>
  );
}
```

## Langkah 6 — Halaman Favorit

**File: `src/pages/FavoritesPage.jsx`** (ganti seluruh isi)

```jsx
import { Link } from "react-router-dom";
import { HeartOff } from "lucide-react";
import { useFavorites } from "../context/FavoritesContext.jsx";
import useDocumentTitle from "../hooks/useDocumentTitle.js";
import MovieGrid from "../components/MovieGrid.jsx";
import EmptyState from "../components/EmptyState.jsx";

export default function FavoritesPage() {
  const { favorites } = useFavorites();
  useDocumentTitle("Favorit");

  return (
    <div className="space-y-6">
      <section>
        <h1 className="text-3xl font-bold tracking-tight">Film Favorit</h1>
        <p className="mt-1 text-gray-400">{favorites.length} film tersimpan di browser ini.</p>
      </section>

      {favorites.length === 0 ? (
        <EmptyState
          icon={HeartOff}
          title="Belum ada film favorit"
          message="Klik ikon hati di poster film untuk menyimpannya di sini."
        >
          <Link
            to="/"
            className="rounded-lg bg-accent px-5 py-2 text-sm font-semibold transition hover:opacity-90"
          >
            Jelajahi film
          </Link>
        </EmptyState>
      ) : (
        <MovieGrid movies={favorites} />
      )}
    </div>
  );
}
```

`MovieGrid` dan `MovieCard` dipakai ulang tanpa diubah, karena objek favorit punya field yang sama (`id`, `title`, `poster_path`, `vote_average`, `release_date`). Di sinilah keputusan "simpan 5 field" terbayar.

## Langkah 7 — Badge jumlah di Navbar

**File: `src/components/Navbar.jsx`** (ganti seluruh isi)

```jsx
import { Link, NavLink } from "react-router-dom";
import { Film, Heart } from "lucide-react";
import { useFavorites } from "../context/FavoritesContext.jsx"; // [BARU]

const linkClass = ({ isActive }) =>
  `flex items-center gap-1.5 text-sm font-medium transition-colors ${
    isActive ? "text-white" : "text-gray-400 hover:text-white"
  }`;

export default function Navbar() {
  const { favorites } = useFavorites(); // [BARU]

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
            {/* [BARU] badge hanya muncul kalau ada favorit */}
            {favorites.length > 0 && (
              <span className="rounded-full bg-accent px-2 py-0.5 text-xs font-semibold text-white">
                {favorites.length}
              </span>
            )}
          </NavLink>
        </div>
      </nav>
    </header>
  );
}
```

## Langkah 8 — Bungkus aplikasi dengan `FavoritesProvider`

**File: `src/main.jsx`** (ganti seluruh isi)

```jsx
import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import { FavoritesProvider } from "./context/FavoritesContext.jsx"; // [BARU]
import App from "./App.jsx";
import "./index.css";

createRoot(document.getElementById("root")).render(
  <StrictMode>
    <BrowserRouter>
      {/* [BARU] Provider membungkus App, jadi Navbar & semua halaman bisa akses favorit */}
      <FavoritesProvider>
        <App />
      </FavoritesProvider>
    </BrowserRouter>
  </StrictMode>,
);
```

Urutan pembungkus: `StrictMode` → `BrowserRouter` → `FavoritesProvider` → `App`. Provider harus ada **di luar** semua component yang memakai `useFavorites()`. Karena `Navbar` juga memakainya, pemasangan di `main.jsx` (bukan di dalam salah satu halaman) sudah benar.

## Langkah 9 — Uji

1. Di Beranda, klik hati di 3 film berbeda. Hati terisi merah, badge Navbar jadi `3`.
2. Buka `/favorites`: 3 film tampil. Klik hati di sana → film hilang dari daftar, badge berkurang.
3. Buka detail sebuah film → tombol "Tambah ke Favorit" berubah jadi "Tersimpan di Favorit". Kembali ke Beranda → hati film itu sudah merah.
4. **Refresh** halaman dan **tutup lalu buka lagi** browser: favorit masih ada.
5. `F12` → **Application** → **Local Storage** → `http://localhost:5173` → key `favorites`. Lihat isinya berupa array JSON.

## Cek

- [ ] Klik hati di kartu → langsung terisi, **tanpa** pindah ke halaman detail.
- [ ] Badge di Navbar bertambah / berkurang, dan hilang saat 0.
- [ ] Halaman Favorit menampilkan film yang sama persis dengan yang di-love.
- [ ] Kosongkan semua favorit → muncul `EmptyState` "Belum ada film favorit" dengan tombol "Jelajahi film".
- [ ] Refresh → data tetap. Buka 2 tab: love di tab 1, refresh tab 2 → muncul di tab 2.
- [ ] Hati di kartu Beranda, halaman Favorit, dan halaman detail selalu sinkron untuk film yang sama.
- [ ] Tekan `Tab` sampai fokus ke tombol hati, tekan `Enter` / `Space` → jalan (bisa dipakai tanpa mouse).

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| `useFavorites harus dipakai di dalam <FavoritesProvider>` | `FavoritesProvider` belum membungkus `App` | Langkah 8. Pastikan di `main.jsx`, dan bukan di dalam `App` di bawah Navbar |
| Klik hati malah pindah ke halaman detail | Tombol berada di dalam `<Link>` | Jadikan saudara, bukan anak, seperti kode `MovieCard` |
| Klik hati, tidak terjadi apa-apa di layar | Mengubah array lama (`push`) | Buat array baru: `[...favorites, x]` atau `filter` |
| Hilang saat refresh | Tidak lewat `useLocalStorage` | Pakai `setFavorites` dari hook, bukan `useState` biasa |
| Badge Navbar tidak berubah | Navbar tidak memakai `useFavorites()` | Pastikan `const { favorites } = useFavorites();` di `Navbar` |
| `Unexpected token u in JSON at position 0` | `localStorage` berisi teks `undefined` | DevTools → Application → hapus key `favorites`, lalu refresh |
| Warning ESLint `react-refresh/only-export-components` | Satu file mengekspor component dan hook | Pakai baris `eslint-disable-next-line` seperti kode, atau pisahkan hook ke file lain |
| Film di halaman Favorit tanpa poster | Field tidak ikut tersimpan | Cek destructuring di `toggleFavorite` (5 field) |
| Data lama rusak setelah mengubah bentuk data | `localStorage` masih berisi format lama | Hapus key `favorites` di DevTools |

Lanjut ke [10-polish-ui-ux.md](10-polish-ui-ux.md).
