# 06 — Filter Film (Tahun, Rating, Popularitas)

**Hasil akhir:** tiga dropdown + tombol reset di bawah kotak pencarian:

```
[Tahun rilis ▾]  [Rating minimal ▾]  [Urutkan ▾]  [↺ Reset filter]
 Semua tahun      Semua rating         Terpopuler
 2026 ...         ★ 5+ ... ★ 8+        Rating tertinggi
                                       Terbaru
```

**Sebelum mulai:** [05](05-search-movie.md) selesai, pencarian jalan.

## Konsep singkat

Tiga filter yang diminta soal dipetakan ke parameter TMDB:

| Filter | Pilihan user | Parameter TMDB |
|---|---|---|
| Tahun | 2026, 2025, ... 1970 | `primary_release_year=2022` |
| Rating bintang | ★ 5+, 6+, 7+, 8+ | `vote_average.gte=7` |
| Popularitas | Terpopuler / Rating tertinggi / Terbaru | `sort_by=popularity.desc` / `vote_average.desc` / `primary_release_date.desc` |

Semua state filter disimpan di URL, sama seperti `q`:

```
/?q=&year=2022&rating=7&sort=vote_average.desc
```

**Keterbatasan API yang harus kamu tahu.** Endpoint `/search/movie` (pencarian) **tidak punya** parameter `vote_average.gte` maupun `sort_by`. Hanya `/discover/movie` yang punya. Jadi kita atur begini:

| Kondisi | Tahun | Rating minimal | Urutan |
|---|---|---|---|
| Tidak mencari (discover) | Lewat API | Lewat API | Lewat API |
| Sedang mencari (search) | Lewat API | Disaring manual di browser, hanya pada 20 hasil di halaman itu | Dropdown dimatikan |

Ini contoh nyata: API tidak selalu menyediakan semua yang UI-mu mau. Kadang kamu harus menyesuaikan UI, dan **jujur** ke user soal batasannya (kita beri catatan kecil di layar).

## Langkah 1 — Component `FilterBar`

**File: `src/components/FilterBar.jsx`**

```jsx
import { RotateCcw } from "lucide-react";

const currentYear = new Date().getFullYear();
// [2026, 2025, ..., 1970]
const years = Array.from({ length: currentYear - 1969 }, (_, i) => currentYear - i);

const sortOptions = [
  { value: "popularity.desc", label: "Terpopuler" },
  { value: "vote_average.desc", label: "Rating tertinggi" },
  { value: "primary_release_date.desc", label: "Terbaru" },
];

const ratingOptions = [
  { value: "", label: "Semua rating" },
  { value: "5", label: "★ 5+" },
  { value: "6", label: "★ 6+" },
  { value: "7", label: "★ 7+" },
  { value: "8", label: "★ 8+" },
];

const selectClass =
  "w-full rounded-lg border border-border bg-surface px-3 py-2 text-sm focus:border-accent focus:outline-none disabled:cursor-not-allowed disabled:opacity-50";

function Field({ label, children }) {
  return (
    <label className="flex flex-col gap-1 text-xs font-medium text-gray-400">
      {label}
      {children}
    </label>
  );
}

export default function FilterBar({ year, rating, sort, sortDisabled, onChange, onReset }) {
  const hasFilter = year || rating || sort !== "popularity.desc";

  return (
    <div className="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:items-end">
      <Field label="Tahun rilis">
        <select value={year} onChange={(e) => onChange("year", e.target.value)} className={selectClass}>
          <option value="">Semua tahun</option>
          {years.map((y) => (
            <option key={y} value={y}>
              {y}
            </option>
          ))}
        </select>
      </Field>

      <Field label="Rating minimal">
        <select value={rating} onChange={(e) => onChange("rating", e.target.value)} className={selectClass}>
          {ratingOptions.map((o) => (
            <option key={o.value} value={o.value}>
              {o.label}
            </option>
          ))}
        </select>
      </Field>

      <Field label="Urutkan">
        <select
          value={sort}
          onChange={(e) => onChange("sort", e.target.value)}
          disabled={sortDisabled}
          title={sortDisabled ? "Urutan tidak tersedia saat mencari judul" : undefined}
          className={selectClass}
        >
          {sortOptions.map((o) => (
            <option key={o.value} value={o.value}>
              {o.label}
            </option>
          ))}
        </select>
      </Field>

      <button
        type="button"
        onClick={onReset}
        disabled={!hasFilter}
        className="flex items-center justify-center gap-2 rounded-lg border border-border px-3 py-2 text-sm text-gray-300 transition hover:bg-surface disabled:cursor-not-allowed disabled:opacity-40"
      >
        <RotateCcw size={14} aria-hidden="true" />
        Reset filter
      </button>
    </div>
  );
}
```

### Bedah

| Bagian | Artinya |
|---|---|
| `Array.from({ length: currentYear - 1969 }, (_, i) => currentYear - i)` | Membuat daftar tahun dari sekarang mundur ke 1970. Tidak di-hardcode, jadi tahun depan otomatis ikut |
| `<label>` membungkus `<select>` | Mengklik tulisan "Tahun rilis" memfokuskan dropdown, dan pembaca layar tahu fungsinya |
| `value={year}` + `onChange` | Controlled select. Nilainya berasal dari URL, bukan state lokal |
| `onChange("year", e.target.value)` | Satu fungsi `onChange(nama, nilai)` dipakai ketiga dropdown, jadi parent cukup punya satu handler |
| `disabled={sortDisabled}` | Dropdown urutan dimatikan saat mencari. Atribut `title` menjelaskan alasannya |
| `hasFilter` | Tombol Reset hanya aktif kalau memang ada filter yang diubah |
| Komponen `Field` di file yang sama | Pembungkus kecil yang hanya dipakai di sini. Tidak perlu file sendiri |

## Langkah 2 — `HomePage` versi 3 (dengan filter)

`updateQuery` dari file 05 diganti `updateParams` yang lebih umum (bisa mengubah parameter apa saja).

**File: `src/pages/HomePage.jsx`** (ganti seluruh isi)

```jsx
import { useSearchParams } from "react-router-dom";
import SearchBar from "../components/SearchBar.jsx";
import FilterBar from "../components/FilterBar.jsx"; // [BARU]
import MovieGrid from "../components/MovieGrid.jsx";
import EmptyState from "../components/EmptyState.jsx";
import ErrorState from "../components/ErrorState.jsx";
import useFetch from "../hooks/useFetch.js";
import useDebounce from "../hooks/useDebounce.js";
import useDocumentTitle from "../hooks/useDocumentTitle.js";
import { discoverMoviesUrl, searchMoviesUrl } from "../api/tmdb.js";

export default function HomePage() {
  // [A] State ada di URL: /?q=batman&year=2022&rating=7&sort=vote_average.desc
  const [searchParams, setSearchParams] = useSearchParams();
  const q = searchParams.get("q") || "";
  const year = searchParams.get("year") || "";
  const rating = searchParams.get("rating") || "";
  const sort = searchParams.get("sort") || "popularity.desc";

  // [B] Ubah satu atau beberapa parameter sekaligus. Kosong = parameter dihapus dari URL
  function updateParams(changes, { replace = false } = {}) {
    const next = new URLSearchParams(searchParams);
    Object.entries(changes).forEach(([key, value]) => {
      if (value) next.set(key, value);
      else next.delete(key);
    });
    setSearchParams(next, { replace });
  }

  const debouncedQ = useDebounce(q, 500).trim();
  const isSearching = debouncedQ !== "";

  // [C] Filter ikut dikirim ke API
  const url = isSearching
    ? searchMoviesUrl({ query: debouncedQ, year })
    : discoverMoviesUrl({ year, rating, sort });
  const { data, loading, error, refetch } = useFetch(url);

  // [D] Endpoint search tidak punya filter rating, jadi disaring manual di sini
  let movies = data?.results ?? [];
  if (isSearching && rating) {
    movies = movies.filter((m) => m.vote_average >= Number(rating));
  }

  useDocumentTitle(isSearching ? `Cari: ${debouncedQ}` : "Beranda");

  function resetFilters() {
    updateParams({ year: "", rating: "", sort: "" });
  }

  return (
    <div className="space-y-6">
      <section>
        <h1 className="text-3xl font-bold tracking-tight">Temukan film favoritmu</h1>
        <p className="mt-1 text-gray-400">Cari, filter, dan simpan film yang kamu suka.</p>
      </section>

      <SearchBar value={q} onChange={(value) => updateParams({ q: value }, { replace: true })} />

      <FilterBar
        year={year}
        rating={rating}
        sort={sort}
        sortDisabled={isSearching}
        onChange={(name, value) => updateParams({ [name]: value })}
        onReset={resetFilters}
      />

      {isSearching && rating && (
        <p className="text-xs text-gray-500">
          Catatan: filter rating saat mencari hanya menyaring hasil di halaman ini.
        </p>
      )}

      {error ? (
        <ErrorState message={error} onRetry={refetch} />
      ) : loading ? (
        <MovieGrid loading />
      ) : movies.length === 0 ? (
        <EmptyState
          title="Film tidak ditemukan"
          message="Coba kata kunci lain atau longgarkan filter kamu."
        >
          <button
            type="button"
            onClick={() => setSearchParams({})}
            className="rounded-lg bg-accent px-5 py-2 text-sm font-semibold transition hover:opacity-90"
          >
            Hapus semua pencarian & filter
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
| **[A]** | `searchParams.get("sort") \|\| "popularity.desc"` | Kalau URL tidak punya `sort`, pakai nilai bawaan. URL bersih (`/`) berarti "terpopuler, semua tahun, semua rating" |
| **[B]** | `updateParams({ year: "2022" })` | Menyalin parameter yang ada, menimpa yang diubah, menghapus yang kosong. Jadi memilih tahun **tidak** menghapus kata kunci pencarian |
| **[B]** | `{ [name]: value }` | *Computed property*. Kalau `name = "year"`, jadi `{ year: value }` |
| **[C]** | `discoverMoviesUrl({ year, rating, sort })` | Ini alasan kita memisahkan `tmdb.js`: cukup oper objek, perakitan URL sudah beres |
| **[D]** | `.filter(m => m.vote_average >= Number(rating))` | `rating` dari URL bertipe teks (`"7"`). Harus dijadikan angka dulu sebelum dibandingkan |
| — | `setSearchParams({})` | Mengosongkan seluruh parameter URL = kembali ke kondisi awal |

## Cek

- [ ] Pilih tahun **2022** → semua film berumur 2022. URL: `/?year=2022`.
- [ ] Tambah rating **★ 8+** → sisa film berating ≥ 8. URL: `/?year=2022&rating=8`.
- [ ] Ganti urutan **Terbaru** → film tahun itu diurutkan dari tanggal rilis terbaru.
- [ ] Urutan **Rating tertinggi** tanpa filter tahun → film klasik terkenal (bukan film asing dengan 1 vote).
- [ ] Ketik pencarian saat filter tahun aktif → hasil pencarian **tetap** terfilter tahun tersebut, dropdown Urutkan meredup.
- [ ] Pencarian + rating ★ 8+ → muncul catatan kecil di bawah filter.
- [ ] Tombol **Reset filter** mati kalau belum ada filter, aktif kalau ada, dan mengembalikan semua dropdown.
- [ ] Refresh halaman → dropdown tetap menampilkan pilihan yang sama (karena dari URL).
- [ ] Pilih kombinasi yang tidak ada hasilnya (tahun 1970 + ★ 8+ + kata kunci ngawur) → muncul `EmptyState`.

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| Dropdown berubah tapi film tidak | Filter tidak diteruskan ke fungsi URL | `discoverMoviesUrl({ year, rating, sort })` |
| Memilih tahun menghapus kata kunci | `updateParams` tidak menyalin parameter lama | `new URLSearchParams(searchParams)` di awal fungsi |
| Filter rating di pencarian tidak jalan | Membandingkan teks dengan angka | `Number(rating)` |
| Dropdown "melompat" balik ke pilihan lama | `value` tidak terhubung ke URL | `value={year}` harus dari `searchParams.get("year")` |
| Urutan "Rating tertinggi" penuh film asing | `vote_count.gte` hilang dari `tmdb.js` | Cek `discoverMoviesUrl`, harus ada `"vote_count.gte": 200` |
| `Warning: A component is changing an uncontrolled input to be controlled` | `value` bisa `undefined` | Gunakan `\|\| ""` saat membaca dari `searchParams` |
| Tombol Reset selalu mati | `sort` default tidak sama dengan yang dibandingkan | Bandingkan dengan `"popularity.desc"` persis |

Lanjut ke [07-pagination.md](07-pagination.md).
