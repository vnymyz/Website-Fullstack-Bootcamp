# 01 - Arsitektur dan Perencanaan

## Tujuan Pembelajaran
- Memahami gambaran besar aplikasi fullstack (frontend, backend, database).
- Mengenal fitur, endpoint API, dan struktur folder project.
- Memahami alur data dari browser sampai database dan kembali lagi.

## Yang Akan Kita Bangun
**Task Manager** - aplikasi daftar tugas dengan login. Setiap pengguna hanya bisa melihat dan mengelola tugasnya sendiri.

### Fitur
1. Daftar akun (register) dan masuk (login) dengan JWT
2. Tambah, lihat, ubah, hapus task (CRUD)
3. Filter task berdasarkan status (`todo`, `in_progress`, `done`)
4. Pagination daftar task
5. Halaman dilindungi (hanya bisa dibuka jika sudah login)

## Arsitektur

```
 +-------------------+      HTTP + JSON       +--------------------+     SQL      +-----------+
 |  React (Vite)     |  ------------------>   |  Golang (Gin)      |  --------->  |  MySQL    |
 |  localhost:5173   |  <------------------   |  localhost:8080    |  <---------  |  :3306    |
 +-------------------+   Authorization:       +--------------------+              +-----------+
                         Bearer <JWT>
```

Peran masing-masing:

| Lapisan | Tugas |
|---|---|
| React | Menampilkan UI, menerima input, memanggil API |
| Go (Gin) | Validasi, logika bisnis, autentikasi, bicara ke database |
| MySQL | Menyimpan data permanen |

### Alur Login
1. User mengisi email & password di React.
2. React mengirim `POST /api/auth/login`.
3. Go mencari user di MySQL, membandingkan password dengan hash bcrypt.
4. Jika cocok, Go membuat **token JWT** dan mengirimnya.
5. React menyimpan token di `localStorage`.
6. Setiap request berikutnya membawa header `Authorization: Bearer <token>`.
7. Middleware Go memverifikasi token sebelum handler dijalankan.

## Daftar Endpoint

| Method | URL | Auth | Fungsi |
|---|---|---|---|
| GET | `/api/health` | tidak | Cek server hidup |
| POST | `/api/auth/register` | tidak | Daftar akun |
| POST | `/api/auth/login` | tidak | Login |
| GET | `/api/auth/me` | ya | Data user yang sedang login |
| GET | `/api/tasks?status=&page=&limit=` | ya | Daftar task |
| POST | `/api/tasks` | ya | Buat task |
| GET | `/api/tasks/:id` | ya | Detail task |
| PUT | `/api/tasks/:id` | ya | Ubah task |
| DELETE | `/api/tasks/:id` | ya | Hapus task |

### Format Response Standar
```json
{ "success": true, "message": "berhasil", "data": { } }
```
```json
{ "success": false, "message": "email atau password salah" }
```

## Struktur Folder Final

```
04-project-fullstack-task-manager/
├── database/
│   └── schema.sql
├── backend/
│   ├── go.mod
│   ├── .env.example
│   ├── .env                      (dibuat sendiri, jangan di-commit)
│   ├── .gitignore
│   ├── .air.toml                 (opsional, hot reload)
│   ├── cmd/api/main.go           (pintu masuk program)
│   └── internal/
│       ├── config/config.go
│       ├── database/mysql.go
│       ├── models/user.go, task.go
│       ├── repository/user_repository.go, task_repository.go
│       ├── handlers/auth_handler.go, task_handler.go
│       ├── middleware/auth.go
│       ├── utils/jwt.go, password.go, response.go
│       └── routes/routes.go
└── frontend/
    ├── package.json, vite.config.js, index.html
    └── src/
        ├── main.jsx, App.jsx, index.css
        ├── api/axios.js, auth.js, tasks.js
        ├── context/AuthContext.jsx
        ├── components/Navbar.jsx, ProtectedRoute.jsx, TaskForm.jsx, TaskItem.jsx, TaskFilter.jsx
        └── pages/Login.jsx, Register.jsx, Dashboard.jsx, NotFound.jsx
```

### Mengapa folder `internal/` dan `cmd/`?
- `cmd/api/main.go`: tempat program dimulai. Sengaja tipis, hanya merangkai komponen.
- `internal/`: kode yang hanya boleh dipakai oleh module ini (aturan Go, tidak bisa di-import project lain).
- Pemisahan **handler -> repository** membuat kode mudah dites dan diubah:
  - **handler**: urusan HTTP (baca request, kirim response)
  - **repository**: urusan database (query SQL)

## Persiapan
Pastikan sudah terpasang:
```powershell
go version
node -v
npm -v
```
Dan XAMPP / Laragon sudah ada (dijelaskan di materi 3, file `01-setup-xampp-laragon.md`).

## Catatan Instruktur
- Estimasi: 30-45 menit. Gambar diagram di papan tulis, minta siswa menjelaskan ulang alur login.
- Poin diskusi: mengapa password tidak boleh disimpan polos? Mengapa kita perlu token?
