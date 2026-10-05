# Materi 3 - Menghubungkan Golang ke MySQL

Belajar memakai `database/sql` + driver `go-sql-driver/mysql` untuk CRUD, transaksi, dan pola repository. Ditutup dengan perbandingan ke GORM.

## Tujuan Belajar
Setelah materi ini siswa mampu:
1. Menyalakan MySQL (XAMPP/Laragon) dan mengelola data lewat phpMyAdmin.
2. Menghubungkan program Go ke MySQL dan mengatur connection pool.
3. Melakukan INSERT, SELECT, UPDATE, DELETE dengan aman (query berparameter).
4. Menangani NULL, transaksi, dan timeout dengan `context`.
5. Menyusun kode database dengan repository pattern.
6. Membandingkan `database/sql` dengan ORM (GORM).

## Prasyarat
Materi 1 dan 2 (terutama struct, interface, error handling, `context`, `godotenv`).

## Struktur Folder
```
03-golang-mysql/
├── README.md
├── go.mod / go.sum
├── .env.example               <- salin menjadi .env
├── database/
│   ├── schema.sql             <- tabel products & accounts
│   ├── seed.sql               <- data contoh
│   └── latihan.sql            <- tabel orders untuk latihan
├── internal/koneksi/koneksi.go    <- helper koneksi dipakai semua contoh
├── materi/                    <- 12 file panduan
├── contoh-kode/               <- 02-koneksi ... 12-gorm (masing-masing punya main.go)
└── latihan/
    ├── soal.md
    └── solusi/
```

## Cara Mulai
```powershell
cd 03-golang-mysql
copy .env.example .env
go mod tidy

# nyalakan MySQL, jalankan database/schema.sql + seed.sql di phpMyAdmin

go run ./contoh-kode/02-koneksi
```

## Daftar Materi

| No | File | Topik | Estimasi |
|---|---|---|---|
| 01 | `materi/01-setup-xampp-laragon.md` | Install MySQL, phpMyAdmin, buat DB | 45 mnt |
| 02 | `materi/02-driver-dan-koneksi.md` | Driver, DSN, `sql.Open`, `Ping` | 45 mnt |
| 03 | `materi/03-connection-pool.md` | Pool koneksi, `db.Stats()` | 30 mnt |
| 04 | `materi/04-create-insert.md` | `ExecContext`, `LastInsertId` | 30 mnt |
| 05 | `materi/05-read-query.md` | `QueryContext`, `Scan`, `ErrNoRows` | 60 mnt |
| 06 | `materi/06-update-delete.md` | UPDATE, DELETE, `RowsAffected` | 30 mnt |
| 07 | `materi/07-sql-injection-prepared-statement.md` | Keamanan query | 45 mnt |
| 08 | `materi/08-null-handling.md` | `NullString`, pointer, `COALESCE` | 30 mnt |
| 09 | `materi/09-transaction.md` | `BeginTx`, `Commit`, `Rollback` | 60 mnt |
| 10 | `materi/10-context-timeout.md` | Timeout dan pembatalan query | 30 mnt |
| 11 | `materi/11-repository-pattern.md` | Interface + implementasi MySQL | 60 mnt |
| 12 | `materi/12-gorm-pembanding.md` | CRUD dengan GORM | 60 mnt |

Total sekitar 9 jam (2-3 pertemuan).

## Status Verifikasi
Seluruh kode di `contoh-kode/` dan `latihan/solusi/` sudah lolos `gofmt`, `go vet`, dan compile. Eksekusi nyata membutuhkan MySQL menyala; instruktur disarankan menjalankan setiap contoh sekali sebelum kelas untuk mencocokkan output dengan versi MySQL/MariaDB yang dipakai.

## Catatan XAMPP (MariaDB)
XAMPP menyertakan **MariaDB**, yang kompatibel dengan driver dan semua contoh di sini. Pesan `VERSION()` akan berupa `10.x.x-MariaDB`.
