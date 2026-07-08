# Soal 5 — Bug Hunt (Debugging)

Waktu: ~15 menit. **Tanpa AI.**

## Konteks

Ada aplikasi kecil "Simple Cart" di folder ini — tombol tambah item, counter jumlah item, total harga, dan dark mode toggle. **Kodenya sengaja dirusak.** Ada **5 bug** tersebar di `index.html`, `style.css`, dan `script.js`.

## Yang Harus Dilakukan

1. Buka `index.html` di browser, coba semua fitur (tombol tambah item, dark mode)
2. Cari kelakuan yang **tidak sesuai ekspektasi** (baca deskripsi tiap fitur di bawah)
3. Buka DevTools (F12) → tab Console → lihat kalau ada error merah
4. Cari baris kode yang jadi penyebabnya, benerin
5. Test ulang di browser sampai semua fitur jalan normal

## Fitur yang Seharusnya Jalan

1. **Tombol "+ Tambah Item"** → jumlah item nambah 1, total harga nambah sesuai harga per item (Rp 10.000)
2. **Dark mode toggle** → klik tombol, background berubah gelap, teks tombol berubah jadi "☀️ Light Mode"
3. **Form "Tambah Item Custom"** → ketik nama item, submit (klik tombol atau tekan Enter) → nama item muncul di list di bawahnya, **halaman tidak boleh reload**
4. **Reset button** → jumlah item dan total kembali ke 0

## Kategori Bug yang Ada (Petunjuk Umum, Bukan Lokasi)

5 bug-nya masuk 5 kategori beda — ini buat bantu kamu tau harus ngecek apa, bukan kasih tau di mana:

1. **Selector nggak nyambung** — ada `id` yang dipanggil di JS tapi beda sama `id` di HTML. Cek satu-satu tiap `querySelector` di `script.js`, cocokin ke `index.html`.
2. **Tipe data ketuker** — ada angka yang diperlakukan kayak teks (atau sebaliknya). Ini nggak bikin error di console, tapi hasilnya keliatan aneh (angkanya nggak masuk akal) pas dites manual.
3. **Nama class nggak nyambung antar file** — class yang di-toggle lewat JS beda nama sama class yang didefinisikan di CSS. Nggak ada error console, harus dicek pakai DevTools → Elements, bandingin ke `style.css`.
4. **Ada satu method penting yang lupa dipanggil** — salah satu function events di form ini kelupaan 1 baris yang harusnya ada paling awal, biar nggak reload.
5. **Logic-nya nggak lengkap** — satu function ngerjain sebagian tugasnya doang, lupa 1 bagian lain yang seharusnya ikut ke-reset/ke-update bareng.

## Aturan

- **Nggak boleh** hapus fitur atau tulis ulang dari nol — cari akar masalahnya, fix sekecil mungkin
- Tulis di komentar dekat baris yang kamu fix: `// FIX: <penjelasan singkat>`
- Kalau nemu bug tapi nggak yakin cara benerinnya, tulis di komentar `// BUG DITEMUKAN: <deskripsi>` dan lanjut ke bug lain — lebih baik dapat 4/5 sebagian daripada stuck di 1 bug terus

## Ditanya Pas Review

- "Coba tunjukin, bug apa yang paling susah dicari? Kenapa susah?"
- "Console error tadi bilang apa? Itu artinya apa?"
- Kamu bakal diminta jelasin tiap fix satu-satu, kenapa itu penyebabnya
