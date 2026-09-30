# Ujian React — Website Film (Versi Ujian)

## Soal

Buat website film menggunakan **React JS** dengan data dari **TMDB API** (https://www.themoviedb.org/). Website harus punya tampilan yang nyaman dipakai (UI/UX), bisa mencari dan memfilter film, menampilkan detail film, menyimpan film favorit, dan punya pagination.

Kamu mengerjakan **dari nol** di project baru. Dokumen ini hanya berisi **apa yang harus jadi** (spesifikasi). Cara membuatnya adalah tugasmu. Petunjuk ada di [01-petunjuk-dan-hint.md](01-petunjuk-dan-hint.md).

## Aturan Ujian

| Aturan | Keterangan |
|---|---|
| Waktu | Ditentukan pengawas (saran: 3–4 jam kerja + 15 menit tanya jawab lisan) |
| Boleh dibuka | Dokumen ini, hint, dokumentasi resmi React, React Router, Tailwind, TMDB, MDN |
| **Tidak boleh** | Panduan Lengkap (`panduan-lengkap/`), project latihan sendiri, AI (ChatGPT/Claude/Copilot dll), copy-paste dari sumber lain |
| Tanya jawab lisan | Setelah selesai, kamu menjelaskan kodemu. Kalau tidak bisa menjelaskan bagian tertentu, nilai bagian itu dianggap tidak sah |
| Commit | Wajib punya riwayat Git minimal 5 commit dengan pesan bermakna |
| API key | Milikmu sendiri. Jangan sampai ter-commit |

## Tech Stack

Wajib: **React (Vite)**, **react-router-dom**, **Tailwind CSS** (atau CSS biasa kalau disetujui pengawas).

Dilarang: library state global (Redux/Zustand), library data-fetching (axios, React Query/SWR), UI kit siap pakai (MUI, shadcn, Bootstrap komponen). Pakai `fetch`, `useState`, `useEffect`, Context, dan custom hook. Library ikon (misal `lucide-react`) **boleh**.

## Spesifikasi Fitur

Tiap fitur punya **kriteria penerimaan** yang bisa diuji. Penilai akan mengujinya satu per satu.

### F1 — UI/UX yang ramah

- [ ] Tampilan konsisten (satu skema warna, satu font) dan enak dibaca.
- [ ] **Responsif:** nyaman di lebar 375 px (HP), 768 px (tablet), 1280 px (laptop). Tidak ada scroll horizontal.
- [ ] Saat data dimuat: ada indikator loading (skeleton / spinner), bukan layar kosong.
- [ ] Saat gagal (offline / key salah): ada pesan error yang jelas dan tombol **Coba lagi**.
- [ ] Saat tidak ada hasil: ada tampilan kosong yang menjelaskan dan memberi saran.
- [ ] Film tanpa poster tetap tampil rapi (ada gambar pengganti).
- [ ] Elemen interaktif punya efek hover dan garis fokus keyboard yang terlihat.
- [ ] Tombol ikon punya `aria-label`; gambar punya `alt`.
- [ ] Ada navbar yang selalu terlihat dengan link ke Beranda dan Favorit, serta footer atribusi TMDB.
- [ ] Judul tab browser berubah sesuai halaman.

### F2 — Terhubung ke API TMDB

- [ ] Data film **asli** dari TMDB (bukan data buatan sendiri).
- [ ] Semua pemanggilan TMDB dikumpulkan di **satu file** (bukan tersebar di banyak component).
- [ ] Gambar poster ditampilkan dengan benar.

### F3 — API key di `.env`

- [ ] API key dibaca dari `.env` lewat `import.meta.env`.
- [ ] **Tidak ada** API key di dalam kode sumber.
- [ ] `.env` masuk `.gitignore`. Ada `.env.example` berisi contoh tanpa key asli.
- [ ] Riwayat Git tidak pernah memuat key asli.

### F4 — Cari film

- [ ] Ada kotak pencarian; mengetik judul menampilkan film yang cocok.
- [ ] Request **tidak** dikirim di setiap huruf (ada penundaan / debounce).
- [ ] Ada tombol untuk menghapus pencarian.
- [ ] Kata kunci tetap ada setelah halaman di-refresh (disimpan di URL).
- [ ] Hasil kosong ditangani dengan tampilan kosong.

### F5 — Filter

Tiga filter, semuanya harus benar-benar mengubah hasil:

- [ ] **Tahun rilis** (dropdown tahun).
- [ ] **Rating bintang minimal** (misal 5+, 6+, 7+, 8+).
- [ ] **Popularitas / urutan:** minimal terpopuler, rating tertinggi, terbaru.
- [ ] Filter bisa dikombinasikan.
- [ ] Ada tombol **Reset filter**.
- [ ] Filter tersimpan di URL dan bertahan setelah refresh.
- [ ] Filter berfungsi bersama pencarian (tahun minimal). Jika ada batasan API untuk sebagian filter saat mencari, tangani dengan jujur (nonaktifkan atau beri catatan).

### F6 — Deskripsi film

- [ ] Klik kartu film membuka halaman detail dengan URL sendiri (contoh `/movie/550`).
- [ ] Menampilkan: judul, poster, **sinopsis**, rating, tahun rilis, durasi, genre.
- [ ] Menampilkan pemeran (minimal 6 orang).
- [ ] Menampilkan trailer jika tersedia (dan tidak error jika tidak ada).
- [ ] Ada cara kembali ke halaman sebelumnya dengan pencarian/filter tetap utuh.
- [ ] Data yang kosong (tanpa poster/sinopsis/trailer) tidak membuat halaman error.

### F7 — Love / like film + halaman Favorit

- [ ] Ada tombol hati di kartu film dan di halaman detail.
- [ ] Klik hati menambah/menghapus film dari favorit; tampilan hati berubah (terisi/kosong).
- [ ] Klik hati di kartu **tidak** membuka halaman detail.
- [ ] Ada halaman `/favorites` yang menampilkan semua film favorit.
- [ ] Favorit **bertahan** setelah refresh dan setelah browser ditutup.
- [ ] Navbar menampilkan jumlah favorit.
- [ ] Halaman Favorit kosong menampilkan tampilan kosong dengan ajakan menjelajah film.
- [ ] Status hati konsisten di semua tempat (kartu, detail, halaman Favorit).

### F8 — Tanpa login

- [ ] Tidak ada halaman login/register/auth. Semua bisa dipakai langsung.

### F9 — Pagination

- [ ] Ada kontrol halaman di bawah daftar film (nomor halaman dan/atau Prev/Next).
- [ ] Klik halaman lain memuat film halaman tersebut dan menggulir ke atas.
- [ ] Tombol Prev mati di halaman pertama; Next mati di halaman terakhir.
- [ ] Halaman aktif terlihat jelas.
- [ ] Nomor halaman tersimpan di URL.
- [ ] Mengubah pencarian atau filter mengembalikan ke halaman 1.
- [ ] Menghormati batas TMDB (maksimal halaman 500).
- [ ] Tidak menampilkan ratusan tombol sekaligus (gunakan jendela halaman dengan `…`).

## Halaman yang Wajib Ada

| URL | Isi |
|---|---|
| `/` | Pencarian, filter, daftar film, pagination |
| `/movie/:id` | Detail film |
| `/favorites` | Daftar favorit |
| URL lain | Halaman 404 |

## Struktur Folder yang Diharapkan

Kamu boleh menyesuaikan nama, tapi **pemisahan tanggung jawabnya** harus terlihat:

```
react-movie/
├── .env                 (tidak di-commit)
├── .env.example
├── .gitignore
└── src/
    ├── api/             semua pemanggilan TMDB
    ├── components/      potongan UI yang dipakai ulang
    ├── context/         state global (favorit)
    ├── hooks/           custom hook (fetch, debounce, localStorage)
    ├── pages/           satu file per halaman
    ├── utils/           fungsi bantu murni (format tanggal, dll)
    ├── App.jsx          daftar route
    └── main.jsx
```

## Yang Dikumpulkan

1. Link repository Git (tanpa `.env`).
2. `README.md` singkat berisi: cara menjalankan, cara mengisi `.env`, dan daftar fitur yang berhasil.
3. Project bisa dijalankan dengan `npm install` lalu `npm run dev` setelah `.env` diisi.
4. `npm run build` berhasil tanpa error.

## Bonus (opsional, nilai tambah kecil)

- Favorit bisa dihapus semua sekaligus dengan konfirmasi.
- Filter genre memakai endpoint `/genre/movie/list`.
- Mode "Muat lebih banyak" sebagai alternatif pagination (tetap sediakan pagination).
- Menyimpan pilihan urutan terakhir di `localStorage`.
- Animasi transisi halus yang menghormati `prefers-reduced-motion`.

Penilaian lengkap: [02-rubrik-penilaian.md](02-rubrik-penilaian.md).
