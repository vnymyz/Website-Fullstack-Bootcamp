# 08 — Halaman Detail Film

**Hasil akhir:** klik kartu film → halaman `/movie/:id` berisi backdrop, poster, judul, rating, tahun, durasi, genre, **sinopsis**, trailer YouTube, daftar pemeran, dan film mirip.

```
┌────────────────────────────────────────────────────────────┐
│ ← Kembali                                                  │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ (backdrop samar di belakang)                          │   │
│ │ ┌────────┐  Judul Film                                │   │
│ │ │ poster │  "Tagline"                                 │   │
│ │ │        │  ★7.8   📅 2022   🕒 2j 15m   12.000 suara │   │
│ │ │        │  [Action] [Drama]                          │   │
│ │ └────────┘  Sinopsis                                  │   │
│ │             Teks sinopsis...                          │   │
│ │             [♡ Tambah ke Favorit]  <- fitur file 09   │   │
│ └──────────────────────────────────────────────────────┘   │
│ Trailer     [ video YouTube ]                              │
│ Pemeran     (o) (o) (o) (o) (o) (o)                        │
│ Film mirip  [kartu] [kartu] [kartu] [kartu] [kartu]        │
└────────────────────────────────────────────────────────────┘
```

**Sebelum mulai:** [07](07-pagination.md) selesai.

## Konsep singkat

- **Route dinamis:** `/movie/550` dan `/movie/603` memakai halaman yang sama. Angka di belakang (`:id`) dibaca dengan `useParams()`.
- **Satu request untuk semuanya:** `movieDetailUrl(id)` di `tmdb.js` sudah memakai `append_to_response=credits,videos,similar`. Jadi satu `fetch` membawa detail + pemeran + video + film mirip.
- **Data opsional.** Tidak semua film punya trailer, poster, sinopsis, atau foto pemeran. Setiap bagian harus **aman kalau datanya kosong**.

Bentuk data yang kita pakai (dipotong):

```js
{
  id: 550, title: "...", tagline: "...", overview: "...",
  runtime: 139, release_date: "1999-10-15", vote_average: 8.4, vote_count: 29000,
  poster_path: "/x.jpg", backdrop_path: "/y.jpg",
  genres: [{ id: 18, name: "Drama" }],
  credits: { cast: [{ name, character, profile_path, cast_id }] },
  videos:  { results: [{ site: "YouTube", type: "Trailer", key: "abc123" }] },
  similar: { results: [ ...film seperti di halaman utama ] }
}
```

## Langkah 1 — Tombol favorit sementara

Halaman detail memakai `FavoriteButton` yang baru dibuat di file 09. Supaya file ini bisa dijalankan sekarang, **jangan** import dulu. Di langkah berikutnya, baris `FavoriteButton` ditandai `[09]` dan sudah dikomentari. Buka komentarnya di file 09.

## Langkah 2 — `MovieDetailPage`

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
// import FavoriteButton from "../components/FavoriteButton.jsx"; // [09] buka komentar di file 09
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
  const { id } = useParams(); // [A]
  const navigate = useNavigate();
  const { data: movie, loading, error, refetch } = useFetch(movieDetailUrl(id)); // [B]

  useDocumentTitle(movie?.title);

  // [C] Pindah dari film ke "film mirip" -> mulai dari atas lagi
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

  // [D] Siapkan data opsional dengan aman
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

            {/* [E] Deskripsi film */}
            <div>
              <h2 className="mb-1 text-lg font-semibold">Sinopsis</h2>
              <p className="max-w-2xl leading-relaxed text-gray-300">
                {movie.overview || "Sinopsis belum tersedia untuk film ini."}
              </p>
            </div>

            {/* [09] buka komentar di file 09:
            <FavoriteButton movie={movie} showLabel className="border border-border" />
            */}
          </div>
        </div>
      </section>

      {/* [F] Trailer */}
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

      {/* [G] Pemeran */}
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

      {/* [H] Film mirip */}
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

### Bedah per marker

| Marker | Baris | Penjelasan |
|---|---|---|
| **[A]** | `useParams()` | Mengambil `:id` dari URL. Nilainya teks (`"550"`), tapi untuk dirangkai ke URL tidak perlu diubah jadi angka |
| **[B]** | `useFetch(movieDetailUrl(id))` | Ganti `id` → URL berubah → fetch ulang otomatis. Itu sebabnya klik "film mirip" langsung memuat film baru tanpa kode tambahan |
| **[B]** | `{ data: movie }` | Destructuring dengan ganti nama, sama seperti sesi 8 |
| **[C]** | `useEffect(..., [id])` | Setiap `id` berubah, scroll ke atas. Tanpa ini, klik film mirip di bagian bawah membuat kamu tetap di bawah |
| **[D]** | `movie.videos?.results.find(...)` | Cari video yang jenisnya **Trailer** dan berasal dari **YouTube**. Hasilnya `undefined` kalau tidak ada → bagian trailer tidak dirender |
| **[D]** | `.slice(0, 12)` | Pemeran film bisa ratusan orang. Ambil 12 pertama saja |
| **[E]** | `movie.overview \|\| "Sinopsis belum..."` | **Fitur "ada deskripsi film".** Dengan cadangan teks kalau TMDB tidak punya sinopsis |
| **[F]** | `youtube-nocookie.com/embed/${key}` | Menyematkan video YouTube lewat `iframe`. Domain `nocookie` tidak memasang cookie pelacak sebelum video diputar |
| **[G]** | `person.cast_id ?? person.credit_id` | Key unik untuk tiap pemeran. `??` = pakai yang kedua kalau yang pertama `null/undefined` |
| **[H]** | `<MovieGrid movies={similar} />` | Component yang sama dipakai ulang di halaman lain |

Urutan pengecekan di awal (`error` → `loading || !movie` → render utama) sama seperti di HomePage. `!movie` memastikan kita tidak pernah mengakses `movie.title` saat `movie` masih `null`.

## Langkah 3 — Uji

1. Dari Beranda klik film mana pun.
2. Coba film lama / film obscure yang tidak punya poster atau trailer.
3. Gulir ke bawah, klik salah satu "Film mirip".
4. Ketik URL ngawur `/movie/99999999`.

## Cek

- [ ] Halaman menampilkan judul, poster, rating, tahun, durasi (misal `2j 19m`), genre.
- [ ] **Sinopsis** tampil. Untuk film tanpa sinopsis tampil "Sinopsis belum tersedia...".
- [ ] Trailer bisa diputar. Film tanpa trailer: bagian Trailer hilang, tidak error.
- [ ] Deretan pemeran tampil dengan foto bulat, nama, dan peran. Yang tak punya foto tampil ikon orang.
- [ ] Klik film mirip → halaman berganti dan scroll ke atas.
- [ ] Tombol **Kembali** mengembalikan ke Beranda **dengan pencarian/filter/halaman yang sama** (karena semuanya ada di URL).
- [ ] `/movie/99999999` → `ErrorState` dengan pesan "Gagal ambil data (status 404)" dan tombol Coba lagi.
- [ ] Tab browser menampilkan judul film.
- [ ] Perkecil ke lebar HP: poster di atas, info di bawah, tidak ada scroll horizontal.

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| `Cannot read properties of null (reading 'title')` | Mengakses `movie` sebelum data datang | Pastikan ada `if (loading \|\| !movie) return ...` **sebelum** memakai `movie` |
| `Cannot read properties of undefined (reading 'find')` | `videos` tidak ada | Pakai `movie.videos?.results.find(...)` |
| Trailer tidak pernah muncul | Field salah | Cek `site === "YouTube"` dan `type === "Trailer"` (huruf besar-kecil persis) |
| Trailer kotak hitam / "Video unavailable" | Video dibatasi pemiliknya | Wajar untuk sebagian film. Bagian ini tidak bisa dikontrol |
| Klik film mirip tapi isi tidak berganti | `useFetch` tidak bergantung ke `url` | Pastikan `[url, reloadKey]` ada di dependency effect (file 04) |
| Setelah klik film mirip, layar tetap di bawah | Effect scroll hilang | Tambah `useEffect(..., [id])` |
| Halaman melebar, ada scroll horizontal di HP | Kelas lebar tetap | Gunakan `w-full max-w-xs md:w-64` seperti kode |
| Sinopsis kosong padahal film terkenal | `language` bukan `en-US` | Kembalikan `language: "en-US"` di `tmdb.js` |
| `Objects are not valid as a React child` | Merender objek (misal `movie.genres` langsung) | Render lewat `.map()` dan tampilkan `g.name` |

Lanjut ke [09-favorit.md](09-favorit.md).
