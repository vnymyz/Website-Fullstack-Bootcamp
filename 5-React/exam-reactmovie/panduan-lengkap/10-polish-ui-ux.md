# 10 — Polish UI/UX & Checklist Akhir

**Hasil akhir:** aplikasi lulus semua fitur soal, bisa dipakai pakai keyboard, nyaman di HP, `npm run build` dan `npm run lint` bersih, dan kamu siap menjelaskan tiap bagian.

**Sebelum mulai:** [09](09-favorit.md) selesai.

## Konsep singkat: apa itu UI/UX yang "baik"?

Bukan soal hiasan. Ini daftar prinsip yang sudah kamu pasang sejak file 01, dan sekarang kita audit:

| Prinsip | Wujud di project ini |
|---|---|
| **Selalu beri tahu user apa yang terjadi** | Skeleton saat loading, `ErrorState` + "Coba lagi", `EmptyState` + tombol aksi, jumlah hasil |
| **Cegah layout loncat** | Skeleton berukuran sama dengan kartu asli (`aspect-[2/3]`) |
| **Data bisa kosong** | Poster tak ada → ikon. Sinopsis tak ada → teks cadangan. Trailer tak ada → bagian hilang |
| **Bisa dipakai tanpa mouse** | Semua tombol/link bisa di-`Tab`, ada garis fokus (`focus-visible:outline-*`) |
| **Bisa dipakai pembaca layar** | `aria-label` di tombol ikon, `aria-pressed`, `aria-current`, `aria-live`, `alt` di gambar |
| **Nyaman di HP** | Grid 2→5 kolom, filter 2 kolom di HP, tidak ada scroll horizontal |
| **Hormati preferensi user** | `prefers-reduced-motion` mematikan animasi (sudah di `index.css`) |
| **URL jujur** | Pencarian/filter/halaman ada di URL, Back berfungsi, bisa di-share |
| **Cepat** | `loading="lazy"` di gambar, debounce mengurangi request |

## Langkah 1 — Tambah "Lewati ke konten" di Layout

Pengguna keyboard harus menekan `Tab` melewati seluruh navbar tiap membuka halaman. Link ini muncul hanya saat difokus dan langsung melompat ke isi halaman.

**File: `src/Layout.jsx`** (ganti seluruh isi)

```jsx
import { Outlet } from "react-router-dom";
import Navbar from "./components/Navbar.jsx";

export default function Layout() {
  return (
    <div className="flex min-h-screen flex-col">
      {/* [BARU] tersembunyi (sr-only), muncul saat ditekan Tab pertama kali */}
      <a
        href="#konten"
        className="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-accent focus:px-4 focus:py-2"
      >
        Lewati ke konten
      </a>

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

`sr-only` = disembunyikan secara visual tapi tetap terbaca pembaca layar. `focus:not-sr-only` = saat difokus, tampil normal.

## Langkah 2 — Uji keyboard

Tutup mouse, lalu hanya pakai keyboard:

1. Refresh halaman, tekan `Tab` sekali → muncul tombol merah "Lewati ke konten". Tekan `Enter` → fokus lompat ke isi.
2. `Tab` terus: navbar → kotak cari → 3 dropdown → Reset → kartu film + tombol hati → pagination.
3. Di dropdown, ubah pilihan pakai panah atas/bawah.
4. Pada tombol hati tekan `Enter` atau `Space`.
5. Di setiap elemen yang difokus harus terlihat **garis fokus**. Kalau ada yang tidak terlihat, tambahkan `focus-visible:outline-2 focus-visible:outline-accent`.

## Langkah 3 — Uji responsif

`F12` → ikon **Toggle device toolbar** (`Ctrl+Shift+M`). Coba lebar berikut:

| Lebar | Yang diharapkan |
|---|---|
| 375 px (HP) | Grid 2 kolom, filter 2×2, navbar tidak berantakan, detail: poster di atas |
| 768 px (tablet) | Grid 4 kolom, filter 1 baris |
| 1280 px+ (laptop) | Grid 5 kolom, konten di tengah (maks 80rem) |

Cari **scroll horizontal**. Kalau ada, biasanya ada elemen dengan lebar tetap. Ganti jadi `w-full max-w-*`.

## Langkah 4 — Audit Lighthouse

`F12` → tab **Lighthouse** → centang Performance, Accessibility, Best Practices, SEO → **Analyze page load**.

Target: **Accessibility ≥ 90**. Kalau ada temuan, baca penjelasannya dan perbaiki. Yang paling sering:

| Temuan | Perbaikan |
|---|---|
| Buttons do not have an accessible name | Tambah `aria-label` di tombol ikon |
| Image elements do not have `[alt]` | Tambah `alt` (kosongkan `alt=""` kalau gambar hanya hiasan) |
| Background and foreground colors do not have a sufficient contrast ratio | Ganti `text-gray-500` jadi `text-gray-400` |
| Form elements do not have associated labels | Bungkus dengan `<label>` atau pakai `aria-label` |

Catatan: mode `npm run dev` lebih lambat dari hasil build. Skor Performance yang akurat didapat dari `npm run preview` (Langkah 6).

## Langkah 5 — Uji kasus tepi (edge case)

Aplikasi yang bagus tidak rusak saat kondisinya aneh. Coba semua:

| Skenario | Yang benar |
|---|---|
| Cari `zzzxxqq` | `EmptyState` + tombol "Hapus pencarian" |
| Cari `   ` (spasi saja) | Dianggap tidak mencari, tampil film populer |
| Cari karakter khusus: `a&b=c?` | Tidak crash, tidak merusak URL |
| Tahun 1970 + rating ★ 8+ | Hasil sedikit / kosong, tidak crash |
| `/?page=abc` | Dianggap halaman 1 |
| `/?page=9999` | Aplikasi tidak crash |
| `/movie/abc` | `ErrorState`, tombol Kembali tetap jalan |
| Matikan internet lalu ganti halaman | `ErrorState`, hidupkan lagi → "Coba lagi" berhasil |
| Love 20+ film | Grid Favorit rapi, badge menampilkan angka benar |
| Hapus key `favorites` di DevTools lalu refresh | Favorit kosong, tidak error |
| Isi `favorites` dengan teks ngawur di DevTools lalu refresh | Aplikasi tetap jalan (karena `try/catch` di `useLocalStorage`) |

## Langkah 6 — Build produksi & lint

```bash
npm run lint
npm run build
npm run preview
```

| Perintah | Yang dicek |
|---|---|
| `npm run lint` | Kode bersih: tidak ada import terpakai, aturan hooks dilanggar, dll. Harus **0 error** |
| `npm run build` | Kompilasi berhasil, membuat folder `dist/` |
| `npm run preview` | Menjalankan hasil build di `http://localhost:4173`. Ini versi yang benar-benar dilihat user |

Ingat: `.env` ikut "dibakar" ke hasil build saat `npm run build`. Itu sebabnya key TMDB tetap terlihat di browser. Untuk latihan ini tidak apa-apa (key TMDB gratis dan bisa diganti), tapi jangan lakukan itu untuk key berbayar/rahasia sungguhan.

## Checklist Akhir — 9 Fitur Soal

Centang sendiri satu per satu. Semua harus bisa kamu **demonstrasikan** dan **jelaskan**.

| # | Fitur | Cara membuktikan |
|---|---|---|
| 1 | UI/UX ramah | Skeleton, error, empty state ada. Responsif HP. Keyboard jalan. Lighthouse Accessibility ≥ 90 |
| 2 | Terhubung ke API TMDB | Poster & judul film asli tampil. Tab Network menunjukkan request ke `api.themoviedb.org` |
| 3 | API key di `.env` | Tidak ada key di kode. `.env` masuk `.gitignore`. Ada `.env.example` |
| 4 | Cari film | Ketik judul → hasil. Debounce: 1 request per pencarian |
| 5 | Filter tahun / rating / popularitas | Tiga dropdown mengubah hasil. Reset jalan |
| 6 | Deskripsi film | Halaman detail menampilkan sinopsis, genre, durasi, pemeran, trailer |
| 7 | Love / like → halaman Favorit | Hati di kartu & detail. Halaman `/favorites`. Bertahan setelah refresh |
| 8 | Tanpa login / auth | Tidak ada halaman login. Favorit disimpan di `localStorage` |
| 9 | Pagination | Nomor halaman + panah. Reset ke halaman 1 saat filter berubah. Batas 500 |

## Pertanyaan yang Harus Bisa Kamu Jawab (latihan lisan)

Kalau kamu tidak bisa menjawab ini tanpa melihat kode, baca ulang bagian terkaitnya.

1. Kenapa API key diawali `VITE_`? Kenapa `.env` tidak boleh di-commit? Apakah key-nya benar-benar rahasia di aplikasi React? Kenapa?
2. Kenapa URL dirakit di `tmdb.js`, bukan langsung di component?
3. Apa fungsi array dependency `[url, reloadKey]` di `useFetch`? Apa yang terjadi kalau dikosongkan?
4. Kenapa ada `batal = true` di cleanup `useFetch`? Skenario apa yang dicegah?
5. Jelaskan debounce dengan kata-katamu sendiri. Kenapa `clearTimeout` ada di cleanup?
6. Kenapa state pencarian & filter disimpan di URL, bukan `useState`?
7. Kenapa `page` harus di-reset ke 1 saat filter berubah? Di baris mana itu terjadi?
8. Kenapa pagination berhenti di 500? Dari mana angka itu?
9. Kenapa `/search/movie` beda perlakuannya dengan `/discover/movie` untuk filter rating?
10. Kenapa tombol hati bukan anak dari `<Link>`?
11. Kenapa `toggleFavorite` memakai `filter` dan spread `[...]`, bukan `push`?
12. Kenapa favorit memakai Context, bukan props?
13. Apa yang terjadi kalau user membuka aplikasi di HP lain? (favorit tidak ikut) Kenapa? Bagaimana cara mengatasinya?
14. Apa yang terjadi kalau film tidak punya poster / trailer / sinopsis? Di file mana penanganannya?

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| `npm run lint` mengeluh `'x' is defined but never used` | Import tak terpakai | Hapus import tersebut |
| `react-hooks/exhaustive-deps` | Nilai di dalam effect tidak ada di array dependency | Tambahkan ke array, atau pindahkan logika keluar dari effect |
| `npm run build` gagal: `Could not resolve ...` | Path import / huruf besar-kecil salah (Windows longgar, build ketat) | Samakan persis nama file |
| Halaman `dist` putih setelah build | Path aset salah / `.env` tidak terbaca saat build | Pastikan `.env` ada saat `npm run build` |
| Refresh di `/favorites` pada hosting = 404 | Hosting belum diatur untuk SPA | Atur "fallback semua URL ke `index.html`" (dibahas saat deploy) |
| Garis fokus tidak terlihat | Utility `focus-visible:*` terlewat | Tambahkan ke elemen interaktif tersebut |

## Selanjutnya (bacaan lanjutan, bukan untuk ujian ini)

Project ini sengaja tanpa library tambahan. Di dunia kerja kamu akan bertemu:

| Yang kamu tulis manual di sini | Yang dipakai di dunia kerja |
|---|---|
| `useFetch` (loading/error/cache manual) | **TanStack Query** (cache, retry, refetch otomatis) |
| `FavoritesContext` + `localStorage` | **Zustand / Redux Toolkit** untuk state besar, database + auth untuk sinkron lintas perangkat |
| Key TMDB di `.env` klien | Request lewat **backend sendiri** yang menyimpan key (kamu buat ini di tahap Golang) |
| Komponen dibuat sendiri | **shadcn/ui, Radix** untuk komponen aksesibel siap pakai |
| Halaman selalu dirender di browser | **Next.js** untuk SEO & render di server |

Memahami versi manual ini justru yang membuat kamu cepat belajar semua library di atas.
