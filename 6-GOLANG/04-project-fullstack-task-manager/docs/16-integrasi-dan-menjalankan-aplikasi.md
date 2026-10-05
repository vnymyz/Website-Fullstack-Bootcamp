# 16 - Integrasi dan Menjalankan Aplikasi

## Tujuan Pembelajaran
- Menjalankan ketiga bagian (MySQL, backend, frontend) secara bersamaan.
- Melakukan uji alur dari awal sampai akhir.
- Membuat build produksi frontend.

## Checklist Sebelum Menjalankan
- [ ] MySQL menyala (XAMPP/Laragon)
- [ ] Database `task_manager` dan tabelnya sudah dibuat (`database/schema.sql`)
- [ ] `backend/.env` sudah ada dan benar
- [ ] `npm install` sudah dijalankan di `frontend`

## Jalankan (tiga terminal)

**Terminal 1 - MySQL**: nyalakan lewat XAMPP/Laragon.

**Terminal 2 - Backend**
```powershell
cd 04-project-fullstack-task-manager\backend
go run ./cmd/api
```

**Terminal 3 - Frontend**
```powershell
cd 04-project-fullstack-task-manager\frontend
npm run dev
```

Buka **http://localhost:5173**.

## Skenario Uji End-to-End
1. Buka `/register`, buat akun -> otomatis masuk dashboard.
2. Tambah 7 task dengan status berbeda.
3. Cek pagination (5 task per halaman).
4. Klik filter **Selesai**, **Sedang Dikerjakan**, **Semua**.
5. Ubah satu task, lalu hapus satu task.
6. Klik **Keluar**, lalu coba buka `/dashboard` langsung -> harus diarahkan ke `/login`.
7. Login lagi -> task Anda masih ada.
8. Buka phpMyAdmin -> tabel `users`: kolom `password_hash` harus berupa teks acak berawalan `$2a$`.
9. Daftar akun kedua, pastikan task akun pertama tidak terlihat.
10. Refresh halaman saat login -> sesi tetap ada.

## Build Produksi Frontend
```powershell
cd frontend
npm run build
```
Hasilnya di `frontend/dist/`. Folder ini berisi file statis yang bisa disajikan oleh Nginx, Netlify, Vercel, dan sejenisnya. Di produksi, isi `VITE_API_URL` dengan alamat backend (mis. `https://api.contoh.com/api`) dan set `CORS_ORIGIN` di backend sesuai domain frontend.

## Build Binary Backend
```powershell
cd backend
go build -o taskmanager.exe ./cmd/api
```
Satu file `.exe` yang bisa dijalankan tanpa menginstal Go. Salin juga environment variable (atau file `.env`) di server.

## Catatan Instruktur
Estimasi 45 menit. Ajak siswa menebak bagian mana yang bermasalah ketika sesuatu gagal (frontend, backend, atau database) memakai DevTools Network dan log terminal.
