# 02 - Driver dan Koneksi ke MySQL

## Tujuan Pembelajaran
- Memahami peran `database/sql` dan driver.
- Menyusun DSN (connection string).
- Membuka koneksi dan memverifikasinya dengan `Ping`.

## Konsep
Go memiliki paket standar **`database/sql`** yang menyediakan API umum untuk database apa pun. Untuk berbicara dengan MySQL, kita perlu **driver**:

```
Kode kita --> database/sql (antarmuka umum) --> go-sql-driver/mysql (driver) --> MySQL
```

Driver didaftarkan lewat *blank import*:
```go
import _ "github.com/go-sql-driver/mysql"
```
Tanda `_` berarti: "impor paket ini hanya untuk efek samping `init()`-nya (mendaftarkan driver bernama `mysql`)".

### DSN
```
user:password@tcp(host:port)/namadb?parseTime=true&charset=utf8mb4&loc=Local
```
| Parameter | Fungsi |
|---|---|
| `parseTime=true` | Kolom DATE/TIMESTAMP dibaca sebagai `time.Time` (tanpa ini error saat Scan) |
| `charset=utf8mb4` | Mendukung seluruh karakter Unicode termasuk emoji |
| `loc=Local` | Zona waktu mengikuti komputer |

### `sql.Open` vs `Ping`
`sql.Open` hanya **menyiapkan** objek `*sql.DB`; ia belum benar-benar terhubung. Karena itu selalu panggil `Ping` agar tahu koneksi benar.

`*sql.DB` bukan satu koneksi, tetapi **pool koneksi** yang aman dipakai banyak goroutine. Buat **satu kali** saat program mulai, bagikan ke seluruh program, dan `Close()` saat berhenti. Jangan membuka-tutup untuk setiap query.

## Langkah

### 1. Helper koneksi
Agar semua contoh memakai cara yang sama, buat file `internal/koneksi/koneksi.go`:

```go
// File: internal/koneksi/koneksi.go
// Package koneksi berisi helper agar semua contoh memakai cara koneksi yang sama.
package koneksi

import (
	"context"
	"database/sql"
	"fmt"
	"os"
	"time"

	"github.com/joho/godotenv"

	_ "github.com/go-sql-driver/mysql" // mendaftarkan driver "mysql"
)

func env(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}

// DSN membangun connection string dari environment (.env).
func DSN() string {
	_ = godotenv.Load() // abaikan error jika .env tidak ada
	return fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true&charset=utf8mb4&loc=Local",
		env("DB_USER", "root"),
		env("DB_PASS", ""),
		env("DB_HOST", "127.0.0.1"),
		env("DB_PORT", "3306"),
		env("DB_NAME", "belajar_go"),
	)
}

// Buka membuka koneksi pool dan memastikan database dapat dihubungi.
func Buka() (*sql.DB, error) {
	db, err := sql.Open("mysql", DSN())
	if err != nil {
		return nil, err
	}
	db.SetMaxOpenConns(10)
	db.SetMaxIdleConns(5)
	db.SetConnMaxLifetime(5 * time.Minute)

	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()
	if err := db.PingContext(ctx); err != nil {
		db.Close()
		return nil, fmt.Errorf("tidak bisa terhubung ke MySQL: %w", err)
	}
	return db, nil
}
```

### 2. Program pertama
Buat `contoh-kode/02-koneksi/main.go`:

```go
// File: contoh-kode/02-koneksi/main.go
package main

import (
	"fmt"
	"log"

	"golangmysql/internal/koneksi"
)

func main() {
	db, err := koneksi.Buka()
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	var versi string
	if err := db.QueryRow("SELECT VERSION()").Scan(&versi); err != nil {
		log.Fatal(err)
	}
	fmt.Println("Berhasil terhubung! Versi MySQL:", versi)
}
```

### 3. Jalankan
Pastikan MySQL menyala, lalu dari folder `03-golang-mysql`:
```powershell
go mod tidy
go run ./contoh-kode/02-koneksi
```

## Output yang Diharapkan
```
Berhasil terhubung! Versi MySQL: 8.0.xx   (atau 10.4.xx-MariaDB pada XAMPP)
```

## Kesalahan Umum
| Error | Penyebab |
|---|---|
| `connectex: No connection could be made because the target machine actively refused it` | MySQL belum menyala / port salah |
| `Error 1045: Access denied for user 'root'@'localhost'` | Password di `.env` salah |
| `Error 1049: Unknown database 'belajar_go'` | `schema.sql` belum dijalankan |
| `missing driver / unknown driver "mysql"` | Lupa `_ "github.com/go-sql-driver/mysql"` |

## Latihan
Ubah `DB_PASS` menjadi salah sengaja dan baca pesan error-nya. Kembalikan ke nilai benar.

## Catatan Instruktur
Estimasi 45 menit. Tekankan: `sql.Open` tidak konek, dan `*sql.DB` itu pool.
