# 02 — API Layer TMDB

**Hasil akhir:** satu file `src/api/tmdb.js` yang jadi satu-satunya "pintu" ke TMDB. Kamu sudah bisa melihat data film asli muncul di Console.

**Sebelum mulai:** [01](01-setup-project-dan-tmdb.md) selesai, `API key terbaca: YA ✅`.

## Konsep singkat

Kalau setiap component nulis sendiri `fetch("https://api.themoviedb.org/3/...api_key=...")`, alamat dan key tersebar di mana-mana. Ganti satu hal (misal bahasa) = edit puluhan tempat.

Solusinya: **satu file khusus** yang tau semua soal TMDB. Component cuma bilang "aku mau film populer tahun 2022", dan file ini yang merakit URL-nya.

```
Component ──> discoverMoviesUrl({ year: 2022 }) ──> "https://api.themoviedb.org/3/discover/movie?api_key=...&primary_release_year=2022"
                        (src/api/tmdb.js)
```

File ini **tidak** melakukan `fetch`, hanya **merakit URL**. Yang `fetch` nanti adalah hook `useFetch` (Langkah di file 04). Alasannya: `useFetch(url)` persis pola yang sudah kamu pelajari di sesi 8.

## Langkah 1 — Bikin `src/api/tmdb.js`

**File: `src/api/tmdb.js`**

```js
// SATU-SATUNYA file yang tau alamat & cara ngobrol ke TMDB.
// Component lain cukup manggil fungsi di sini, gak perlu tau URL-nya.

const BASE_URL = "https://api.themoviedb.org/3";
const IMAGE_URL = "https://image.tmdb.org/t/p";
const API_KEY = import.meta.env.VITE_TMDB_API_KEY;

if (!API_KEY) {
  console.warn("VITE_TMDB_API_KEY belum diisi. Cek file .env kamu lalu restart npm run dev.");
}

// Rakit URL lengkap: BASE_URL + path + api_key + parameter lain.
// Parameter yang kosong ("" / null / undefined) otomatis dibuang.
function buildUrl(path, params = {}) {
  const url = new URL(BASE_URL + path);
  url.searchParams.set("api_key", API_KEY);
  url.searchParams.set("language", "en-US");
  Object.entries(params).forEach(([key, value]) => {
    if (value !== "" && value !== null && value !== undefined) {
      url.searchParams.set(key, value);
    }
  });
  return url.toString();
}

// Daftar film (dipakai untuk halaman utama, lengkap dengan filter & urutan)
export function discoverMoviesUrl({ page = 1, year = "", rating = "", sort = "popularity.desc" } = {}) {
  return buildUrl("/discover/movie", {
    page,
    sort_by: sort,
    primary_release_year: year,
    "vote_average.gte": rating,
    "vote_count.gte": 200, // buang film yang cuma di-vote segelintir orang
  });
}

// Cari film berdasarkan judul
export function searchMoviesUrl({ query, page = 1, year = "" }) {
  return buildUrl("/search/movie", {
    query,
    page,
    primary_release_year: year,
  });
}

// Detail satu film + pemeran + trailer + film mirip dalam SATU request
export function movieDetailUrl(id) {
  return buildUrl(`/movie/${id}`, {
    append_to_response: "credits,videos,similar",
  });
}

// Bikin URL gambar. TMDB cuma ngasih potongan path ("/abc.jpg").
// Kalau film gak punya gambar, path-nya null -> kita balikin null juga.
export function imageUrl(path, size = "w500") {
  return path ? `${IMAGE_URL}/${size}${path}` : null;
}
```

## Langkah 2 — Bedah per bagian

| Bagian | Artinya |
|---|---|
| `import.meta.env.VITE_TMDB_API_KEY` | Cara Vite membaca isi `.env`. Ini dipakai **sekali** di file ini saja |
| `buildUrl(path, params)` | Fungsi internal (gak di-export). Merakit URL + `api_key` + parameter |
| `new URL(...)` + `searchParams.set` | Cara aman nyusun query string. Otomatis meng-encode spasi/karakter khusus (`"spider man"` → `spider+man`) |
| Filter `value !== ""` | Kalau user gak pilih tahun (`year=""`), parameternya gak ikut dikirim |
| `language: "en-US"` | Judul & sinopsis bahasa Inggris. Bahasa Indonesia sering kosong untuk film tertentu |
| `sort_by` | Cara urutkan: `popularity.desc` (terpopuler), `vote_average.desc` (rating tertinggi), `primary_release_date.desc` (terbaru) |
| `primary_release_year` | Filter tahun rilis |
| `vote_average.gte` | Rating minimal (gte = *greater than or equal*) |
| `vote_count.gte: 200` | Tanpa ini, urutan "rating tertinggi" dipenuhi film gak dikenal yang punya 1 vote nilai 10 |
| `append_to_response` | Minta data tambahan (pemeran, video, film mirip) dalam satu request, gak perlu 4 kali fetch |
| `imageUrl(path, size)` | Merakit `https://image.tmdb.org/t/p/w500/abc.jpg`. Ukuran: `w185` kecil, `w500` poster, `w1280` backdrop |

## Langkah 3 — Test: lihat data asli di Console

Ganti sementara `src/App.jsx` (nanti diganti lagi di file 03):

**File: `src/App.jsx`**

```jsx
import { useEffect } from "react";
import { discoverMoviesUrl } from "./api/tmdb.js";

export default function App() {
  useEffect(() => {
    fetch(discoverMoviesUrl())
      .then((res) => res.json())
      .then((data) => console.log(data));
  }, []);

  return <h1 className="p-8 text-2xl font-bold">Buka Console (F12) dan lihat datanya</h1>;
}
```

Restart `npm run dev` kalau perlu, buka `http://localhost:5173`, tekan `F12` → tab **Console**.

Kamu akan melihat objek seperti ini (dipotong):

```js
{
  page: 1,
  total_pages: 500,          // catatan: TMDB membatasi maksimal 500 halaman
  total_results: 9990,
  results: [                 // 20 film per halaman
    {
      id: 550,
      title: "Fight Club",
      overview: "Sinopsis film (teks panjang)...",
      poster_path: "/abc123.jpg",                        // BUKAN URL lengkap, cuma potongan (isi aslimu beda)
      release_date: "1999-10-15",
      vote_average: 8.4,
      vote_count: 29000,
      popularity: 71.2
    },
    // ...19 film lain
  ]
}
```

Buka satu objek di `results` dan lihat nama field-nya. Kita akan pakai persis nama-nama ini di component (`poster_path`, `release_date`, `vote_average`).

## Langkah 4 — Test error 401 (opsional tapi penting)

Ubah key di `.env` jadi asal (misal `salah`), restart `npm run dev`, refresh. Di Console kamu lihat:

```js
{ status_code: 7, status_message: "Invalid API key: You must be granted a valid key.", success: false }
```

Ini bentuk error dari TMDB. Nanti `useFetch` mengubahnya jadi pesan ramah buat user. **Kembalikan key yang benar** lalu restart lagi.

## Cek

- [ ] Console menampilkan objek dengan `results` berisi 20 film.
- [ ] Kamu bisa menyebut 3 nama field yang ada di satu film (contoh: `title`, `poster_path`, `vote_average`).
- [ ] Ganti key jadi salah → muncul `Invalid API key`. Key sudah dikembalikan ke yang benar.
- [ ] Buka tab **Network** (`F12`), filter `Fetch/XHR`, klik request `discover/movie`. Kamu bisa lihat URL lengkapnya beserta parameter.

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| `status_code: 7 Invalid API key` | Key kosong / salah / ada tanda kutip | Cek `.env` (lihat aturan di file 01), restart dev server |
| Console: `VITE_TMDB_API_KEY belum diisi` | `.env` tidak terbaca | Root project, nama persis, restart dev server |
| `Failed to fetch` / `net::ERR_...` | Internet mati, atau TMDB diblokir provider | Cek koneksi. Beberapa provider ID memblokir TMDB. Coba ganti DNS/VPN, atau pakai hotspot |
| `Uncaught SyntaxError: The requested module ... does not provide an export named` | Nama export salah ketik | Samakan `discoverMoviesUrl` di import dan di `tmdb.js` |
| `results` kosong (`[]`) | Parameter aneh (misal `year` isi teks) | Cek URL di tab Network, hapus parameter yang mencurigakan |
| Console log muncul dua kali | Normal. `StrictMode` menjalankan effect 2x di mode development | Abaikan, tidak terjadi di production |

Lanjut ke [03-layout-navbar-routing.md](03-layout-navbar-routing.md).
