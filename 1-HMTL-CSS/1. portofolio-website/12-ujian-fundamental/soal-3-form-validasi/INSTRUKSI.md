# Soal 3 — Form Registrasi + Validasi

Waktu: ~20 menit. **Tanpa AI.**

## Yang Harus Dibikin

Bikin form registrasi sederhana dengan field:

- Username (text)
- Email (text)
- Password (password)

Dengan validasi saat form di-submit:

1. Kalau ada field yang kosong → tampilkan pesan error "Semua field harus diisi!" dan **jangan lanjut** (jangan tampilkan pesan sukses)
2. Kalau email diisi tapi **tidak mengandung karakter `@`** → tampilkan pesan error "Email tidak valid!"
3. Kalau password **kurang dari 6 karakter** → tampilkan pesan error "Password minimal 6 karakter!"
4. Kalau semua validasi lolos → tampilkan pesan sukses "Registrasi berhasil! Selamat datang, [username]!" dan kosongkan semua field

## Requirement Teknis

- Form **wajib** pakai `<form>` dan tombol `type="submit"` (bukan `type="button"`)
- Wajib pakai event `"submit"` di form (`form.addEventListener("submit", ...)`)
- Wajib pakai `e.preventDefault()` — halaman **tidak boleh reload** sama sekali pas submit
- Urutan pengecekan penting: cek kosong dulu, baru cek format email, baru cek panjang password. Kalau salah satu gagal, langsung `return` — jangan lanjut cek yang lain
- Pesan error pakai warna beda dari pesan sukses (contoh: error merah, sukses hijau)

## Tools/Syntax yang Dipakai

- HTML: `<form>`, `<input type="text">`, `<input type="password">`, `<button type="submit">`
- JS ambil elemen: `document.querySelector()`
- JS event: `form.addEventListener("submit", function (e) { ... })`
- JS stop reload: `e.preventDefault()` — harus baris **pertama** di dalam function
- JS ambil isi input: `.value`, dan `.trim()` buat buang spasi kosong di username/email
- JS cek string: `.includes("@")` buat cek email, `.length` buat cek panjang password
- JS ubah tampilan pesan: `.textContent`, `.className` (buat ganti class error/sukses)
- 3 blok `if (...) { ...; return; }` terpisah, urut sesuai requirement

## File yang Dipakai

Edit `index.html`, `style.css`, `script.js` yang udah disediakan.

## Ditanya Pas Review

- "Kenapa `e.preventDefault()` harus dipanggil paling awal di function submit?"
- "Kenapa urutan pengecekan (kosong → email → password) penting?"
- "Coba jelasin cara kamu ngecek email itu 'valid' — pakai method apa?"
