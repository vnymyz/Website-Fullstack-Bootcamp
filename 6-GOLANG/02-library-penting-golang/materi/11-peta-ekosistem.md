# 11 - Peta Ekosistem Library Go

Panduan memilih library. **Tidak perlu menguasai semuanya**; ketahui keberadaannya dan kapan dipakai. Aturan umum: pilih library yang **aktif dirawat**, banyak dipakai, dan punya dokumentasi jelas. Periksa di https://pkg.go.dev (tanggal rilis terakhir, jumlah *imported by*).

## Web Framework / Router

| Library | Ciri | Kapan dipilih |
|---|---|---|
| `net/http` (standar) | Tanpa dependency, routing method+path sejak Go 1.22 | API kecil, ingin minim dependency |
| **Gin** | Paling populer, banyak tutorial | **Pilihan kita**, mudah dipelajari |
| Echo | Mirip Gin, API rapi | Preferensi tim |
| Fiber | Gaya Express.js, berbasis `fasthttp` (bukan `net/http`) | Tim dari Node.js; perhatikan ketidakcocokan dengan middleware `net/http` |
| chi | Router ringan, 100% kompatibel `net/http` | Ingin tetap dekat dengan standar |

## Database

| Library | Ciri | Kapan dipilih |
|---|---|---|
| `database/sql` + driver | Standar, kontrol penuh | **Dasar, wajib dipahami** |
| `go-sql-driver/mysql` | Driver MySQL/MariaDB | MySQL |
| `jackc/pgx` | Driver PostgreSQL modern | PostgreSQL |
| `sqlx` | Ekstensi `database/sql`: scan ke struct otomatis | Ingin kurangi boilerplate, tetap SQL mentah |
| `sqlc` | Menghasilkan kode Go dari file `.sql` (type-safe) | Query banyak dan ingin aman-tipe |
| GORM | ORM lengkap | CRUD cepat, prototipe |
| `ent` | ORM berbasis skema/kode, type-safe | Model data kompleks |
| `golang-migrate` | Migrasi skema bernomor | Produksi (versi skema) |
| `go-redis` | Client Redis | Cache, session, rate limit |

## Konfigurasi

| Library | Kapan |
|---|---|
| `godotenv` | Baca `.env` (**kita pakai**) |
| `viper` | Banyak sumber konfigurasi (file YAML/JSON, env, flag) |
| `envconfig` / `caarlos0/env` | Isi struct dari env lewat tag |

## Validasi dan Utilitas

| Library | Fungsi |
|---|---|
| `go-playground/validator` | Validasi struct (ikut di Gin) |
| `google/uuid` | UUID |
| `shopspring/decimal` | Angka desimal presisi (uang) |
| `samber/lo` | Helper gaya Lodash (Map, Filter) dengan generics |

## Keamanan

| Library | Fungsi |
|---|---|
| `x/crypto/bcrypt` | Hash password (**kita pakai**) |
| `x/crypto/argon2` | Alternatif hash password modern |
| `golang-jwt/jwt/v5` | JWT (**kita pakai**) |
| `x/oauth2` | OAuth2 / login Google, GitHub |
| `ulule/limiter`, `x/time/rate` | Rate limiting |

## Logging

| Library | Kapan |
|---|---|
| `log/slog` (standar) | Logging terstruktur, **pilihan default** sejak Go 1.21 |
| `zerolog`, `zap` | Performa sangat tinggi |

## Testing

| Library | Fungsi |
|---|---|
| `testing` (standar) | Dasar |
| `testify` | Assertion, mock, suite |
| `go-sqlmock` | Mock `database/sql` tanpa database sungguhan |
| `testcontainers-go` | Database asli di Docker saat test |
| `gomock` / `mockery` | Membuat mock dari interface |

## API dan Komunikasi

| Library | Fungsi |
|---|---|
| `swaggo/swag` | Dokumentasi Swagger/OpenAPI |
| `gorilla/websocket` | WebSocket |
| `grpc-go` | gRPC |
| `resty` | HTTP client yang nyaman |
| `robfig/cron` | Penjadwalan tugas |

## CLI dan Tools
| Library | Fungsi |
|---|---|
| `cobra` | Membuat aplikasi command-line |
| `air` | Hot reload |
| `golangci-lint` | Linter |
| `delve (dlv)` | Debugger |

## Panduan Memilih Library
1. Bisa dikerjakan dengan standard library? Pakai itu.
2. Cek aktivitas repo (commit terakhir, issue terbuka, jumlah bintang).
3. Baca lisensi.
4. Setiap dependency = tanggung jawab (update keamanan, kompatibilitas). **Sedikit dependency lebih baik.**
5. Jalankan `go mod tidy` dan `govulncheck ./...` untuk memeriksa kerentanan.

## Latihan
Pilih tiga library dari tabel, buka halaman `pkg.go.dev`-nya, dan catat: versi terbaru, tanggal rilis, dan satu contoh penggunaan. Presentasikan singkat.

## Catatan Instruktur
Estimasi 30-45 menit. Format diskusi, bukan ceramah.
