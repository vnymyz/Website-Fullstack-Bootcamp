# Materi 4 - Project Fullstack: Task Manager (React + Golang + MySQL)

Aplikasi daftar tugas lengkap dengan registrasi/login (JWT), CRUD task, filter, dan pagination.

| Lapisan | Teknologi |
|---|---|
| Frontend | React 19 + Vite, React Router, Axios |
| Backend | Go + Gin, `database/sql`, JWT, bcrypt |
| Database | MySQL (XAMPP / Laragon) |

## Dua Cara Memakai Folder Ini

### Cara 1 - Belajar step by step (disarankan untuk siswa)
Buat project dari nol (folder kosong), ikuti `docs/` berurutan. Setiap file `.md` menyebut **file apa yang harus dibuat, di folder mana, dan isi lengkapnya**.

### Cara 2 - Menjalankan hasil jadi (untuk instruktur / pembanding)
Folder `backend/` dan `frontend/` sudah berisi kode final yang sudah diuji compile (`go build`, `npm run build`).

```powershell
# 1. Nyalakan MySQL (XAMPP/Laragon), jalankan database/schema.sql di phpMyAdmin

# 2. Backend
cd backend
copy .env.example .env      # sesuaikan DB_PASS jika perlu
go mod tidy
go run ./cmd/api

# 3. Frontend (terminal baru)
cd frontend
npm install
npm run dev
```
Buka http://localhost:5173

## Daftar Panduan (`docs/`)

| No | File | Isi | Estimasi |
|---|---|---|---|
| 01 | `01-arsitektur-dan-perencanaan.md` | Arsitektur, endpoint, struktur folder | 45 mnt |
| 02 | `02-desain-database.md` | ERD dan `schema.sql` | 30 mnt |
| 03 | `03-setup-backend.md` | `go mod init`, dependency, `.env` | 20 mnt |
| 04 | `04-config-dan-koneksi-db.md` | `config.go`, `mysql.go` | 45 mnt |
| 05 | `05-models-dan-repository.md` | Model dan query SQL | 60 mnt |
| 06 | `06-response-helper-dan-error.md` | Response, bcrypt, JWT | 45 mnt |
| 07 | `07-auth-register-login-jwt.md` | Handler auth | 60 mnt |
| 08 | `08-middleware-auth-dan-cors.md` | Middleware JWT, CORS | 45 mnt |
| 09 | `09-crud-task.md` | Handler CRUD + pagination | 75 mnt |
| 10 | `10-routing-dan-main.md` | Routes, `main.go`, run server | 45 mnt |
| 11 | `11-testing-api-postman-curl.md` | Uji API | 45 mnt |
| 12 | `12-setup-frontend-vite-react.md` | Setup Vite + React | 30 mnt |
| 13 | `13-axios-dan-auth-context.md` | Axios, Context, ProtectedRoute | 60 mnt |
| 14 | `14-halaman-login-register.md` | Halaman login/register + CSS | 60 mnt |
| 15 | `15-dashboard-dan-crud-task-ui.md` | Dashboard dan CRUD UI | 90 mnt |
| 16 | `16-integrasi-dan-menjalankan-aplikasi.md` | Uji end-to-end, build | 45 mnt |
| 17 | `17-troubleshooting.md` | Daftar masalah umum | referensi |
| 18 | `18-pengembangan-lanjutan.md` | Tugas akhir + bonus | - |

## Struktur Folder

```
04-project-fullstack-task-manager/
├── README.md
├── docs/                       <- panduan step-by-step (18 file)
├── database/schema.sql
├── backend/                    <- Go (Gin)
│   ├── cmd/api/main.go
│   └── internal/{config,database,models,repository,handlers,middleware,utils,routes}
└── frontend/                   <- React (Vite)
    └── src/{api,context,components,pages}
```

## Prasyarat
Selesaikan materi 1-3 terlebih dahulu (terutama `03-golang-mysql/materi/01-setup-xampp-laragon.md`).

## Contoh Request dan Response

**Register**
```
POST /api/auth/register
{ "name": "Budi", "email": "budi@example.com", "password": "rahasia123" }
```
```json
{
  "success": true,
  "message": "registrasi berhasil",
  "data": {
    "token": "eyJhbGciOi...",
    "user": { "id": 1, "name": "Budi", "email": "budi@example.com", "created_at": "2026-10-05T10:00:00+07:00" }
  }
}
```

**Daftar task**
```
GET /api/tasks?status=todo&page=1&limit=5
Authorization: Bearer eyJhbGciOi...
```
```json
{
  "success": true,
  "message": "berhasil",
  "data": {
    "items": [
      { "id": 1, "user_id": 1, "title": "Belajar Gin", "description": "", "status": "todo",
        "due_date": null, "created_at": "...", "updated_at": "..." }
    ],
    "page": 1, "limit": 5, "total": 1
  }
}
```

## Catatan Instruktur
- Total estimasi 4-5 pertemuan (2-3 jam).
- Kode di `docs/` diambil langsung dari file di `backend/` dan `frontend/`, jadi keduanya konsisten.
- Pengujian yang sudah dilakukan saat materi dibuat: `go vet`, `go build`, dan `npm run build`. Uji alur penuh dengan MySQL (skenario di `docs/16`) perlu dijalankan sekali oleh instruktur di mesin yang memiliki MySQL.
