# 00 — Gambaran Besar Project

Baca file ini **sebelum** ngoding. Tujuannya biar kamu tau lagi bikin apa, file apa aja yang bakal dibuat, dan gimana data mengalir. Kalau di tengah jalan bingung "ini file buat apa?", balik ke sini.

## Hasil Akhir

Website film dengan 3 halaman:

```
┌──────────────────────────────────────────────────────────────┐
│ 🎬 ReactMovie                          Beranda   ♥ Favorit (3)│  <- Navbar (sticky)
├──────────────────────────────────────────────────────────────┤
│  Temukan film favoritmu                                      │
│  ┌────────────────────────────────────────────────────────┐  │
│  │ 🔍 Cari judul film...                                  │  │  <- SearchBar
│  └────────────────────────────────────────────────────────┘  │
│  [Tahun ▾]  [Rating minimal ▾]  [Urutkan ▾]  [Reset filter]  │  <- FilterBar
│                                                              │
│  12.480 film ditemukan · halaman 1 dari 500                  │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐                │
│  │★7.8 ♥│ │★6.2 ♡│ │★8.1 ♡│ │★5.5 ♡│ │★7.0 ♡│                │  <- MovieGrid
│  │poster│ │poster│ │poster│ │poster│ │poster│                │     isi MovieCard
│  │Judul │ │Judul │ │Judul │ │Judul │ │Judul │                │
│  │2024  │ │2023  │ │2022  │ │2021  │ │2020  │                │
│  └──────┘ └──────┘ └──────┘ └──────┘ └──────┘                │
│                                                              │
│          «  1 … 3 4 [5] 6 7 … 500  »                        │  <- Pagination
└──────────────────────────────────────────────────────────────┘
```

## Peta Halaman (Route)

| URL | Halaman | Isi |
|---|---|---|
| `/` | `HomePage` | Search + filter + daftar film + pagination |
| `/movie/:id` | `MovieDetailPage` | Sinopsis, genre, durasi, pemeran, trailer, film mirip |
| `/favorites` | `FavoritesPage` | Film yang di-love |
| `*` (lainnya) | `NotFoundPage` | 404 |

Kondisi search & filter disimpan **di URL**, contoh:

```
/?q=batman&year=2022&rating=7&sort=vote_average.desc&page=2
```

Kenapa di URL? Halaman bisa di-share, tombol Back browser jalan, dan refresh gak reset pencarian.

## Struktur Folder Lengkap

Angka `[01]` di kanan artinya **file itu dibuat di langkah/file panduan nomor berapa**.

```
react-movie/
├── .env                          VITE_TMDB_API_KEY=xxxx (RAHASIA, jangan di-commit)   [01]
├── .env.example                  template tanpa isi asli (BOLEH di-commit)            [01]
├── .gitignore                    ada baris ".env"                                     [01]
├── index.html                    judul, font Inter, meta                              [01]
├── package.json                  daftar library
├── vite.config.js                plugin react() + tailwindcss()                       [01]
└── src/
    ├── main.jsx                  titik masuk: Router + Provider + <App />             [03][09]
    ├── App.jsx                   daftar <Route> semua halaman                         [03]
    ├── Layout.jsx                Navbar + <Outlet /> + footer                         [03][10]
    ├── index.css                 Tailwind + warna tema + font                         [01]
    │
    ├── api/
    │   └── tmdb.js               SATU-SATUNYA file yang tau alamat TMDB               [02]
    │
    ├── hooks/
    │   ├── useFetch.js           ambil data: { data, loading, error, refetch }        [04]
    │   ├── useDocumentTitle.js   ganti judul tab browser                              [03]
    │   ├── useDebounce.js        tunda nilai ketikan biar gak spam API                [05]
    │   └── useLocalStorage.js    useState yang tersimpan di localStorage              [09]
    │
    ├── context/
    │   └── FavoritesContext.jsx  daftar favorit + toggleFavorite + isFavorite         [09]
    │
    ├── utils/
    │   └── format.js             formatYear, formatRating, formatRuntime              [04]
    │
    ├── components/
    │   ├── Navbar.jsx            logo + link + badge jumlah favorit                   [03][09]
    │   ├── MovieCard.jsx         satu kartu film (poster, judul, tahun, rating, ♥)    [04][09]
    │   ├── MovieGrid.jsx         grid kartu / grid skeleton                           [04]
    │   ├── SkeletonCard.jsx      kartu abu berdenyut saat loading                     [04]
    │   ├── RatingBadge.jsx       ★ 7.8 (hijau/kuning/merah)                           [04]
    │   ├── EmptyState.jsx        tampilan "kosong" (tidak ada hasil, belum ada fav)   [04]
    │   ├── ErrorState.jsx        pesan error + tombol "Coba lagi"                     [04]
    │   ├── SearchBar.jsx         kotak cari + tombol hapus                            [05]
    │   ├── FilterBar.jsx         dropdown tahun, rating, urutan + reset               [06]
    │   ├── Pagination.jsx        « 1 … 4 [5] 6 … 500 »                                [07]
    │   └── FavoriteButton.jsx    tombol hati                                          [09]
    │
    └── pages/
        ├── HomePage.jsx          search + filter + grid + pagination                  [04-07]
        ├── MovieDetailPage.jsx   halaman detail                                       [08]
        ├── FavoritesPage.jsx     halaman favorit                                      [03][09]
        └── NotFoundPage.jsx      404                                                  [03]
```

## Alur Data (Ini Bagian Terpenting)

```
   User ngetik / pilih filter / klik halaman
                    │
                    ▼
        ┌──────────────────────┐
        │ URL berubah          │   /?q=batman&year=2022&page=2
        │ (useSearchParams)    │
        └──────────┬───────────┘
                   ▼
        ┌──────────────────────┐
        │ HomePage baca URL,   │   q kosong?  -> discoverMoviesUrl(...)
        │ pilih endpoint       │   q ada?     -> searchMoviesUrl(...)
        └──────────┬───────────┘
                   ▼
        ┌──────────────────────┐
        │ useFetch(url)        │   fetch -> { data, loading, error }
        └──────────┬───────────┘
                   ▼
        ┌──────────────────────┐        ┌───────────────────────┐
        │ TMDB API             │ <----> │ .env (API key)        │
        │ api.themoviedb.org/3 │        │ dibaca di tmdb.js     │
        └──────────┬───────────┘        └───────────────────────┘
                   ▼
        ┌──────────────────────┐
        │ MovieGrid → MovieCard│   tampilkan poster, judul, rating
        └──────────┬───────────┘
                   │  klik ♥
                   ▼
        ┌──────────────────────┐        ┌───────────────────────┐
        │ FavoritesContext     │ -----> │ localStorage browser  │
        │ toggleFavorite()     │ <----- │ key: "favorites"      │
        └──────────────────────┘        └───────────────────────┘
                   │
                   ▼
        Navbar (badge jumlah) + FavoritesPage (daftar) ikut update otomatis
```

## Pohon Component

```
main.jsx
└── <BrowserRouter>
    └── <FavoritesProvider>            <- Context: dibungkus di luar supaya semua halaman bisa akses
        └── <App>                      <- daftar Route
            └── <Layout>               <- Navbar + Outlet + footer
                ├── <Navbar />
                └── <Outlet />  ─┬─ HomePage
                                 │    ├── SearchBar
                                 │    ├── FilterBar
                                 │    ├── MovieGrid ── MovieCard ── RatingBadge, FavoriteButton
                                 │    ├── EmptyState / ErrorState
                                 │    └── Pagination
                                 ├─ MovieDetailPage
                                 │    ├── RatingBadge, FavoriteButton
                                 │    └── MovieGrid (film mirip)
                                 ├─ FavoritesPage
                                 │    └── MovieGrid / EmptyState
                                 └─ NotFoundPage
```

## Peta Fitur → File

| Fitur | File utama | Konsep React yang dipakai |
|---|---|---|
| Koneksi TMDB | `api/tmdb.js`, `hooks/useFetch.js` | `fetch`, `useEffect`, custom hook |
| API key di `.env` | `.env`, `api/tmdb.js` | `import.meta.env` |
| Search | `SearchBar.jsx`, `HomePage.jsx`, `useDebounce.js` | controlled input, `useEffect` + cleanup |
| Filter | `FilterBar.jsx`, `HomePage.jsx` | `useSearchParams`, lifting state up |
| Pagination | `Pagination.jsx`, `HomePage.jsx` | props, kalkulasi array |
| Detail film | `MovieDetailPage.jsx` | `useParams`, conditional rendering |
| Favorit | `FavoritesContext.jsx`, `FavoriteButton.jsx`, `useLocalStorage.js` | Context, `localStorage`, immutability |
| UI/UX | semua component | Tailwind, state loading/error/empty, aksesibilitas |

## Endpoint TMDB yang Dipakai

| Kebutuhan | Endpoint | Parameter penting |
|---|---|---|
| Daftar film + filter + urutan | `GET /discover/movie` | `page`, `sort_by`, `primary_release_year`, `vote_average.gte`, `vote_count.gte` |
| Cari judul | `GET /search/movie` | `query`, `page`, `primary_release_year` |
| Detail + pemeran + trailer + mirip | `GET /movie/{id}` | `append_to_response=credits,videos,similar` |
| Gambar poster | `https://image.tmdb.org/t/p/{ukuran}{path}` | ukuran: `w185`, `w500`, `w1280` |

Semua request butuh `api_key=...` di query string.

## Urutan Kerja

```
01 setup  ->  02 api  ->  03 layout  ->  04 kartu+grid  ->  05 search
                                                                │
        10 polish  <-  09 favorit  <-  08 detail  <-  07 pagination  <-  06 filter
```

Di akhir **tiap** file ada aplikasi yang bisa dijalanin. Jadi kalau macet, kamu selalu punya versi terakhir yang jalan.

Lanjut ke [01-setup-project-dan-tmdb.md](01-setup-project-dan-tmdb.md).
