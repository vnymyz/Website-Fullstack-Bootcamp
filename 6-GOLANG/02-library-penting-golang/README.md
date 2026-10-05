# Materi 2 - Library Penting Golang

Mengenal library yang hampir selalu dipakai saat membuat backend Go: standard library, web framework (Gin), konfigurasi, validasi, keamanan (bcrypt, JWT), CORS, testing, dan tools.

## Tujuan Belajar
Setelah materi ini siswa mampu:
1. Memakai paket standard library yang paling sering digunakan.
2. Membuat REST API dengan `net/http` dan Gin.
3. Mengelola konfigurasi dengan `.env`.
4. Memvalidasi input, meng-hash password, dan membuat/memverifikasi JWT.
5. Mengatur CORS agar frontend dapat memanggil backend.
6. Menulis test untuk handler HTTP.
7. Memilih library yang tepat dengan bantuan peta ekosistem.

## Prasyarat
Materi 1 (terutama struct, interface, error, goroutine/mutex, package).

## Struktur Folder
```
02-library-penting-golang/
├── README.md
├── go.mod / go.sum
├── materi/              <- 11 file panduan
├── contoh-kode/
│   ├── 01-stdlib/       main.go
│   ├── 02-nethttp/      main.go
│   ├── 03-gin/          main.go
│   ├── 04-godotenv/     main.go + .env (+ .env.example)
│   ├── 05-validator/    main.go
│   ├── 06-bcrypt/       main.go
│   ├── 07-jwt/          main.go
│   ├── 08-cors/         main.go
│   └── 09-testify/      handler.go + handler_test.go
└── latihan/
    ├── soal.md
    └── solusi/
```

## Cara Menjalankan
```powershell
cd 02-library-penting-golang
go mod tidy
go run ./contoh-kode/01-stdlib
go run ./contoh-kode/03-gin        # server di :8080
go test ./contoh-kode/09-testify -v
```
> Contoh `02`, `03`, `05`, `08`, dan solusi `02-todo-api` semuanya memakai port **8080**. Jalankan satu per satu (hentikan dengan `Ctrl+C`).

## Daftar Materi

| No | File | Topik | Estimasi |
|---|---|---|---|
| 01 | `materi/01-standard-library.md` | fmt, strings, strconv, time, json, slog, context | 90 mnt |
| 02 | `materi/02-net-http-dasar.md` | Server HTTP dengan `net/http` | 60 mnt |
| 03 | `materi/03-gin-framework.md` | Route, group, middleware, binding | 90 mnt |
| 04 | `materi/04-godotenv-konfigurasi.md` | `.env` dan Config | 30 mnt |
| 05 | `materi/05-validator.md` | Validasi input | 45 mnt |
| 06 | `materi/06-bcrypt-password.md` | Hash password | 30 mnt |
| 07 | `materi/07-jwt.md` | Token JWT | 60 mnt |
| 08 | `materi/08-cors.md` | CORS | 30 mnt |
| 09 | `materi/09-testify.md` | Test handler | 60 mnt |
| 10 | `materi/10-tools-developer.md` | go tools, air, lint | 45 mnt |
| 11 | `materi/11-peta-ekosistem.md` | Rekomendasi library | 30 mnt |

Total sekitar 9-10 jam (2-3 pertemuan).

## Status Verifikasi
Semua kode lolos `gofmt` dan `go vet`. Test di `09-testify` dan `latihan/solusi/02-todo-api` lulus (`go test ./...`). Contoh `01-stdlib`, `04-godotenv`, `06-bcrypt`, `07-jwt`, serta `05-validator` sudah dijalankan dan outputnya dicocokkan dengan yang tertulis di materi.
