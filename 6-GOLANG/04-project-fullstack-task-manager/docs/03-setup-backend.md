# 03 - Setup Backend

## Tujuan Pembelajaran
- Membuat Go module baru dan memasang dependency.
- Membuat kerangka folder backend.

## Langkah

### 1. Buat folder dan module
```powershell
cd 04-project-fullstack-task-manager
mkdir backend
cd backend
go mod init taskmanager
```
`taskmanager` adalah nama module. Nama ini dipakai di `import "taskmanager/internal/..."`.

### 2. Pasang dependency
```powershell
go get github.com/gin-gonic/gin
go get github.com/gin-contrib/cors
go get github.com/go-sql-driver/mysql
go get github.com/joho/godotenv
go get github.com/golang-jwt/jwt/v5
go get golang.org/x/crypto
```

| Library | Fungsi |
|---|---|
| gin | Web framework / router |
| gin-contrib/cors | Izin akses lintas origin (frontend -> backend) |
| go-sql-driver/mysql | Driver MySQL untuk `database/sql` |
| godotenv | Membaca file `.env` |
| golang-jwt/jwt/v5 | Membuat & memverifikasi token JWT |
| x/crypto (bcrypt) | Hash password |

### 3. Buat kerangka folder
```powershell
mkdir cmd\api
mkdir internal\config, internal\database, internal\models, internal\repository, internal\handlers, internal\middleware, internal\utils, internal\routes
```

### 4. Buat file `.env.example`
```env
# File: backend/.env.example
# Salin file ini menjadi .env lalu sesuaikan nilainya
APP_PORT=8080
APP_ENV=development

DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=root
DB_PASS=
DB_NAME=task_manager

# Ganti dengan string acak yang panjang (minimal 32 karakter)
JWT_SECRET=ganti-dengan-secret-yang-panjang-dan-acak-123456
JWT_EXPIRE_HOURS=24

# Alamat frontend yang boleh mengakses API (CORS)
CORS_ORIGIN=http://localhost:5173
```

Salin menjadi `.env`:
```powershell
copy .env.example .env
```
Sesuaikan `DB_PASS` jika MySQL Anda memakai password.

> **Penting:** `.env` berisi rahasia. Jangan pernah di-commit ke Git.

### 5. Buat file `.gitignore`
```
# File: backend/.gitignore
.env
tmp/
*.exe
```

## Cek Hasil
Struktur sekarang:
```
backend/
├── cmd/api/
├── internal/{config,database,models,repository,handlers,middleware,utils,routes}/
├── go.mod
├── go.sum
├── .env
├── .env.example
└── .gitignore
```

## Catatan Instruktur
Estimasi 20 menit. Jika `go get` lambat, cek koneksi atau set `GOPROXY=https://proxy.golang.org,direct`.
