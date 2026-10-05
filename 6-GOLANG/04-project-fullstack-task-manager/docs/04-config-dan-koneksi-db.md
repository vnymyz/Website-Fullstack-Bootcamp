# 04 - Config dan Koneksi Database

## Tujuan Pembelajaran
- Membaca konfigurasi dari environment variable.
- Membuka koneksi pool ke MySQL dan memastikan koneksi hidup.

## Langkah

### 1. Config
Buat file `backend/internal/config/config.go`:

```go
// File: backend/internal/config/config.go
package config

import (
	"fmt"
	"os"
	"strconv"
	"time"

	"github.com/joho/godotenv"
)

// Config menyimpan semua pengaturan aplikasi yang dibaca dari environment.
type Config struct {
	AppPort    string
	AppEnv     string
	DBHost     string
	DBPort     string
	DBUser     string
	DBPass     string
	DBName     string
	JWTSecret  string
	JWTExpire  time.Duration
	CORSOrigin string
}

// Load membaca file .env (jika ada) lalu mengisi struct Config.
func Load() (*Config, error) {
	// Abaikan error: di server produksi biasanya env diset langsung, bukan lewat file.
	_ = godotenv.Load()

	expireHours, err := strconv.Atoi(getEnv("JWT_EXPIRE_HOURS", "24"))
	if err != nil {
		return nil, fmt.Errorf("JWT_EXPIRE_HOURS harus berupa angka: %w", err)
	}

	cfg := &Config{
		AppPort:    getEnv("APP_PORT", "8080"),
		AppEnv:     getEnv("APP_ENV", "development"),
		DBHost:     getEnv("DB_HOST", "127.0.0.1"),
		DBPort:     getEnv("DB_PORT", "3306"),
		DBUser:     getEnv("DB_USER", "root"),
		DBPass:     getEnv("DB_PASS", ""),
		DBName:     getEnv("DB_NAME", "task_manager"),
		JWTSecret:  getEnv("JWT_SECRET", ""),
		JWTExpire:  time.Duration(expireHours) * time.Hour,
		CORSOrigin: getEnv("CORS_ORIGIN", "http://localhost:5173"),
	}

	if cfg.JWTSecret == "" {
		return nil, fmt.Errorf("JWT_SECRET wajib diisi")
	}
	return cfg, nil
}

// DSN membentuk Data Source Name untuk driver MySQL.
func (c *Config) DSN() string {
	return fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true&charset=utf8mb4&loc=Local",
		c.DBUser, c.DBPass, c.DBHost, c.DBPort, c.DBName)
}

func getEnv(key, fallback string) string {
	if v, ok := os.LookupEnv(key); ok && v != "" {
		return v
	}
	return fallback
}
```

**Penjelasan**
- `godotenv.Load()` membaca `.env` dan memasukkannya ke environment proses.
- `getEnv` memberi nilai default jika variabel kosong.
- `JWT_SECRET` wajib; jika kosong, program berhenti dengan error jelas.
- `parseTime=true` di DSN membuat kolom `TIMESTAMP/DATE` terbaca sebagai `time.Time`.
- Method `DSN()` menyusun connection string: `user:pass@tcp(host:port)/db?...`.

### 2. Koneksi MySQL
Buat file `backend/internal/database/mysql.go`:

```go
// File: backend/internal/database/mysql.go
package database

import (
	"context"
	"database/sql"
	"fmt"
	"time"

	_ "github.com/go-sql-driver/mysql" // registrasi driver MySQL
)

// NewMySQL membuka koneksi pool ke MySQL dan memastikan database bisa dihubungi.
func NewMySQL(dsn string) (*sql.DB, error) {
	db, err := sql.Open("mysql", dsn)
	if err != nil {
		return nil, fmt.Errorf("gagal membuka koneksi: %w", err)
	}

	// Pengaturan connection pool.
	db.SetMaxOpenConns(25)
	db.SetMaxIdleConns(10)
	db.SetConnMaxLifetime(5 * time.Minute)

	// sql.Open belum benar-benar konek, jadi Ping untuk memastikan.
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()
	if err := db.PingContext(ctx); err != nil {
		db.Close()
		return nil, fmt.Errorf("gagal ping database (apakah MySQL sudah menyala?): %w", err)
	}
	return db, nil
}
```

**Penjelasan**
- `import _ "github.com/go-sql-driver/mysql"`: tanda `_` berarti paket hanya diimpor untuk efek sampingnya (mendaftarkan driver).
- `sql.Open` **tidak** langsung konek. Maka kita panggil `PingContext`.
- `SetMaxOpenConns`, `SetMaxIdleConns`, `SetConnMaxLifetime`: pengaturan connection pool (dijelaskan di materi 3).
- `context.WithTimeout`: ping dibatalkan jika lebih dari 5 detik.

## Cek Hasil
Belum bisa dijalankan karena `main.go` belum ada. Cek compile saja:
```powershell
go build ./internal/config ./internal/database
```

## Kesalahan Umum
| Error | Penyebab |
|---|---|
| `gagal ping database` | MySQL belum menyala / port salah |
| `Access denied for user` | `DB_USER`/`DB_PASS` salah |
| `Unknown database 'task_manager'` | `schema.sql` belum dijalankan |

## Catatan Instruktur
Diskusikan: mengapa konfigurasi tidak ditulis langsung di kode (hardcode)?
