# React JS — Belajar dari Nol

Selamat datang di materi React! Kamu udah paham HTML, CSS, JS, PHP, MySQL, dan Laravel — jadi di sini kita fokus ke cara berpikir baru: **Single Page Application (SPA)**.

## Cara Pakai Materi Ini

Semua panduan ada di **[react-journey/00-daftar-isi.md](react-journey/00-daftar-isi.md)** — 10 sesi, masing-masing file markdown terpisah, isinya penjelasan + kode yang kamu ketik sendiri langkah demi langkah.

**Semua hands-on dikerjakan di satu project: `5-React/learn-react/`.** Gak ada project baru per sesi — kamu edit/tambah file di `learn-react/src/` sambil ngikutin tiap panduan secara berurutan.

## Yang Perlu Diinstall Dulu

1. **Node.js** (versi LTS) — download di [nodejs.org](https://nodejs.org). Ini kayak PHP-nya dunia JavaScript.
2. Cek udah kepasang bener:
   ```
   node -v
   npm -v
   ```
3. Code editor: VS Code.

## Cara Menjalankan `learn-react/`

```
cd ../learn-react
npm install       <- sekali aja pas pertama kali
npm run dev        <- jalanin development server
```

Buka alamat yang muncul (biasanya `http://localhost:5173`). Buat berhenti, `Ctrl + C`.

## Daftar Isi — Urutan Belajar

Ikuti sesi 1 sampai 10 berurutan, jangan loncat:

| Sesi | Panduan | Topik |
|---|---|---|
| 1 | [Kenapa React?](react-journey/01-kenapa-react.md) | Kenapa React, SPA vs server-rendered, setup |
| 2 | [JSX & Component](react-journey/02-jsx-dan-component.md) | Sintaks JSX, component, Tailwind setup |
| 3 | [Props & Rendering List](react-journey/03-props-dan-rendering-list.md) | Props, render list, conditional rendering |
| 4 | [State & Event](react-journey/04-state-dan-event.md) | useState, event handler, immutability (paling penting!) |
| 5 | [Form & Controlled Input](react-journey/05-form-dan-controlled-input.md) | Controlled form, validasi |
| 6 | [useEffect & Fetch](react-journey/06-useeffect-dan-fetch.md) | useEffect, fetch data dari API (json-server) |
| 7 | [Router Multi Halaman](react-journey/07-router-multi-halaman.md) | react-router-dom, multi halaman |
| 8 | [Custom Hook & Context](react-journey/08-custom-hook-dan-context.md) | Custom hook, Context API |
| 9 | [Project TaskFlow Lite](react-journey/09-project-taskflow-lite.md) | Project latihan dari nol, gabungin semua materi |
| 10 | [Laravel Connect Demo](react-journey/10-laravel-connect-demo.md) | Nyambungin React ke backend Laravel yang udah kamu kenal |

Daftar isi versi lengkap (dengan catatan tambahan): [react-journey/00-daftar-isi.md](react-journey/00-daftar-isi.md).

**Catatan:**
- Project besar/serius (Task Management pakai database beneran) itu nanti pas belajar **Golang**, bukan di sini.
- Deploy/hosting yang lengkap baru diajarin di akhir banget, setelah project Golang dan ujian selesai.

## Troubleshooting Umum

| Masalah | Penyebab & Solusi |
|---|---|
| `'vite' is not recognized` / `npm: command not found` | Node.js belum keinstall, atau belum restart terminal setelah install. |
| Port `5173` udah dipakai | Ada `npm run dev` lain yang masih jalan. `Ctrl+C` di terminal itu, atau biarin Vite otomatis pindah ke port lain. |
| Halaman putih kosong, gak ada error di layar | Buka **Console** di DevTools browser (F12) — error JS biasanya muncul di situ. |
| `npm install` lama banget / gagal | Cek koneksi internet. Kalau masih gagal, hapus `node_modules` dan `package-lock.json`, coba lagi. |
| Error `CORS` di console (mulai sesi 10) | Backend (Laravel) belum diatur buat nerima request dari alamat React. Dibahas di `react-journey/10-laravel-connect-demo.md`. |
