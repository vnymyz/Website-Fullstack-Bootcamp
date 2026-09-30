# 05 — Pencarian Film

**Hasil akhir:** kotak pencarian di atas grid. Ketik judul → hasil film muncul otomatis setelah kamu berhenti mengetik. Kata kunci ikut tersimpan di URL (`/?q=batman`).

**Sebelum mulai:** [04](04-movie-card-dan-grid.md) selesai, grid film populer tampil.

## Konsep singkat

**Masalah 1 — terlalu banyak request.** Kalau setiap huruf memicu request, mengetik "batman" = 6 request. Boros dan bisa kena batas kuota TMDB.

**Solusi: debounce.** Tunggu user berhenti mengetik 500 ms, baru kirim request.

```
Ketik:      b   a   t   m   a   n
Waktu:      0  100 200 300 400 500 ........ 1000ms
Tanpa debounce:  6 request
Dengan debounce:                            1 request ("batman")  <- 500ms setelah huruf terakhir
```

**Masalah 2 — di mana kata kunci disimpan?** Kita simpan di **URL** (`?q=batman`), bukan `useState` biasa. Akibatnya: hasil bisa di-share, tombol Back berfungsi, refresh tidak menghapus pencarian.

```
Ketik "batman" -> URL: /?q=batman  (langsung, tiap huruf)
                          │
                          ▼  useDebounce (tunda 500ms)
                   debouncedQ = "batman"  -> dipakai untuk memilih endpoint & fetch
```

Input mengikuti URL **langsung** (biar terasa responsif), tapi request ke API mengikuti versi yang **ditunda**.

## Langkah 1 — Hook `useDebounce`

**File: `src/hooks/useDebounce.js`**

```js
import { useState, useEffect } from "react";

// Kasih nilai yang "ketinggalan" sebentar. Nilai baru baru dipakai
// setelah user berhenti ngetik selama `delay` ms.
export default function useDebounce(value, delay = 500) {
  const [debounced, setDebounced] = useState(value);

  useEffect(() => {
    const timer = setTimeout(() => setDebounced(value), delay);
    return () => clearTimeout(timer); // ngetik lagi? batalin timer lama
  }, [value, delay]);

  return debounced;
}
```

Kunci ada di **cleanup** (`clearTimeout`). Setiap `value` berubah, timer lama dibatalkan dan timer baru dimulai. Jadi `setDebounced` hanya jalan kalau `value` diam selama 500 ms.

## Langkah 2 — Component `SearchBar`

**File: `src/components/SearchBar.jsx`**

```jsx
import { Search, X } from "lucide-react";

export default function SearchBar({ value, onChange }) {
  return (
    <div className="relative">
      <Search
        size={18}
        className="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
        aria-hidden="true"
      />
      <input
        type="search"
        value={value}
        onChange={(e) => onChange(e.target.value)}
        placeholder="Cari judul film..."
        aria-label="Cari film"
        className="w-full rounded-xl border border-border bg-surface py-3 pl-11 pr-11 text-sm placeholder:text-gray-500 focus:border-accent focus:outline-none [&::-webkit-search-cancel-button]:hidden"
      />
      {value && (
        <button
          type="button"
          onClick={() => onChange("")}
          aria-label="Hapus pencarian"
          className="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-gray-400 hover:text-white focus-visible:outline-2 focus-visible:outline-accent"
        >
          <X size={18} aria-hidden="true" />
        </button>
      )}
    </div>
  );
}
```

| Bagian | Artinya |
|---|---|
| `value` + `onChange` | Controlled input (sesi 5). Nilainya dikendalikan oleh parent (`HomePage`) |
| `onChange(e.target.value)` | Yang dikirim ke parent adalah **teksnya**, bukan event |
| `{value && <button>}` | Tombol ✕ hanya muncul kalau ada teks |
| `pointer-events-none` di ikon lup | Klik di atas ikon tetap "tembus" ke input |
| `[&::-webkit-search-cancel-button]:hidden` | Sembunyikan tombol ✕ bawaan Chrome supaya tidak ada dua tombol hapus |
| `aria-label="Cari film"` | Input tanpa label teks butuh label untuk pembaca layar |

## Langkah 3 — `HomePage` versi 2 (dengan pencarian)

**File: `src/pages/HomePage.jsx`** (ganti seluruh isi)

```jsx
import { useSearchParams } from "react-router-dom";
import SearchBar from "../components/SearchBar.jsx"; // [BARU]
import MovieGrid from "../components/MovieGrid.jsx";
import EmptyState from "../components/EmptyState.jsx";
import ErrorState from "../components/ErrorState.jsx";
import useFetch from "../hooks/useFetch.js";
import useDebounce from "../hooks/useDebounce.js"; // [BARU]
import useDocumentTitle from "../hooks/useDocumentTitle.js";
import { discoverMoviesUrl, searchMoviesUrl } from "../api/tmdb.js"; // [BARU] searchMoviesUrl

export default function HomePage() {
  // [A] Kata kunci disimpan di URL: /?q=batman
  const [searchParams, setSearchParams] = useSearchParams();
  const q = searchParams.get("q") || "";

  function updateQuery(value) {
    const next = new URLSearchParams(searchParams);
    if (value) next.set("q", value);
    else next.delete("q");
    setSearchParams(next, { replace: true });
  }

  // [B] Request ke API ditunda 500ms setelah user berhenti ngetik
  const debouncedQ = useDebounce(q, 500).trim();
  const isSearching = debouncedQ !== "";

  // [C] Ada kata kunci -> endpoint search. Kosong -> endpoint discover (populer)
  const url = isSearching ? searchMoviesUrl({ query: debouncedQ }) : discoverMoviesUrl();
  const { data, loading, error, refetch } = useFetch(url);

  useDocumentTitle(isSearching ? `Cari: ${debouncedQ}` : "Beranda");

  const movies = data?.results ?? [];

  return (
    <div className="space-y-6">
      <section>
        <h1 className="text-3xl font-bold tracking-tight">Temukan film favoritmu</h1>
        <p className="mt-1 text-gray-400">Cari, filter, dan simpan film yang kamu suka.</p>
      </section>

      <SearchBar value={q} onChange={updateQuery} />

      {error ? (
        <ErrorState message={error} onRetry={refetch} />
      ) : loading ? (
        <MovieGrid loading />
      ) : movies.length === 0 ? (
        <EmptyState
          title="Film tidak ditemukan"
          message={`Tidak ada hasil untuk "${debouncedQ}". Coba kata kunci lain.`}
        >
          <button
            type="button"
            onClick={() => updateQuery("")}
            className="rounded-lg bg-accent px-5 py-2 text-sm font-semibold transition hover:opacity-90"
          >
            Hapus pencarian
          </button>
        </EmptyState>
      ) : (
        <MovieGrid movies={movies} />
      )}
    </div>
  );
}
```

### Bedah per marker

| Marker | Baris | Penjelasan |
|---|---|---|
| **[A]** | `useSearchParams` | Sama seperti `useState`, tapi nilainya hidup di URL. `searchParams.get("q")` membaca, `setSearchParams(...)` menulis |
| **[A]** | `new URLSearchParams(searchParams)` | **Salin** parameter yang ada dulu, baru diubah. Ini penting di file 06 supaya mengubah `q` tidak menghapus `year` dan `rating` |
| **[A]** | `{ replace: true }` | Setiap huruf **menimpa** riwayat browser, bukan menambah. Tanpa ini, tombol Back harus ditekan 6x untuk keluar dari "batman" |
| **[B]** | `useDebounce(q, 500).trim()` | `q` berubah tiap huruf, `debouncedQ` cuma berubah setelah 500 ms diam. `.trim()` supaya spasi saja tidak dianggap pencarian |
| **[C]** | ternary `isSearching ? ... : ...` | Memilih URL. Karena `useFetch` bergantung ke `url`, ganti URL otomatis memicu fetch baru |

## Cek

- [ ] Ketik `batman` → setelah berhenti sekitar setengah detik, grid berganti hasil film Batman.
- [ ] Tab **Network**: mengetik `batman` cepat menghasilkan **1** request `search/movie`, bukan 6.
- [ ] URL berubah jadi `/?q=batman`. **Refresh halaman** → kotak masih terisi "batman" dan hasil sama.
- [ ] Klik ✕ → kotak kosong, grid balik ke film populer, URL kembali `/`.
- [ ] Ketik `zzzxxqq` → muncul `EmptyState` "Film tidak ditemukan" dengan tombol "Hapus pencarian".
- [ ] Judul tab berubah jadi "Cari: batman | React Movie".
- [ ] Copy URL `/?q=batman`, buka di tab baru → langsung tampil hasil.

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| Ketik satu huruf, langsung hilang / kotak "macet" | `SearchBar` tidak mengubah URL | `onChange={updateQuery}` dan `value={q}` harus sama-sama terpasang |
| Request keluar tiap huruf | `useFetch` diberi `q`, bukan `debouncedQ` | `searchMoviesUrl({ query: debouncedQ })` |
| Hasil pencarian tidak pernah muncul | `debouncedQ` selalu kosong | Cek `useDebounce`: `setDebounced(value)` di dalam `setTimeout` |
| Tombol Back butuh banyak klik | Lupa `replace: true` | `setSearchParams(next, { replace: true })` |
| `Cannot read properties of undefined (reading 'get')` | `useSearchParams` salah destructure | `const [searchParams, setSearchParams] = useSearchParams();` (array, bukan objek) |
| Mengetik spasi saja memicu pencarian kosong | Lupa `.trim()` | `useDebounce(q, 500).trim()` |
| Kotak tidak bisa mengetik spasi di tengah kata | Nilai di-`trim()` sebelum masuk `q` | `trim` hanya pada `debouncedQ`, bukan pada `q` |

Lanjut ke [06-filter-film.md](06-filter-film.md).
