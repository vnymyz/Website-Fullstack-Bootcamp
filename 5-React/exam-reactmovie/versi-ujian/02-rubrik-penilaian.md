# Rubrik Penilaian

Total **100 poin** = 60 poin fitur + 20 poin kualitas kode (dinilai dari aplikasi) + 20 poin tanya jawab lisan.

Ingat aturan ujian: bagian yang tidak bisa dijelaskan murid saat tanya jawab **dianggap tidak sah**, dan poin bagian itu dikurangi sesuai tabel di bawah.

## A. Fitur (60 poin)

| # | Fitur | Poin | Kriteria ringkas |
|---|---|---|---|
| F1 | UI/UX ramah | 11 | Responsif tanpa scroll horizontal (2), skeleton/loading (2), error + Coba lagi (2), empty state (2), fallback poster (1), fokus keyboard & aria (2) |
| F2 | Terhubung TMDB | 6 | Data asli (3), semua pemanggilan TMDB di satu tempat (3) |
| F3 | API key di `.env` | 6 | Dibaca via `import.meta.env` (2), tidak ada key di kode (2), `.env` di-ignore + ada `.env.example` (2). **Key ter-commit: 0 untuk F3** |
| F4 | Cari film | 7 | Cari berfungsi (2), debounce (2), tombol hapus (1), tersimpan di URL (1), hasil kosong ditangani (1) |
| F5 | Filter | 8 | Tahun (2), rating (2), popularitas/urutan (2), kombinasi + tombol reset (1), tersimpan di URL (1) |
| F6 | Detail film | 7 | Route dinamis (1), info utama (2), sinopsis (1), pemeran (1), trailer aman jika kosong (1), navigasi kembali + data kosong tidak error (1) |
| F7 | Favorit | 7 | Toggle hati (2), halaman Favorit (2), bertahan setelah refresh (2), badge navbar + status konsisten (1) |
| F8 | Tanpa login | 1 | Tidak ada auth |
| F9 | Pagination | 7 | Tampil & berfungsi (2), Prev/Next mati di ujung (1), reset ke halaman 1 saat filter/cari berubah (2), batas 500 (1), jendela `…` (1) |

Jumlah: 11+6+6+7+8+7+7+1+7 = **60**.

## B. Kualitas Kode (20 poin)

| Aspek | Poin | Yang dilihat |
|---|---|---|
| Struktur folder & pemisahan tanggung jawab | 5 | `api/`, `components/`, `hooks/`, `context/`, `pages/` jelas. Component kecil dan fokus, tidak ada satu file raksasa |
| Penggunaan hook & konsep React yang benar | 5 | Dependency array benar, cleanup effect, tidak memutasi state, `key` benar, tidak ada state yang seharusnya bisa dihitung |
| Custom hook & Context dipakai tepat | 4 | `useFetch`/`useDebounce`/`useLocalStorage` reusable; Context tidak berlebihan |
| Kebersihan | 3 | Tidak ada kode mati / `console.log` tertinggal; `npm run lint` tanpa error; `npm run build` berhasil |
| Git | 3 | Minimal 5 commit bermakna, tidak ada `.env`/`node_modules` di repo, README menjelaskan cara menjalankan |

## C. Tanya Jawab Lisan (20 poin)

Penilai memilih **5 pertanyaan** dari daftar di bawah (4 poin per pertanyaan): 1 dari kelompok A, 1 dari B, 1 dari C, dan 2 bebas. Penilai boleh meminta murid membuka kodenya sendiri dan **mengubah sesuatu secara langsung** (live modification).

| Skor per pertanyaan | Arti |
|---|---|
| 4 | Menjawab benar, menunjuk kode yang tepat, paham alasannya |
| 3 | Benar tapi kurang alasan / sedikit ragu |
| 2 | Paham sebagian |
| 1 | Hanya bisa membaca kodenya, tanpa alasan |
| 0 | Tidak bisa menjelaskan sama sekali |

### Kelompok A — Data & API

1. Tunjukkan di kodemu di mana API key dibaca. Kenapa harus diawali `VITE_`? Apakah key itu benar-benar rahasia di aplikasi React? Kenapa?
2. Kenapa pemanggilan TMDB dikumpulkan di satu file? Apa untungnya kalau nanti alamat API berubah?
3. Jelaskan `useFetch` baris per baris. Apa fungsi array dependency-nya? Apa yang terjadi kalau dikosongkan?
4. Apa tujuan cleanup di effect fetch? Berikan skenario di mana tanpa cleanup terjadi bug.
5. Kenapa `poster_path` tidak bisa langsung dipakai sebagai `src`? Bagaimana kamu menangani `null`?
6. Endpoint `/search/movie` tidak punya filter rating. Bagaimana kamu menanganinya, dan kenapa memilih cara itu?

### Kelompok B — State & Alur

7. Jelaskan debounce dengan kata-katamu. Kenapa `clearTimeout` ada di cleanup?
8. Kenapa pencarian & filter disimpan di URL, bukan `useState`? Sebutkan minimal dua keuntungan.
9. Di baris mana halaman di-reset ke 1? Apa yang terjadi kalau baris itu dihapus? (Minta murid mendemonstrasikan.)
10. Kenapa nilai `page` dari URL harus dikonversi? Apa akibatnya kalau tidak?
11. Dari mana angka 500 pada pagination berasal? Apa yang terjadi kalau kamu abaikan?
12. Jelaskan algoritma jendela halaman (`1 … 3 4 [5] 6 7 … 500`) dengan contoh untuk `page=1` dan `page=500`.

### Kelompok C — Favorit & UI

13. Kenapa favorit memakai Context, bukan props? Gambarkan siapa saja yang memakai `useFavorites()`.
14. Kenapa `toggleFavorite` memakai `filter` dan spread, bukan `push`/`splice`?
15. Kenapa tombol hati bukan anak dari `<Link>`? Apa masalahnya kalau dijadikan anak?
16. Kenapa `useLocalStorage` memakai fungsi di `useState(() => ...)`? Apa bedanya dengan `useState(JSON.parse(...))`?
17. Apa yang terjadi kalau user membuka aplikasi di HP yang berbeda? Kenapa? Bagaimana cara mengatasinya di aplikasi sungguhan?
18. Tunjukkan tiga hal yang kamu lakukan untuk aksesibilitas. Apa bedanya `aria-label` dan `alt`?
19. Apa yang terjadi kalau film tidak punya poster / sinopsis / trailer? Tunjukkan kodenya.

### Live modification (pilih satu, opsional sebagai pengganti satu pertanyaan)

| Permintaan | Yang dinilai |
|---|---|
| "Tambahkan filter urutan baru: *Terlama*" | Paham alur dari dropdown → URL → API |
| "Ubah jendela pagination jadi ±3 halaman" | Paham algoritma `getPages` |
| "Tampilkan jumlah favorit di judul halaman Favorit" | Paham Context |
| "Ganti debounce jadi 1 detik" | Paham hook `useDebounce` |
| "Tampilkan 8 pemeran, bukan 12" | Paham data detail |
| "Sembunyikan film tanpa poster dari hasil" | Paham manipulasi array data |

## D. Penalti

| Pelanggaran | Akibat |
|---|---|
| API key asli ada di repository (riwayat Git) | F3 = 0 dan minus 5 poin |
| Memakai library terlarang (Redux, axios, React Query, UI kit) | Minus 5 poin per library |
| Terbukti memakai AI / menyalin pekerjaan orang lain | Ujian dianggap gagal, diulang dengan soal berbeda |
| `npm install` + `npm run dev` gagal dijalankan (tanpa alasan `.env`) | Maksimal nilai 60 |
| Tidak ada tanda tangan jawaban lisan (tidak hadir tanya jawab) | Bagian C = 0 |

## E. Predikat

| Nilai | Predikat | Arti |
|---|---|---|
| 90–100 | Sangat baik | Siap lanjut ke tahap backend |
| 75–89 | Baik | Lanjut; ulangi bagian lisan yang lemah |
| 60–74 | Cukup | Ulangi topik yang lemah sebelum lanjut |
| < 60 | Belum lulus | Ulangi panduan, ujian ulang dengan soal berbeda |

## Lembar Penilaian (fotokopi / salin)

```
Nama murid : ____________________     Tanggal : ____________

A. FITUR (maks 60)
 F1 UI/UX ....../11    F2 TMDB ....../6     F3 .env ....../6
 F4 Cari ....../7      F5 Filter ....../8   F6 Detail ....../7
 F7 Favorit ....../7   F8 Tanpa login ....../1   F9 Pagination ....../7
 Nilai A: ...... /60

B. KUALITAS KODE
 Struktur ....../5  Hook & konsep ....../5  Custom hook/Context ....../4
 Kebersihan ....../3  Git ....../3          Nilai B: ....../20

C. LISAN (5 pertanyaan x 4)
 No. ___ ..../4   No. ___ ..../4   No. ___ ..../4   No. ___ ..../4   No. ___ ..../4
 Nilai C: ....../20

D. Penalti: ....................................  (-) ......

NILAI AKHIR = A + B + C - penalti poin : ......
Predikat : ............
```
