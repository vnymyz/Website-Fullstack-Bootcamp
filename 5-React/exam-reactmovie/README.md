# Ujian React — Website Film (React Movie)

Project ujian React: bikin website film sendiri yang datanya diambil dari **TMDB** (The Movie Database). Ini ujian praktis, jadi tujuannya bukan cuma "jadi", tapi kamu **ngerti** kenapa tiap bagian ditulis begitu.

Folder ini isinya **dua versi** dokumen. Pakai yang sesuai kebutuhan:

| Versi | Folder | Isinya | Dipakai buat |
|---|---|---|---|
| **Panduan Lengkap** | [`panduan-lengkap/`](panduan-lengkap/) | Langkah demi langkah, **kode lengkap** di tiap langkah, ada "Cek" dan tabel "Kalau Error" | Latihan / belajar sebelum ujian, atau ujian "ikuti panduan lalu jelasin" |
| **Versi Ujian** | [`versi-ujian/`](versi-ujian/) | Soal + spesifikasi + hint + potongan kode kecil. **Bukan** solusi lengkap | Ujian beneran: murid bikin sendiri dari nol |

Saran alur buat guru: murid ngerjain **Panduan Lengkap** dulu di sesi latihan, lalu ujian pakai **Versi Ujian** (project baru, tanpa buka panduan lengkap).

---

## Fitur yang Harus Jadi

| # | Fitur | Dibahas di |
|---|---|---|
| 1 | UI/UX rapi & nyaman (dark theme, responsif, skeleton loading, empty/error state, aksesibel) | Semua langkah, dirapikan di [10](panduan-lengkap/10-polish-ui-ux.md) |
| 2 | Terhubung ke API TMDB | [02](panduan-lengkap/02-api-layer-tmdb.md) |
| 3 | API key disimpan di `.env` | [01](panduan-lengkap/01-setup-project-dan-tmdb.md) |
| 4 | Cari film berdasarkan judul | [05](panduan-lengkap/05-search-movie.md) |
| 5 | Filter: tahun rilis, rating bintang, popularitas | [06](panduan-lengkap/06-filter-film.md) |
| 6 | Halaman detail film (sinopsis, genre, durasi, pemeran, trailer) | [08](panduan-lengkap/08-detail-film.md) |
| 7 | Love/like film → tersimpan di halaman Favorit | [09](panduan-lengkap/09-favorit.md) |
| 8 | Tanpa login / auth | — (sengaja tidak ada) |
| 9 | Pagination | [07](panduan-lengkap/07-pagination.md) |

## Tech Stack

| Yang dipakai | Kenapa |
|---|---|
| Vite + React 19 | Dasar project (sesi 1) |
| Tailwind CSS v4 | Styling cepat (sesi 2) |
| react-router-dom 7 | Multi halaman + parameter URL (sesi 7) |
| lucide-react | Ikon rapi (Search, Heart, Star, dll) biar UI kelihatan profesional |
| `fetch` + custom hook | Ambil data tanpa library tambahan (sesi 6 & 8) |
| Context + localStorage | Simpan favorit tanpa database (sesi 8) |

**Sengaja tidak dipakai:** axios, React Query, Redux, shadcn/MUI. Ujian ini ngetes fundamental React, bukan hafalan library.

## Prasyarat

- Sudah selesai **react-journey sesi 1–8** (state, effect, fetch, router, custom hook, context).
- Node.js terpasang (`node -v` jalan).
- Punya email aktif buat daftar akun TMDB (gratis).
- Text editor (VS Code) dan browser dengan DevTools (`F12`).

## Urutan Baca — Panduan Lengkap

| File | Isi |
|---|---|
| [00-gambaran-besar.md](panduan-lengkap/00-gambaran-besar.md) | **Baca dulu.** Struktur folder, alur data, peta halaman |
| [01-setup-project-dan-tmdb.md](panduan-lengkap/01-setup-project-dan-tmdb.md) | Akun TMDB, API key, bikin project, `.env` |
| [02-api-layer-tmdb.md](panduan-lengkap/02-api-layer-tmdb.md) | File `tmdb.js` + test koneksi |
| [03-layout-navbar-routing.md](panduan-lengkap/03-layout-navbar-routing.md) | Layout, navbar, route |
| [04-movie-card-dan-grid.md](panduan-lengkap/04-movie-card-dan-grid.md) | Kartu film, grid, loading/error/empty state |
| [05-search-movie.md](panduan-lengkap/05-search-movie.md) | Pencarian + debounce |
| [06-filter-film.md](panduan-lengkap/06-filter-film.md) | Filter tahun, rating, urutan |
| [07-pagination.md](panduan-lengkap/07-pagination.md) | Pagination |
| [08-detail-film.md](panduan-lengkap/08-detail-film.md) | Halaman detail |
| [09-favorit.md](panduan-lengkap/09-favorit.md) | Fitur love + halaman Favorit |
| [10-polish-ui-ux.md](panduan-lengkap/10-polish-ui-ux.md) | Rapikan UI/UX + checklist akhir |

## Urutan Baca — Versi Ujian

| File | Isi |
|---|---|
| [00-soal-dan-spesifikasi.md](versi-ujian/00-soal-dan-spesifikasi.md) | Soal, aturan, kriteria tiap fitur |
| [01-petunjuk-dan-hint.md](versi-ujian/01-petunjuk-dan-hint.md) | Tabel endpoint TMDB + hint per fitur |
| [02-rubrik-penilaian.md](versi-ujian/02-rubrik-penilaian.md) | Rubrik nilai + pertanyaan lisan |

## Cara Pakai Panduan Lengkap

- Kerjain **berurutan**: Langkah 1, Langkah 2, Langkah 3, dst. Jangan loncat.
- Tiap langkah ngasih **file lengkap** (path-nya di atas kode). Ketik sendiri, jangan copy-paste mentah. Setelah selesai, baca lagi per baris dan tebak fungsinya.
- Tiap langkah diakhiri **Cek**. Kalau belum cocok, jangan lanjut.
- Di akhir tiap file ada tabel **Kalau Error**.
- Buka **Console** browser (`F12`) terus selama ngerjain. Error React hampir selalu kebaca di sana.

## Peringatan Soal API Key

API key TMDB itu **gratis dan pribadi**. Jangan di-commit ke GitHub, jangan di-screenshot, jangan dikirim di chat. Di project ini key disimpan di `.env` yang masuk `.gitignore`. Catatan jujur: karena React jalan di browser, key tetap bisa dilihat orang lewat DevTools. Untuk project beneran, request ke TMDB harusnya lewat backend (kamu belajar itu nanti di tahap Golang).
