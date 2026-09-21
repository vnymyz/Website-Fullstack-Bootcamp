# Daftar Isi — Panduan React JS

Semua sesi di bawah ini dikerjakan di **satu project yang sama**: `5-React/learn-react/`. Gak ada project baru per sesi — kamu edit/tambah file di `learn-react/src/` sambil ngikutin panduan, commit progress kalau mau nyimpen histori.

| Sesi | File Panduan | Topik |
|---|---|---|
| 1 | [01-kenapa-react.md](01-kenapa-react.md) | Kenapa React, SPA vs server-rendered, setup |
| 2 | [02-jsx-dan-component.md](02-jsx-dan-component.md) | JSX, component, composition, Tailwind |
| 3 | [03-props-dan-rendering-list.md](03-props-dan-rendering-list.md) | Props, `.map()`, `key`, conditional rendering |
| 4 | [04-state-dan-event.md](04-state-dan-event.md) | `useState`, event, immutability (paling penting!) |
| 5 | [05-form-dan-controlled-input.md](05-form-dan-controlled-input.md) | Controlled form, validasi |
| 6 | [06-useeffect-dan-fetch.md](06-useeffect-dan-fetch.md) | `useEffect`, fetch API, json-server, tambah/toggle/edit/hapus |
| 7 | [07-router-multi-halaman.md](07-router-multi-halaman.md) | react-router-dom, multi halaman, protected route |
| 8 | [08-custom-hook-dan-context.md](08-custom-hook-dan-context.md) | Custom hook, Context API, alur login |
| 9 | [09-project-taskflow-lite.md](09-project-taskflow-lite.md) | Project latihan dari nol — gabungin semua materi |
| 10 | [10-laravel-connect-demo.md](10-laravel-connect-demo.md) | Nyambungin React ke backend Laravel |

## Cara Pakai Panduan Ini

Tiap sesi punya struktur yang sama, jadi kamu selalu tau lagi ada di bagian mana:

1. **Tujuan & hasil akhir** — apa yang bakal kamu punya di akhir sesi.
2. **Sebelum mulai** — apa yang harus sudah jalan dari sesi sebelumnya (kalau belum, balik dulu).
3. **Konsep** — penjelasan singkat *kenapa* sebelum nulis kode.
4. **Langkah 1, 2, 3, ...** — kerjain berurutan. Tiap langkah dipecah jadi sub-langkah (a, b, c) dan diakhiri **"Cek"**.
5. **Cek** — checklist kecil buat mastiin langkah itu bener. **Jangan lanjut kalau "Cek"-nya belum cocok.** Kalau gak cocok, lihat tabel **Kalau Error**.
6. **Kalau Error** — daftar gejala yang paling sering muncul beserta penyebabnya.
7. **Latihan / Checkpoint / Catatan buat Kamu** — kerjain sebelum lanjut ke sesi berikutnya.

**Kebiasaan yang harus dibangun dari sesi 1:**
- Ketik sendiri kodenya, jangan copy-paste mentah-mentah. Setelah ngetik, baca lagi per baris dan tebak fungsinya.
- Selalu buka **Console** browser (`F12` → tab Console). Error React hampir selalu kebaca jelas di sana.
- Simpan file (`Ctrl+S`) sebelum ngecek browser. Vite otomatis refresh.
- Lihat terminal `npm run dev` — error sintaks (kurung kurang, import salah) muncul di sana juga.

**Catatan penting:**
- Project besar/serius (Task Management pakai database beneran) itu nanti pas belajar **Golang**, bukan di sini.
- Deploy/hosting lengkap juga baru diajarin nanti di akhir banget (setelah project Golang + ujian). Sesi 1-10 fokus ke fundamental React aja.
- Tiap sesi ada bagian **Catatan buat kamu** — kerjain sebelum lanjut ke sesi berikutnya.
- Sesi 3, 4, 6 ada **bug hunt** — kode dikasih rusak sengaja, tugas kamu nemuin & benerin sendiri sebelum liat jawaban.
- Sesi 10 butuh **Laragon + Composer + PHP** karena backend-nya Laravel. Sesi 1-9 cukup Node.js.

## Peta Alur Belajar

```
Sesi 1-2   : bikin project + tampilan statis (JSX, component, Tailwind)
Sesi 3-4   : data mengalir (props) + data berubah (state)
Sesi 5     : input dari user (form)
Sesi 6     : data dari luar (fetch ke API palsu)
Sesi 7     : banyak halaman (router)
Sesi 8     : berbagi data antar halaman (context) + reuse logic (hook)
Sesi 9     : gabungin semuanya jadi satu app
Sesi 10    : ganti API palsu dengan backend beneran (Laravel)
```

## Perintah Terminal yang Sering Dipakai

Semua dijalanin dari dalam folder `learn-react/` (di Windows: PowerShell atau terminal bawaan VS Code).

| Perintah | Gunanya |
|---|---|
| `npm run dev` | Jalanin React di `http://localhost:5173` (mulai sesi 1) |
| `npm run api` | Jalanin json-server di `http://localhost:3001` (mulai sesi 6) — di terminal **terpisah** |
| `npm install <nama>` | Pasang library baru |
| `Ctrl+C` di terminal | Matiin server yang lagi jalan |
| `npm run lint` | Cek kode pakai ESLint |
