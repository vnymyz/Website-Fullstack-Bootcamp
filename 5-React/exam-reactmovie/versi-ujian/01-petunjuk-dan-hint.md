# Petunjuk & Hint

Dokumen ini membantu kamu **menemukan arah**, bukan memberi jawaban jadi. Potongan kodenya sengaja kecil dan tidak lengkap. Sisanya kamu susun sendiri.

## 1. Mendapatkan API Key TMDB

1. Daftar di https://www.themoviedb.org/signup, verifikasi email.
2. Profil → **Settings** → **API** → **Create** → **Developer** → isi formulir (Type: Education/Personal, URL: `http://localhost:5173`).
3. Ambil **API Key (v3 auth)**.

## 2. Setup

```bash
npm create vite@latest react-movie -- --template react
cd react-movie
npm install react-router-dom lucide-react tailwindcss @tailwindcss/vite
```

Hint konfigurasi:

- Tailwind v4 dengan Vite: tambahkan `tailwindcss()` ke `plugins` di `vite.config.js`, dan `@import "tailwindcss";` di CSS utama.
- `.env` di **root** project (sejajar `package.json`). Nama variabel harus diawali `VITE_`. Tanpa spasi dan tanda kutip. Restart `npm run dev` setelah mengubahnya.
- Pastikan `.env` ada di `.gitignore`. Cek dengan `git check-ignore .env`.

```js
// membaca variabel dari .env
const API_KEY = import.meta.env.VITE_TMDB_API_KEY;
```

## 3. Endpoint TMDB

Base URL: `https://api.themoviedb.org/3`. Semua request butuh `?api_key=KEY_KAMU`.

| Kebutuhan | Endpoint | Parameter yang berguna |
|---|---|---|
| Daftar film + filter + urutan | `GET /discover/movie` | `page`, `sort_by`, `primary_release_year`, `vote_average.gte`, `vote_count.gte` |
| Cari judul | `GET /search/movie` | `query`, `page`, `primary_release_year` |
| Detail film | `GET /movie/{id}` | `append_to_response=credits,videos,similar` |
| (Bonus) Daftar genre | `GET /genre/movie/list` | — |
| Gambar | `https://image.tmdb.org/t/p/{ukuran}{path}` | ukuran: `w185`, `w500`, `w1280` |

Nilai `sort_by` yang berguna:

| Nilai | Artinya |
|---|---|
| `popularity.desc` | Terpopuler |
| `vote_average.desc` | Rating tertinggi |
| `primary_release_date.desc` | Terbaru |

Bentuk jawaban daftar film:

```js
{ page: 1, total_pages: 500, total_results: 9990, results: [ { id, title, poster_path, release_date, vote_average, overview, ... } ] }
```

Perhatikan:

- `poster_path` **bukan** URL lengkap. Kamu harus menggabungkannya dengan base URL gambar. Nilainya bisa `null`.
- `release_date` bisa string kosong.
- Maksimal halaman yang diterima TMDB adalah **500**.
- `/search/movie` **tidak punya** parameter `vote_average.gte` dan `sort_by`.
- Urutan `vote_average.desc` tanpa `vote_count.gte` akan dipenuhi film asing dengan 1–2 suara. Tambahkan batas jumlah suara.

## 4. Hint per Fitur

### Struktur & pemisahan tanggung jawab

- Buat satu file `api/tmdb.js` yang berisi fungsi-fungsi untuk merakit URL (atau memanggil `fetch`). Component tidak boleh tahu alamat TMDB.
- Untuk merakit query string, `new URL()` dan `url.searchParams.set()` lebih aman daripada menyambung teks dengan `+` (karena otomatis meng-encode spasi dan karakter khusus).

```js
const url = new URL("https://api.themoviedb.org/3/discover/movie");
url.searchParams.set("api_key", API_KEY);
url.searchParams.set("page", 2);
// url.toString() -> string URL lengkap
```

### Custom hook `useFetch`

- Kembalikan tiga hal: `data`, `loading`, `error`.
- Bergantung pada `url`: ketika `url` berubah, fetch ulang.
- Tangani `res.ok === false` dengan melempar `Error`.
- Ingat **cleanup** agar respons lama yang telat tidak menimpa data baru.
- Untuk tombol "Coba lagi" kamu butuh cara memicu effect berjalan lagi.

```js
useEffect(() => {
  let batal = false;
  // ... fetch ...
  // if (!batal) setData(json);
  return () => { batal = true; };
}, [url]);
```

### Loading, error, kosong

Urutan pengecekan yang aman di render:

```
error?   -> tampilkan ErrorState
loading? -> tampilkan skeleton
kosong?  -> tampilkan EmptyState
lainnya  -> tampilkan data
```

Skeleton sebaiknya berukuran sama dengan kartu asli supaya layout tidak loncat.

### Kartu film

- Fallback kalau `poster_path` `null`: tampilkan ikon/kotak abu.
- `loading="lazy"` di `<img>` untuk performa.
- Tombol hati **jangan** ditaruh di dalam `<a>`/`<Link>`. Jadikan saudaranya, lalu posisikan dengan `absolute` di dalam pembungkus `relative`.
- Grid responsif: `grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5`.

### Pencarian + debounce

- Custom hook `useDebounce(value, delay)` memakai `useEffect` + `setTimeout`. Kuncinya ada di **cleanup** (`clearTimeout`).
- Pakai nilai yang sudah di-debounce untuk memilih URL/endpoint, tapi tampilkan nilai asli di kotak input supaya terasa responsif.
- Kalau kata kunci kosong, pakai endpoint `discover`, bukan `search` (endpoint search menolak query kosong).

```js
function useDebounce(value, delay = 500) {
  const [debounced, setDebounced] = useState(value);
  useEffect(() => {
    // pasang timer, dan kembalikan fungsi cleanup yang membatalkannya
  }, [value, delay]);
  return debounced;
}
```

### State di URL

Gunakan `useSearchParams` dari react-router-dom.

```js
const [searchParams, setSearchParams] = useSearchParams();
const q = searchParams.get("q") || "";
const page = Number(searchParams.get("page")) || 1;

// Mengubah satu parameter tanpa menghapus yang lain:
const next = new URLSearchParams(searchParams); // salin dulu!
next.set("year", "2022");
setSearchParams(next);
```

Yang harus kamu pikirkan sendiri:

- Nilai dari URL selalu **teks**. Kapan perlu diubah menjadi angka?
- Bagaimana menghapus parameter kalau nilainya kosong?
- Di mana tepatnya `page` harus dihapus supaya kembali ke halaman 1?
- Ketik cepat memenuhi riwayat browser dengan puluhan entri. Adakah opsi `setSearchParams` yang menimpa, bukan menambah?

### Filter

- Daftar tahun jangan di-hardcode. Buat dari `new Date().getFullYear()` mundur sampai 1970.
- Satu fungsi `onChange(nama, nilai)` di `FilterBar` cukup untuk ketiga dropdown.
- Karena `/search/movie` tidak punya filter rating dan urutan, kamu punya pilihan: (a) nonaktifkan dropdown yang tidak didukung, (b) saring hasil di browser dan beri catatan bahwa hanya menyaring halaman yang tampil. Pilih salah satu dan **jelaskan alasannya** saat ujian lisan.
- Untuk membandingkan rating dari URL, ingat tipe datanya.

### Pagination

- Ambil `total_pages` dari jawaban API, lalu batasi dengan `Math.min(total_pages, 500)`.
- Algoritma jendela halaman: tampilkan halaman aktif ±2, halaman pertama, halaman terakhir, dan `…` bila ada celah.

```
page=5, total=500  ->  1 … 3 4 [5] 6 7 … 500
page=1, total=500  ->  [1] 2 3 … 500
page=500, total=500 -> 1 … 498 499 [500]
```

- Component `Pagination` sebaiknya hanya menerima `page`, `totalPages`, `onChange`. Ia tidak perlu tahu soal URL atau API.
- Setelah pindah halaman: `window.scrollTo({ top: 0, behavior: "smooth" })`.
- Tombol atribut aksesibilitas: `aria-current="page"` untuk halaman aktif.

### Halaman detail

- `useParams()` untuk mengambil `id` dari `/movie/:id`.
- Satu request cukup untuk detail + pemeran + video + film mirip: `append_to_response=credits,videos,similar`.
- Cari trailer di `videos.results` dengan `site === "YouTube"` dan `type === "Trailer"`.
- Sematkan trailer dengan `<iframe src="https://www.youtube-nocookie.com/embed/KEY" ...>`.
- Bagian opsional dirender bersyarat dan pakai optional chaining (`?.`).
- Durasi ada dalam menit (`runtime`). Ubah jadi format jam dan menit.
- Saat pindah dari film ke film mirip, halaman perlu scroll ke atas lagi. Effect apa dan dependency apa yang cocok?

### Favorit

- Simpan di `localStorage` (hanya menyimpan teks: `JSON.stringify` / `JSON.parse`).
- Bungkus dalam custom hook `useLocalStorage(key, initialValue)`. Pakai *lazy initial state* (`useState(() => ...)`) agar `localStorage` dibaca sekali.
- Bagikan lewat Context: sediakan `favorites`, `isFavorite(id)`, `toggleFavorite(movie)`.
- **Jangan** ubah array langsung (`push`, `splice`). Buat array baru (`filter`, spread).
- Simpan data secukupnya (`id`, `title`, `poster_path`, `vote_average`, `release_date`) agar kartu bisa digambar ulang tanpa fetch.
- Provider harus membungkus semua component yang memakai context, termasuk Navbar.

```jsx
// kerangka context
const FavoritesContext = createContext(null);
export function FavoritesProvider({ children }) {
  // state + fungsi
  return <FavoritesContext.Provider value={/* ... */}>{children}</FavoritesContext.Provider>;
}
export function useFavorites() { /* useContext + cek kalau null */ }
```

### UI/UX

- Warna: tentukan 4–5 warna (latar, permukaan, batas, aksen) sekali di CSS, lalu pakai konsisten.
- Ikon tombol tanpa teks wajib punya `aria-label`. Ikon dekoratif beri `aria-hidden="true"`.
- Garis fokus: `focus-visible:outline-2 focus-visible:outline-*`.
- Hormati `prefers-reduced-motion` untuk animasi.
- Uji dengan `F12` → Toggle device toolbar, dan dengan hanya memakai keyboard (`Tab`, `Enter`, `Space`).

## 5. Urutan Kerja yang Disarankan

```
1 setup + .env  ->  2 tmdb.js + tes di Console  ->  3 Layout + route
   ->  4 kartu + grid + 3 keadaan  ->  5 search  ->  6 filter  ->  7 pagination
   ->  8 detail  ->  9 favorit  ->  10 rapikan + uji edge case
```

Setelah **setiap** tahap, jalankan dan pastikan masih jalan, lalu **commit**. Jangan menumpuk semua sampai akhir.

## 6. Kesalahan yang Sering Terjadi

| Gejala | Kemungkinan penyebab |
|---|---|
| `Invalid API key` | `.env` salah folder / nama / ada kutip / server belum di-restart |
| Poster tidak muncul | `poster_path` belum digabung dengan base URL gambar |
| `Cannot read properties of null` | Memakai `data` sebelum datang; lupa `?.` atau pengecekan `loading` |
| Setiap huruf memicu request | Memakai nilai input langsung, bukan yang di-debounce |
| Ganti filter, halaman tetap di 8 | `page` tidak di-reset |
| Filter satu menghapus filter lain | `URLSearchParams` baru dibuat kosong, bukan disalin dari yang lama |
| Klik hati membuka halaman detail | Tombol berada di dalam `<Link>` |
| Favorit hilang saat refresh | Tidak menulis ke `localStorage` / salah key |
| `useX must be used within Provider` | Provider tidak membungkus component itu |
| Angka halaman tidak berubah warna | Membandingkan teks `"5"` dengan angka `5` |
| Hasil pencarian film obscure kosong | Wajar; tampilkan `EmptyState`, bukan layar putih |
