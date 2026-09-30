# 01 — Setup Project & API Key TMDB

**Hasil akhir:** project `react-movie` jalan di `http://localhost:5173`, Tailwind aktif, dan API key TMDB tersimpan aman di `.env`.

**Sebelum mulai:** Node.js terpasang. Cek dengan `node -v`.

---

## Langkah 1 — Daftar akun TMDB

1. Buka https://www.themoviedb.org/signup lalu daftar (gratis).
2. Buka email, klik link verifikasi. Tanpa verifikasi, kamu gak bisa minta API key.
3. Login.

## Langkah 2 — Minta API key

1. Klik foto profil (pojok kanan atas) → **Settings** → menu kiri **API**.
2. Klik **Create** → pilih **Developer** → centang persetujuan.
3. Isi formulir:

| Kolom | Isi contoh |
|---|---|
| Type of Use | Education / Personal |
| Application Name | React Movie |
| Application URL | `http://localhost:5173` |
| Application Summary | Latihan belajar React, menampilkan daftar film |

4. Setelah disetujui, kamu lihat dua kunci. Ambil yang bernama **API Key** (v3 auth), berupa teks acak 32 karakter. Yang **API Read Access Token** (panjang banget) **tidak dipakai** di project ini.

> Tampilan situs TMDB kadang berubah. Kalau menunya beda, cari tulisan "API" di halaman Settings.

**Cek:** kamu punya teks API Key 32 karakter. Simpan sementara di Notepad, kita pindahin ke `.env` di Langkah 7.

## Langkah 3 — Bikin project Vite

Buka terminal di folder tempat kamu simpan project, lalu:

```bash
npm create vite@latest react-movie -- --template react
```

Kalau ditanya pilihan tambahan (misal rolldown / "install and start now"), pilih **No** untuk jalan otomatis. Lalu:

```bash
cd react-movie
npm install
```

## Langkah 4 — Pasang library

```bash
npm install react-router-dom lucide-react
npm install tailwindcss @tailwindcss/vite
```

| Library | Fungsi |
|---|---|
| `react-router-dom` | Pindah halaman (Beranda / Detail / Favorit) |
| `lucide-react` | Ikon SVG (Search, Heart, Star, dll) |
| `tailwindcss` + `@tailwindcss/vite` | Styling pakai class |

## Langkah 5 — Aktifkan Tailwind

**File: `vite.config.js`** (ganti seluruh isinya)

```js
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],
});
```

**File: `src/index.css`** (ganti seluruh isinya)

```css
@import "tailwindcss";

@theme {
  --color-bg: #0b0d12;
  --color-surface: #151922;
  --color-border: #262c3a;
  --color-accent: #e50914;
  --font-sans: "Inter", system-ui, sans-serif;
}

body {
  background-color: var(--color-bg);
  color: #ffffff;
  font-family: var(--font-sans);
  -webkit-font-smoothing: antialiased;
}

/* Hormati user yang mematikan animasi di sistem operasinya */
@media (prefers-reduced-motion: reduce) {
  * {
    animation: none !important;
    transition: none !important;
    scroll-behavior: auto !important;
  }
}
```

Bagian `@theme` bikin nama warna sendiri. Setelah ini kamu bisa nulis `bg-bg`, `bg-surface`, `border-border`, `text-accent` di class Tailwind. Kalau mau ganti tema, cukup ubah 4 baris hex di sini.

## Langkah 6 — Rapikan `index.html` dan hapus file bawaan

**File: `index.html`** (ganti seluruh isinya)

```html
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Cari, filter, dan simpan film favoritmu. Data dari TMDB." />
    <meta name="theme-color" content="#0b0d12" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <title>React Movie</title>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.jsx"></script>
  </body>
</html>
```

Hapus file bawaan template yang gak dipakai:

- `src/App.css`
- folder `src/assets/`

Lalu buat folder kosong yang bakal dipakai (biar rapi dari awal): `src/api`, `src/hooks`, `src/context`, `src/utils`, `src/components`, `src/pages`.

## Langkah 7 — Simpan API key di `.env`

Di **root project** (sejajar `package.json`, BUKAN di dalam `src/`), buat dua file:

**File: `.env`**

```
VITE_TMDB_API_KEY=tempel_api_key_kamu_di_sini
```

**File: `.env.example`**

```
VITE_TMDB_API_KEY=isi_api_key_kamu
```

Aturan penulisan `.env`:

| Aturan | Salah | Benar |
|---|---|---|
| Nama harus diawali `VITE_` | `TMDB_API_KEY=abc` | `VITE_TMDB_API_KEY=abc` |
| Tanpa spasi di sekitar `=` | `VITE_TMDB_API_KEY = abc` | `VITE_TMDB_API_KEY=abc` |
| Tanpa tanda kutip | `VITE_TMDB_API_KEY="abc"` | `VITE_TMDB_API_KEY=abc` |

Kenapa harus `VITE_`? Vite sengaja **menyembunyikan** semua variabel yang gak diawali `VITE_` dari kode browser, sebagai pengaman supaya variabel rahasia server gak bocor tanpa sengaja.

`.env.example` itu contoh buat orang lain (atau kamu di komputer lain) supaya tau variabel apa yang harus diisi. Isinya **bukan** key asli.

## Langkah 8 — Pastikan `.env` tidak ikut ke Git

Buka `.gitignore` di root project, cari apakah ada baris `.env`. Kalau belum, tambahkan di bagian bawah:

```
# API key rahasia
.env
```

Cek (kalau project sudah `git init`):

```bash
git check-ignore .env
```

Kalau outputnya `.env`, berarti aman. Kalau kosong, berarti belum di-ignore, perbaiki dulu.

> Kalau key sudah terlanjur ter-commit: hapus key itu di halaman API TMDB dan generate yang baru. Menghapus file di commit berikutnya **tidak cukup**, karena key lama masih ada di riwayat Git.

## Langkah 9 — Test: Tailwind & `.env` jalan

Ganti sementara isi dua file ini supaya kita bisa ngetes.

**File: `src/main.jsx`**

```jsx
import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import App from "./App.jsx";
import "./index.css";

createRoot(document.getElementById("root")).render(
  <StrictMode>
    <App />
  </StrictMode>,
);
```

**File: `src/App.jsx`**

```jsx
export default function App() {
  const keyTerisi = Boolean(import.meta.env.VITE_TMDB_API_KEY);

  return (
    <div className="p-8">
      <h1 className="text-3xl font-bold text-accent">React Movie</h1>
      <p className="mt-2 text-gray-300">
        API key terbaca: {keyTerisi ? "YA ✅" : "TIDAK ❌ (cek file .env)"}
      </p>
    </div>
  );
}
```

Jalankan:

```bash
npm run dev
```

Buka `http://localhost:5173`.

Perhatikan: kita cuma menampilkan **ya/tidak**, bukan isi key-nya. Jangan pernah nampilin key di layar.

## Cek

- [ ] Halaman punya latar gelap (`#0b0d12`), tulisan "React Movie" berwarna merah.
- [ ] Tertulis `API key terbaca: YA ✅`.
- [ ] `git check-ignore .env` mengeluarkan `.env` (kalau pakai Git).
- [ ] Console browser (`F12`) tidak ada error merah.

## Kalau Error

| Gejala | Penyebab | Solusi |
|---|---|---|
| `API key terbaca: TIDAK ❌` | Nama variabel salah / `.env` salah folder | Nama harus persis `VITE_TMDB_API_KEY`, file di root (sejajar `package.json`) |
| Sudah benar tapi tetap TIDAK | Server dev belum di-restart setelah bikin/ubah `.env` | `Ctrl+C` lalu `npm run dev` lagi. Vite baca `.env` hanya saat start |
| Halaman putih polos, warna tidak muncul | Tailwind belum aktif | Cek `tailwindcss()` ada di `vite.config.js` dan `@import "tailwindcss";` ada di `index.css` |
| `Cannot find module '@tailwindcss/vite'` | Belum di-install | `npm install tailwindcss @tailwindcss/vite` |
| `bg-bg` / `text-accent` tidak ada efeknya | `@theme` salah tulis | Pastikan nama diawali `--color-` persis seperti di atas |
| `.env` muncul di `git status` | Belum masuk `.gitignore` | Tambah baris `.env`, lalu `git rm --cached .env` kalau sempat ter-track |
| Font tidak berubah jadi Inter | Internet mati / link font salah | Font cuma pemanis, aman dilewati. Pastikan tag `<link>` di `index.html` persis |

Lanjut ke [02-api-layer-tmdb.md](02-api-layer-tmdb.md).
