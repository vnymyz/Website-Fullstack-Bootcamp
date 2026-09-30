# 07 — Pagination

**Hasil akhir:** navigasi halaman di bawah grid:

```
«  1 … 3 4 [5] 6 7 … 500  »
```

Klik nomor / panah → film berganti, halaman scroll ke atas, nomor halaman tersimpan di URL (`/?page=5`).

**Sebelum mulai:** [06](06-filter-film.md) selesai, filter jalan.

## Konsep singkat

TMDB mengirim film **20 per halaman**. Di jawabannya ada info:

```js
{ page: 5, total_pages: 500, total_results: 9990, results: [ ...20 film ] }
```

Kita cukup:
1. Kirim `page` ke API (`tmdb.js` sudah menerimanya sejak file 02).
2. Baca `total_pages` untuk tahu ada berapa halaman.
3. Gambar tombol-tombolnya.

Tiga hal yang perlu diperhatikan:

| Masalah | Solusi |
|---|---|
| TMDB membatasi maksimal **halaman 500**, walau `total_pages` lebih besar | `Math.min(total_pages, 500)` |
| Menampilkan 500 tombol itu gila | Tampilkan jendela kecil di sekitar halaman aktif: `1 … 3 4 [5] 6 7 … 500` |
| Ganti filter saat di halaman 8 → halaman 8 mungkin tidak ada di hasil baru | Setiap filter/pencarian berubah, `page` **di-reset** ke 1 |

## Langkah 1 — Algoritma jendela halaman

Sebelum ngoding, pahami dulu polanya. Untuk `page = 5`, `total = 500`, tampilkan 2 halaman di kiri dan kanan:

```
start = max(1, page - 2)    = 3
end   = min(total, page + 2) = 7

kalau start > 1      -> tambah 1 (dan "..." kalau ada celah)
isi start..end       -> 3 4 5 6 7
kalau end < total    -> tambah "..." (kalau ada celah) lalu total

hasil: [1, "...", 3, 4, 5, 6, 7, "...", 500]
```

Contoh lain:

| page | total | Hasil |
|---|---|---|
| 1 | 500 | `1 2 3 … 500` |
| 2 | 5 | `1 2 3 4 5` |
| 500 | 500 | `1 … 498 499 500` |

## Langkah 2 — Component `Pagination`

**File: `src/components/Pagination.jsx`**

```jsx
import { ChevronLeft, ChevronRight } from "lucide-react";

// Contoh: page=5, total=500 -> [1, "...", 3, 4, 5, 6, 7, "...", 500]
function getPages(page, total) {
  const pages = [];
  const start = Math.max(1, page - 2);
  const end = Math.min(total, page + 2);

  if (start > 1) {
    pages.push(1);
    if (start > 2) pages.push("...");
  }
  for (let i = start; i <= end; i++) pages.push(i);
  if (end < total) {
    if (end < total - 1) pages.push("...");
    pages.push(total);
  }
  return pages;
}

const btnClass =
  "flex h-10 min-w-10 items-center justify-center rounded-lg border border-border px-3 text-sm transition focus-visible:outline-2 focus-visible:outline-accent";

export default function Pagination({ page, totalPages, onChange }) {
  if (totalPages <= 1) return null;

  return (
    <nav aria-label="Navigasi halaman" className="mt-10 flex flex-wrap items-center justify-center gap-2">
      <button
        type="button"
        onClick={() => onChange(page - 1)}
        disabled={page <= 1}
        aria-label="Halaman sebelumnya"
        className={`${btnClass} hover:bg-surface disabled:cursor-not-allowed disabled:opacity-40`}
      >
        <ChevronLeft size={18} aria-hidden="true" />
      </button>

      {getPages(page, totalPages).map((p, i) =>
        p === "..." ? (
          <span key={`dots-${i}`} className="px-1 text-gray-500">
            …
          </span>
        ) : (
          <button
            key={p}
            type="button"
            onClick={() => onChange(p)}
            aria-current={p === page ? "page" : undefined}
            aria-label={`Halaman ${p}`}
            className={`${btnClass} ${p === page ? "border-accent bg-accent font-semibold" : "hover:bg-surface"}`}
          >
            {p}
          </button>
        ),
      )}

      <button
        type="button"
        onClick={() => onChange(page + 1)}
        disabled={page >= totalPages}
        aria-label="Halaman berikutnya"
        className={`${btnClass} hover:bg-surface disabled:cursor-not-allowed disabled:opacity-40`}
      >
        <ChevronRight size={18} aria-hidden="true" />
      </button>
    </nav>
  );
}
```

| Bagian | Artinya |
|---|---|
| `getPages` di luar component | Fungsi murni (input → output), tidak butuh state. Gampang dites sendiri |
| `if (totalPages <= 1) return null` | Hasil cuma satu halaman? Jangan tampilkan pagination |
| `disabled={page <= 1}` | Tombol « mati di halaman pertama, » mati di halaman terakhir |
| `aria-current="page"` | Memberi tahu pembaca layar halaman mana yang sedang aktif |
| `key={\`dots-${i}\`}` | Titik-titik `…` bisa muncul dua kali, jadi key-nya dibedakan pakai indeks |
| `onChange(p)` | Component ini **tidak tahu** soal URL atau API. Ia cuma bilang "user mau ke halaman p". Parent yang menentukan apa yang terjadi |

## Langkah 3 — `HomePage` versi 4 (final): tambah pagination

**File: `src/pages/HomePage.jsx`** (ganti seluruh isi)

```jsx
import { useSearchParams } from "react-router-dom";
import SearchBar from "../components/SearchBar.jsx";
import FilterBar from "../components/FilterBar.jsx";
import MovieGrid from "../components/MovieGrid.jsx";
import Pagination from "../components/Pagination.jsx"; // [BARU]
import EmptyState from "../components/EmptyState.jsx";
import ErrorState from "../components/ErrorState.jsx";
import useFetch from "../hooks/useFetch.js";
import useDebounce from "../hooks/useDebounce.js";
import useDocumentTitle from "../hooks/useDocumentTitle.js";
import { discoverMoviesUrl, searchMoviesUrl } from "../api/tmdb.js";

const MAX_PAGE = 500; // [BARU] TMDB cuma ngizinin sampai halaman 500

export default function HomePage() {
  // State ada di URL: /?q=batman&year=2022&rating=7&sort=vote_average.desc&page=2
  const [searchParams, setSearchParams] = useSearchParams();
  const q = searchParams.get("q") || "";
  const year = searchParams.get("year") || "";
  const rating = searchParams.get("rating") || "";
  const sort = searchParams.get("sort") || "popularity.desc";
  const page = Number(searchParams.get("page")) || 1; // [BARU] [A]

  // Ubah satu/lebih parameter URL. Kecuali yang diubah "page", halaman balik ke 1.
  function updateParams(changes, { replace = false } = {}) {
    const next = new URLSearchParams(searchParams);
    Object.entries(changes).forEach(([key, value]) => {
      if (value) next.set(key, value);
      else next.delete(key);
    });
    if (!("page" in changes)) next.delete("page"); // [BARU] [B]
    setSearchParams(next, { replace });
  }

  // Search: input langsung berubah, tapi request ke API ditunda 500ms
  const debouncedQ = useDebounce(q, 500).trim();
  const isSearching = debouncedQ !== "";

  // Fetch: pilih endpoint search atau discover
  const url = isSearching
    ? searchMoviesUrl({ query: debouncedQ, page, year }) // [BARU] page
    : discoverMoviesUrl({ page, year, rating, sort }); // [BARU] page
  const { data, loading, error, refetch } = useFetch(url);

  // Endpoint search TMDB gak punya filter rating, jadi disaring manual di sini
  let movies = data?.results ?? [];
  if (isSearching && rating) {
    movies = movies.filter((m) => m.vote_average >= Number(rating));
  }
  const totalPages = Math.min(data?.total_pages ?? 1, MAX_PAGE); // [BARU] [C]

  useDocumentTitle(isSearching ? `Cari: ${debouncedQ}` : "Beranda");

  function goToPage(newPage) {
    updateParams({ page: newPage > 1 ? newPage : "" }); // [BARU] [D]
    window.scrollTo({ top: 0, behavior: "smooth" }); // [BARU] [E]
  }

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
        <>
          {/* [F] Info jumlah hasil */}
          <p className="text-sm text-gray-400" aria-live="polite">
            {data.total_results.toLocaleString("id-ID")} film ditemukan · halaman {page} dari {totalPages}
          </p>
          <MovieGrid movies={movies} />
          <Pagination page={page} totalPages={totalPages} onChange={goToPage} />
        </>
      )}
    </div>
  );
}
```

### Bedah per marker

| Marker | Baris | Penjelasan |
|---|---|---|
| **[A]** | `Number(searchParams.get("page")) \|\| 1` | Dari URL berupa teks `"5"` → angka `5`. Kalau kosong / bukan angka (`NaN`) → 1 |
| **[B]** | `if (!("page" in changes)) next.delete("page")` | **Inti reset halaman.** Setiap kali yang diubah bukan `page` (ketik, ganti filter), `page` dihapus dari URL → kembali ke halaman 1. Kalau yang diubah memang `page`, dibiarkan |
| **[C]** | `Math.min(total_pages, 500)` | Batas TMDB. `?? 1` cadangan saat data belum datang |
| **[D]** | `page: newPage > 1 ? newPage : ""` | Halaman 1 tidak ditulis ke URL (`/` lebih bersih daripada `/?page=1`) |
| **[E]** | `window.scrollTo(...)` | Tanpa ini, setelah klik "Halaman 2" kamu masih di bawah dan tidak sadar isinya berganti |
| **[F]** | `aria-live="polite"` | Pembaca layar mengumumkan jumlah hasil baru tanpa mengganggu. `toLocaleString("id-ID")` → `9.990` |

## Cek

- [ ] Di bawah grid ada pagination. Klik **3** → film berganti, halaman scroll ke atas, URL `/?page=3`.
- [ ] Panah « mati (abu, tidak bisa diklik) di halaman 1. Pindah ke halaman 500 → panah » mati.
- [ ] Nomor aktif berwarna merah.
- [ ] Di halaman 5 lalu ubah tahun → URL kehilangan `page`, kembali ke halaman 1.
- [ ] Ketik pencarian saat di halaman 4 → kembali ke halaman 1.
- [ ] Refresh di `/?page=7` → tetap di halaman 7.
- [ ] Tombol Back browser dari halaman 3 → halaman 2.
- [ ] Ketik manual `/?page=9999` → tidak crash (bisa tampil kosong / error dari TMDB, tapi aplikasi tidak rusak).
- [ ] Cari judul yang hasilnya cuma 1 halaman (misal `zzq` atau judul unik) → pagination **tidak tampil**.

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| Klik halaman berikutnya, data tidak berganti | `page` belum dikirim ke fungsi URL | `discoverMoviesUrl({ page, ... })` dan `searchMoviesUrl({ ..., page })` |
| Ganti filter tapi tetap di halaman 8 | Baris reset `page` hilang | `if (!("page" in changes)) next.delete("page")` |
| Halaman aktif tidak berubah warna | Membandingkan teks dengan angka (`"5" === 5`) | `const page = Number(...)` |
| Layar tidak scroll ke atas | Lupa `window.scrollTo` | Tambahkan di `goToPage` |
| Nomor halaman menampilkan `NaN` atau `undefined` | `total_pages` belum ada | `data?.total_pages ?? 1` |
| Error 422 dari TMDB di halaman > 500 | Melewati batas TMDB | Pakai `Math.min(..., MAX_PAGE)`, dan untuk URL manual biarkan `ErrorState` yang bekerja |
| Dua tombol `…` bikin warning key | Key ganda | `key={\`dots-${i}\`}` |
| Pagination muncul di bawah tapi grid kosong | `movies` disaring habis oleh filter rating saat mencari | Wajar. Itu batasan search (dijelaskan di file 06) |

Lanjut ke [08-detail-film.md](08-detail-film.md).
