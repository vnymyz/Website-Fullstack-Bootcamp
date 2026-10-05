# 12 - Setup Frontend (Vite + React)

## Tujuan Pembelajaran
- Membuat project React memakai Vite.
- Memasang `react-router-dom` dan `axios`.
- Mengatur proxy ke backend.

## Langkah

### 1. Buat project
Dari folder `04-project-fullstack-task-manager`:
```powershell
npm create vite@latest frontend -- --template react
cd frontend
npm install
npm install react-router-dom axios
```

Perintah `npm create vite` bisa menampilkan pertanyaan; pilih **React** dan **JavaScript**. Jika diminta, pilih tidak menjalankan dev server dulu.

### 2. Bersihkan template
Hapus file bawaan yang tidak kita pakai:
- `src/App.css`
- `src/assets/` (folder)
- isi lama `src/index.css` (akan kita ganti)

### 3. Buat folder
```powershell
mkdir src\api, src\context, src\components, src\pages
```

### 4. Konfigurasi Vite (proxy)
Ganti isi `frontend/vite.config.js`:

```js
// File: frontend/vite.config.js
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    // Semua request ke /api diteruskan ke backend Go
    proxy: {
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
      },
    },
  },
})
```

**Penjelasan proxy**
Request dari browser ke `http://localhost:5173/api/...` akan diteruskan Vite ke `http://localhost:8080/api/...`. Keuntungan: tidak ada masalah CORS saat development dan alamat backend tidak tersebar di kode.

### 5. Ubah `index.html`
Ganti isi `frontend/index.html`:

```html
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Task Manager</title>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.jsx"></script>
  </body>
</html>
```

### 6. File env (opsional)
`frontend/.env.example`:

```env
# File: frontend/.env.example
# Kosongkan untuk memakai proxy Vite (/api -> localhost:8080).
# Isi jika backend ada di alamat lain, contoh: http://localhost:8080/api
VITE_API_URL=
```

Variabel di Vite harus berawalan `VITE_` agar terbaca di browser lewat `import.meta.env`.

### 7. Cek `package.json`
Isi dependency Anda kira-kira:

```json
{
  "name": "task-manager-frontend",
  "private": true,
  "version": "1.0.0",
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  },
  "dependencies": {
    "axios": "^1.20.0",
    "react": "^19.3.0",
    "react-dom": "^19.3.0",
    "react-router-dom": "^7.18.4"
  },
  "devDependencies": {
    "@vitejs/plugin-react": "^6.1.1",
    "vite": "^8.3.2"
  }
}
```

> Nomor versi bisa berbeda sesuai waktu instalasi. Itu normal.

### 8. Jalankan
```powershell
npm run dev
```
Buka `http://localhost:5173`. Karena `App.jsx` masih bawaan, kita ganti di langkah berikutnya.

## Kesalahan Umum
- `npm: command not found` -> Node.js belum terpasang / terminal belum di-restart.
- Port 5173 terpakai -> Vite otomatis memakai 5174. Samakan `CORS_ORIGIN` jika memakai alamat itu.

## Catatan Instruktur
Estimasi 30 menit. Jelaskan perbedaan Vite (dev server cepat, ESM) dengan CRA yang sudah ditinggalkan.
