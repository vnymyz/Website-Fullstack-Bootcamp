# 04 — Kartu Film, Grid & Loading/Error/Empty State

**Hasil akhir:** halaman Beranda menampilkan 20 film populer asli dari TMDB dalam grid responsif, lengkap dengan skeleton saat loading, pesan error dengan tombol "Coba lagi", dan tampilan kosong.

**Sebelum mulai:** [03](03-layout-navbar-routing.md) selesai, route jalan.

## Konsep singkat: 3 keadaan yang wajib ditangani

Data dari internet **tidak langsung ada**. UI yang bagus harus siap untuk 4 keadaan:

| Keadaan | Yang ditampilkan | Component |
|---|---|---|
| Loading | Kerangka kartu abu berdenyut (bukan layar kosong / tulisan "Loading...") | `SkeletonCard` |
| Error | Pesan jelas + tombol "Coba lagi" | `ErrorState` |
| Kosong | Penjelasan + saran tindakan | `EmptyState` |
| Sukses | Grid film | `MovieGrid` → `MovieCard` |

Urutan pengecekannya di `HomePage` selalu: **error → loading → kosong → sukses**.

## Langkah 1 — Fungsi format

**File: `src/utils/format.js`**

```js
// "2024-05-01" -> "2024"
export function formatYear(date) {
  return date ? date.slice(0, 4) : "-";
}

// 7.456 -> "7.5"   |   0 (belum ada rating) -> "NR"
export function formatRating(value) {
  return value ? value.toFixed(1) : "NR";
}

// 135 -> "2j 15m"   |   45 -> "45m"
export function formatRuntime(minutes) {
  if (!minutes) return "-";
  const h = Math.floor(minutes / 60);
  const m = minutes % 60;
  return h ? `${h}j ${m}m` : `${m}m`;
}
```

Kenapa dipisah ke file sendiri? Data TMDB kadang kosong (`release_date: ""`, `runtime: null`). Kalau logika "kalau kosong tampilkan strip" ditulis di 5 component, gampang lupa. Satu tempat = satu perbaikan. `formatRuntime` baru dipakai di file 08.

## Langkah 2 — Hook `useFetch`

Ini pola dari sesi 8 dengan dua tambahan: `url = null` berarti "jangan fetch dulu", dan `refetch` buat tombol "Coba lagi".

**File: `src/hooks/useFetch.js`**

```js
import { useState, useEffect, useCallback } from "react";

// Ambil data dari `url`. Kalau url berubah, otomatis fetch ulang.
// Kalau url = null, gak fetch apa-apa.
export default function useFetch(url) {
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(Boolean(url));
  const [error, setError] = useState(null);
  const [reloadKey, setReloadKey] = useState(0);

  useEffect(() => {
    if (!url) return;
    let batal = false; // kalau component keburu ilang / url berubah, jangan update state

    async function load() {
      try {
        setLoading(true);
        setError(null);
        const res = await fetch(url);
        if (res.status === 401) throw new Error("API key salah atau belum diisi. Cek file .env kamu.");
        if (!res.ok) throw new Error(`Gagal ambil data (status ${res.status})`);
        const json = await res.json();
        if (!batal) setData(json);
      } catch (err) {
        if (!batal) setError(err.message);
      } finally {
        if (!batal) setLoading(false);
      }
    }

    load();

    return () => {
      batal = true;
    };
  }, [url, reloadKey]);

  // Tombol "Coba lagi": ganti reloadKey supaya effect jalan ulang
  const refetch = useCallback(() => setReloadKey((k) => k + 1), []);

  return { data, loading, error, refetch };
}
```

| Bagian | Artinya |
|---|---|
| `[url, reloadKey]` | Effect jalan ulang kalau URL berubah (ganti halaman/filter) **atau** `refetch()` dipanggil |
| `batal = true` di cleanup | User ganti filter cepat-cepat: request lama yang telat datang **dibuang**, tidak menimpa data baru |
| `res.status === 401` | Pesan khusus buat kesalahan paling umum: API key |
| `useCallback` | Supaya `refetch` tidak dibuat ulang tiap render |

## Langkah 3 — `RatingBadge`

**File: `src/components/RatingBadge.jsx`**

```jsx
import { Star } from "lucide-react";
import { formatRating } from "../utils/format.js";

export default function RatingBadge({ value }) {
  const color = value >= 7 ? "text-green-400" : value >= 5 ? "text-yellow-400" : "text-red-400";

  return (
    <span
      className={`inline-flex items-center gap-1 rounded-md bg-black/70 px-2 py-1 text-xs font-semibold ${color}`}
    >
      <Star size={12} fill="currentColor" aria-hidden="true" />
      {formatRating(value)}
    </span>
  );
}
```

Warna ikut nilai: 7+ hijau, 5–7 kuning, di bawah 5 merah. Warna membantu, tapi **angkanya tetap ditulis** supaya orang buta warna juga paham.

## Langkah 4 — `SkeletonCard`

**File: `src/components/SkeletonCard.jsx`**

```jsx
// Kerangka kartu film yang berdenyut selama data belum datang
export default function SkeletonCard() {
  return (
    <div className="overflow-hidden rounded-xl border border-border bg-surface">
      <div className="aspect-[2/3] animate-pulse bg-border" />
      <div className="space-y-2 p-3">
        <div className="h-4 w-3/4 animate-pulse rounded bg-border" />
        <div className="h-3 w-1/4 animate-pulse rounded bg-border" />
      </div>
    </div>
  );
}
```

Ukurannya sengaja **sama persis** dengan kartu asli (`aspect-[2/3]`). Jadi saat data datang, layout tidak "loncat".

## Langkah 5 — `EmptyState` dan `ErrorState`

**File: `src/components/EmptyState.jsx`**

```jsx
import { SearchX } from "lucide-react";

export default function EmptyState({ icon: Icon = SearchX, title, message, children }) {
  return (
    <div className="flex flex-col items-center justify-center rounded-xl border border-dashed border-border py-16 text-center">
      <Icon size={48} className="mb-4 text-gray-500" aria-hidden="true" />
      <h2 className="text-lg font-semibold">{title}</h2>
      {message && <p className="mt-1 max-w-sm text-sm text-gray-400">{message}</p>}
      {children && <div className="mt-5">{children}</div>}
    </div>
  );
}
```

`icon: Icon = SearchX` artinya: prop bernama `icon`, ditampung di variabel `Icon` (huruf besar, karena dipakai sebagai component `<Icon />`), dan kalau tidak diberikan pakai `SearchX`. `children` dipakai buat tombol aksi opsional.

**File: `src/components/ErrorState.jsx`**

```jsx
import { TriangleAlert } from "lucide-react";

export default function ErrorState({ message, onRetry }) {
  return (
    <div
      role="alert"
      className="flex flex-col items-center justify-center rounded-xl border border-red-500/30 bg-red-500/5 py-16 text-center"
    >
      <TriangleAlert size={48} className="mb-4 text-red-400" aria-hidden="true" />
      <h2 className="text-lg font-semibold">Waduh, ada masalah</h2>
      <p className="mt-1 max-w-sm text-sm text-gray-400">{message}</p>
      {onRetry && (
        <button
          type="button"
          onClick={onRetry}
          className="mt-5 rounded-lg bg-accent px-5 py-2 text-sm font-semibold transition hover:opacity-90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
        >
          Coba lagi
        </button>
      )}
    </div>
  );
}
```

`role="alert"` membuat pembaca layar langsung membacakan pesan errornya.

## Langkah 6 — `MovieCard`

Versi awal, belum ada tombol hati (ditambah di file 09).

**File: `src/components/MovieCard.jsx`**

```jsx
import { Link } from "react-router-dom";
import { Film } from "lucide-react";
import RatingBadge from "./RatingBadge.jsx";
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
    </article>
  );
}
```

| Bagian | Artinya |
|---|---|
| `imageUrl(movie.poster_path)` | Rakit URL poster. Hasilnya `null` kalau film tak punya poster |
| `poster ? <img/> : <Film/>` | **Fallback**: film tanpa poster tetap tampil rapi dengan ikon, bukan gambar rusak |
| `loading="lazy"` | Browser hanya memuat gambar yang mendekati layar. Halaman jauh lebih cepat |
| `aspect-[2/3]` | Rasio poster film, jadi semua kartu seragam |
| `group` + `group-hover:scale-105` | Saat kartu (induk) di-hover, gambar (anak) ikut membesar sedikit |
| `line-clamp-1` | Judul panjang dipotong jadi satu baris dengan `…` |
| `hover:-translate-y-1`, `focus-visible:outline-2` | Umpan balik saat mouse di atas kartu dan saat dinavigasi pakai keyboard (Tab) |

## Langkah 7 — `MovieGrid`

**File: `src/components/MovieGrid.jsx`**

```jsx
import MovieCard from "./MovieCard.jsx";
import SkeletonCard from "./SkeletonCard.jsx";

const gridClass = "grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5";

export default function MovieGrid({ movies, loading = false, skeletonCount = 20 }) {
  if (loading) {
    return (
      <div className={gridClass} aria-busy="true">
        {Array.from({ length: skeletonCount }, (_, i) => (
          <SkeletonCard key={i} />
        ))}
      </div>
    );
  }

  return (
    <div className={gridClass}>
      {movies.map((movie) => (
        <MovieCard key={movie.id} movie={movie} />
      ))}
    </div>
  );
}
```

Grid responsif: 2 kolom di HP, 3 di tablet kecil (`sm`), 4 (`md`), 5 di layar lebar (`lg`). `Array.from({ length: 20 }, ...)` membuat 20 skeleton tanpa perlu menulis array manual.

## Langkah 8 — `HomePage` versi 1 (film populer)

**File: `src/pages/HomePage.jsx`** (ganti seluruh isi)

```jsx
import MovieGrid from "../components/MovieGrid.jsx";
import EmptyState from "../components/EmptyState.jsx";
import ErrorState from "../components/ErrorState.jsx";
import useFetch from "../hooks/useFetch.js";
import useDocumentTitle from "../hooks/useDocumentTitle.js";
import { discoverMoviesUrl } from "../api/tmdb.js";

export default function HomePage() {
  const { data, loading, error, refetch } = useFetch(discoverMoviesUrl());
  useDocumentTitle("Beranda");

  const movies = data?.results ?? [];

  return (
    <div className="space-y-6">
      <section>
        <h1 className="text-3xl font-bold tracking-tight">Temukan film favoritmu</h1>
        <p className="mt-1 text-gray-400">Cari, filter, dan simpan film yang kamu suka.</p>
      </section>

      {error ? (
        <ErrorState message={error} onRetry={refetch} />
      ) : loading ? (
        <MovieGrid loading />
      ) : movies.length === 0 ? (
        <EmptyState title="Belum ada film" message="TMDB tidak mengembalikan data." />
      ) : (
        <MovieGrid movies={movies} />
      )}
    </div>
  );
}
```

`data?.results ?? []`: pada render pertama `data` masih `null`. Tanda `?.` mencegah crash, dan `?? []` memberi array kosong sebagai cadangan.

## Langkah 9 — Rapikan halaman 404 pakai `EmptyState`

**File: `src/pages/NotFoundPage.jsx`** (ganti seluruh isi)

```jsx
import { Link } from "react-router-dom";
import { Clapperboard } from "lucide-react";
import EmptyState from "../components/EmptyState.jsx";
import useDocumentTitle from "../hooks/useDocumentTitle.js";

export default function NotFoundPage() {
  useDocumentTitle("Halaman tidak ditemukan");

  return (
    <EmptyState
      icon={Clapperboard}
      title="404 - Halaman tidak ditemukan"
      message="Alamat yang kamu buka tidak ada. Mungkin salah ketik?"
    >
      <Link
        to="/"
        className="rounded-lg bg-accent px-5 py-2 text-sm font-semibold transition hover:opacity-90"
      >
        Kembali ke Beranda
      </Link>
    </EmptyState>
  );
}
```

## Langkah 10 — Uji 3 keadaan dengan sengaja

1. **Sukses:** buka `/`. Kamu melihat skeleton sekilas lalu 20 poster.
2. **Lambat:** `F12` → tab **Network** → dropdown throttling pilih **Slow 4G** → refresh. Skeleton terlihat lebih lama.
3. **Error:** di tab Network centang **Offline** → refresh. Muncul kartu merah "Waduh, ada masalah" dan tombol **Coba lagi**. Hilangkan centang Offline, klik tombol → data muncul tanpa refresh halaman.
4. **Key salah:** ubah key di `.env` jadi asal, restart → pesan "API key salah atau belum diisi". Kembalikan key.

## Cek

- [ ] 20 kartu film tampil dengan poster, judul, tahun, dan rating berwarna.
- [ ] Skeleton tampil saat loading (dites dengan throttling).
- [ ] Mode Offline menampilkan `ErrorState`, tombol "Coba lagi" memulihkan.
- [ ] Perkecil lebar jendela: kolom berubah 5 → 4 → 3 → 2.
- [ ] Klik kartu → URL jadi `/movie/<id>` (isi halaman masih placeholder).
- [ ] Tekan `Tab` berkali-kali: kartu mendapat garis fokus merah.

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| `Cannot read properties of null (reading 'results')` | Lupa `?.` | `data?.results ?? []` |
| Kartu tampil tapi poster kosong semua | `imageUrl` salah rakit / field salah nama | Field-nya `poster_path` (underscore). Cek URL gambar di tab Network |
| `Each child in a list should have a unique "key" prop` | `key` lupa di `.map` | `key={movie.id}` |
| Grid cuma 1 kolom | Kelas Tailwind salah ketik | Pastikan `grid grid-cols-2 ...` persis |
| Ikon tidak muncul / `does not provide an export named 'TriangleAlert'` | Nama ikon beda di versi `lucide-react` lama | Ganti dengan `AlertTriangle`, atau update: `npm install lucide-react@latest` |
| Data tidak berubah setelah klik "Coba lagi" | `refetch` tidak diteruskan ke `onRetry` | `<ErrorState onRetry={refetch} />` |
| Terus loading tanpa error | `url` bernilai `null` | Pastikan `discoverMoviesUrl()` dipanggil, bukan hanya dirujuk |

Lanjut ke [05-search-movie.md](05-search-movie.md).
