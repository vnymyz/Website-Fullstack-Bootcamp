# Soal 2 — Like Button & Toggle (DOM + Events)

Waktu: ~20 menit. **Tanpa AI.**

## Yang Harus Dibikin

Bikin 2 fitur kecil di halaman ini:

### A. Tombol Like dengan Counter

- Ada tombol ❤️ dan angka di sebelahnya (mulai dari 0)
- Klik tombol → angka nambah 1, tombol berubah warna (misal jadi merah/pink) dan teksnya berubah jadi "Liked"
- Klik lagi (toggle) → angka kurang 1, warna balik normal, teks balik jadi "Like"
- **Ini toggle, bukan cuma nambah terus** — cek dulu kondisinya sebelum nambah/kurang

### B. Show/Hide Panel Info

- Ada tombol "Tampilkan Info" dan sebuah div berisi teks apapun yang defaultnya tersembunyi
- Klik tombol → panel muncul, teks tombol berubah jadi "Sembunyikan Info"
- Klik lagi → panel hilang lagi, teks tombol balik "Tampilkan Info"

## Requirement Teknis

- Wajib pakai `document.querySelector()` buat ambil elemen
- Wajib pakai `addEventListener("click", ...)` — **bukan** `onclick` di HTML
- Show/hide wajib pakai `classList.toggle()` dengan class CSS `.hidden { display: none; }` — bukan `element.style.display` manual
- Like counter wajib pakai variabel biasa (`let jumlahLike = 0`) yang di-update tiap klik

## Tools/Syntax yang Dipakai

- HTML: `<button>` buat tombol, `<span>` buat angka counter, `<div>` buat panel info
- JS ambil elemen: `document.querySelector("#id")`
- JS event: `.addEventListener("click", function () { ... })`
- JS ubah tampilan: `.textContent = "..."`, `.classList.toggle("hidden")`, `.classList.contains("hidden")`
- JS state: butuh 1 variabel `let` buat nyimpen status like (true/false) supaya toggle-nya konsisten — bukan cuma ngecek angka doang
- Operator `if/else` buat cabang logic (nambah vs kurang, tampil vs sembunyi)

## File yang Dipakai

Edit `index.html`, `style.css`, `script.js` yang udah disediakan. `script.js` sudah di-link di HTML.

## Ditanya Pas Review

- "Kenapa like button ini disebut toggle? Coba jelasin logikanya"
- "Bedanya `classList.toggle()` sama `element.style.display = 'none'` apa?"
- "Kenapa `addEventListener` lebih dipilih daripada `onclick` di HTML?"
