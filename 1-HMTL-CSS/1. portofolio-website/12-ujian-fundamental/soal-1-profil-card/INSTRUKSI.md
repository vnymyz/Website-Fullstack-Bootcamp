# Soal 1 — Profile Card (HTML + CSS)

Waktu: ~20 menit. **Tanpa AI.**

## Yang Harus Dibikin

Bikin 1 kartu profil (profile card) yang isinya:

1. Foto/avatar (boleh pakai placeholder warna kotak/lingkaran kalau nggak ada gambar, nggak wajib gambar asli)
2. Nama
3. Jabatan/role (contoh: "Web Developer")
4. Deskripsi singkat (2-3 kalimat)
5. Minimal 3 skill/tag (contoh: HTML, CSS, JavaScript) — ditampilkan sebagai pills/badge, bukan list biasa
6. Minimal 2 tombol/link (contoh: "Contact Me", "View Portfolio")

## Requirement Teknis

- Pakai **semantic tags** yang sesuai (bukan cuma `<div>` semua) — `<article>` atau `<section>` buat card-nya, `<h2>`/`<h3>` buat nama, dll
- Layout skill/tag pakai **flexbox** (`display: flex`), bukan inline manual
- Card harus **responsive**: di layar HP (di bawah 500px lebar), card full-width. Di layar desktop, card di tengah dengan lebar maksimal (misal `max-width: 350px`)
- Pakai minimal 1 **CSS variable** (`:root { --warna-utama: ...; }`) buat salah satu warna yang dipakai berkali-kali
- Ada efek `:hover` di tombol

## Tools/Syntax yang Dipakai

Ini daftar tag/property yang bakal kepake — bukan urutan kode, cuma daftar "alat" yang harus kamu tau cara pakainya:

- Tag HTML: `<article>` atau `<section>`, `<img>` (atau `<div>` kalau pakai placeholder warna), `<h2>`, `<h3>`, `<p>`, `<div>` buat wadah skill pills, `<button>` atau `<a>`
- CSS layout: `display: flex`, `gap`, `flex-wrap: wrap` (buat skill pills), `justify-content`
- CSS variable: `:root { --nama-variabel: nilai; }` buat definisi, `var(--nama-variabel)` buat pakainya
- CSS responsive: `@media (max-width: 500px) { ... }`, `max-width` di card
- CSS state: `:hover` di selector tombol

## File yang Dipakai

Edit `index.html` dan `style.css` yang udah disediakan di folder ini. Jangan bikin file baru.

## Ditanya Pas Review

- "Kenapa kamu pilih semantic tag ini, bukan div?"
- "Coba jelasin flexbox property yang kamu pakai buat susun skill pills"
- "Kenapa pakai CSS variable di sini?"
